<?php
declare(strict_types=1);
require __DIR__ . '/config.php';

$user = require_login();
$action = (string)($_GET['action'] ?? '');

function users_list(): array {
  return pdo()->query("SELECT id, username, display_name FROM users ORDER BY id ASC")->fetchAll();
}

function get_user_by_id(int $id): ?array {
  $stm = pdo()->prepare("SELECT id, username, display_name FROM users WHERE id=:id LIMIT 1");
  $stm->execute([':id'=>$id]);
  $r = $stm->fetch();
  return $r ?: null;
}

function list_tx(int $userId, string $type, ?int $settlementId): array {
  if ($settlementId === null) {
    $stm = pdo()->prepare("SELECT id, type, amount, note, tx_date, created_at, updated_at, settlement_id
                           FROM transactions
                           WHERE user_id = :uid AND type = :t AND settlement_id IS NULL
                           ORDER BY tx_date DESC, id DESC");
    $stm->execute([':uid'=>$userId, ':t'=>$type]);
  } else {
    $stm = pdo()->prepare("SELECT id, type, amount, note, tx_date, created_at, updated_at, settlement_id
                           FROM transactions
                           WHERE user_id = :uid AND type = :t AND settlement_id = :sid
                           ORDER BY tx_date DESC, id DESC");
    $stm->execute([':uid'=>$userId, ':t'=>$type, ':sid'=>$settlementId]);
  }
  return $stm->fetchAll();
}

function sum_tx(int $userId, string $type, ?int $settlementId): float {
  if ($settlementId === null) {
    $stm = pdo()->prepare("SELECT COALESCE(SUM(amount),0) FROM transactions
                           WHERE user_id=:uid AND type=:t AND settlement_id IS NULL");
    $stm->execute([':uid'=>$userId, ':t'=>$type]);
  } else {
    $stm = pdo()->prepare("SELECT COALESCE(SUM(amount),0) FROM transactions
                           WHERE user_id=:uid AND type=:t AND settlement_id=:sid");
    $stm->execute([':uid'=>$userId, ':t'=>$type, ':sid'=>$settlementId]);
  }
  return (float)$stm->fetchColumn();
}

function settlement_calc(?int $settlementId): array {
  $users = users_list();
  $n = count($users);
  if ($n < 2) return ['ok'=>false,'error'=>'Need at least 2 users'];

  $rows = [];
  $totalReceipts = 0.0;
  $totalExpenses = 0.0;

  foreach ($users as $u) {
    $uid = (int)$u['id'];
    $r = sum_tx($uid, 'receipt', $settlementId);
    $e = sum_tx($uid, 'expense', $settlementId);
    $net = $r - $e;

    $rows[] = [
      'id'=>$uid,
      'username'=>$u['username'],
      'display_name'=>$u['display_name'],
      'receipts'=>$r,
      'expenses'=>$e,
      'net'=>$net,
    ];

    $totalReceipts += $r;
    $totalExpenses += $e;
  }

  $shareReceipts = $totalReceipts / $n;
  $shareExpenses = $totalExpenses / $n;
  $fairNetEach   = $shareReceipts - $shareExpenses;

  $debtors = [];
  $creditors = [];

  foreach ($rows as $x) {
    $pay = ($x['net'] - $fairNetEach); // + يدفع، - يستلم
    if ($pay > 0.005) $debtors[] = ['id'=>$x['id'], 'name'=>($x['display_name'] ?: $x['username']), 'amt'=>$pay];
    if ($pay < -0.005) $creditors[] = ['id'=>$x['id'], 'name'=>($x['display_name'] ?: $x['username']), 'amt'=>abs($pay)];
  }

  $transfers = [];
  $i=0; $j=0;
  while ($i < count($debtors) && $j < count($creditors)) {
    $d = &$debtors[$i];
    $c = &$creditors[$j];
    $m = min($d['amt'], $c['amt']);

    $transfers[] = [
      'from_id'=>$d['id'], 'from'=>$d['name'],
      'to_id'=>$c['id'],   'to'=>$c['name'],
      'amount'=>$m
    ];

    $d['amt'] -= $m;
    $c['amt'] -= $m;
    if ($d['amt'] <= 0.005) $i++;
    if ($c['amt'] <= 0.005) $j++;
  }

  return [
    'ok'=>true,
    'users'=>$rows,
    'totals'=>[
      'n'=>$n,
      'receipts'=>$totalReceipts,
      'expenses'=>$totalExpenses,
      'share_receipts'=>$shareReceipts,
      'share_expenses'=>$shareExpenses,
      'fair_net_each'=>$fairNetEach
    ],
    'transfers'=>$transfers
  ];
}

function settlements_list(): array {
  return pdo()->query("
    SELECT s.id, s.name, s.created_at,
           u.display_name AS created_by_name, u.username AS created_by_username
    FROM settlements s
    JOIN users u ON u.id = s.created_by
    ORDER BY s.id DESC
  ")->fetchAll();
}

function settlement_details(int $sid): array {
  $stm = pdo()->prepare("
    SELECT s.id, s.name, s.created_at,
           u.id AS created_by_id, u.username AS created_by_username, u.display_name AS created_by_name
    FROM settlements s
    JOIN users u ON u.id = s.created_by
    WHERE s.id=:id LIMIT 1
  ");
  $stm->execute([':id'=>$sid]);
  $s = $stm->fetch();
  if (!$s) return ['ok'=>false,'error'=>'Settlement not found'];

  $users = users_list();
  $byUser = [];
  foreach ($users as $u) {
    $uid = (int)$u['id'];
    $byUser[$uid] = [
      'user'=>$u,
      'receipt'=>list_tx($uid, 'receipt', $sid),
      'expense'=>list_tx($uid, 'expense', $sid),
    ];
  }

  $calc = settlement_calc($sid);
  return ['ok'=>true,'settlement'=>$s,'archive'=>$byUser,'calc'=>$calc];
}

try {
  if ($action === 'me') {
    $me = get_user_by_id((int)$user['id']);
    json_out(['ok'=>true,'user'=>$me,'csrf'=>csrf_token(),'users'=>users_list()]);
  }

  if ($action === 'users') {
    json_out(['ok'=>true,'users'=>users_list()]);
  }

  if ($action === 'list') {
    $type = normalize_type((string)($_GET['type'] ?? 'receipt'));
    if (!in_array($type, ['receipt','expense'], true)) json_out(['ok'=>false,'error'=>'Invalid type']);

    $targetId = (int)($_GET['user_id'] ?? (int)$user['id']);
    $u = get_user_by_id($targetId);
    if (!$u) json_out(['ok'=>false,'error'=>'User not found']);

    $sidRaw = (string)($_GET['settlement_id'] ?? 'active');
    $sid = null;
    if ($sidRaw !== 'active') $sid = (int)$sidRaw;

    json_out(['ok'=>true,'user'=>$u,'type'=>$type,'settlement_id'=>$sidRaw,'items'=>list_tx($targetId,$type,$sid)]);
  }

  if ($action === 'add') {
    csrf_verify();

    $type = normalize_type((string)($_POST['type'] ?? ''));
    $amount = (float)($_POST['amount'] ?? 0);
    $note = trim((string)($_POST['note'] ?? ''));
    $tx_date = trim((string)($_POST['tx_date'] ?? ''));

    if (!in_array($type, ['receipt','expense'], true)) throw new Exception('نوع غير صحيح');
    if ($amount <= 0) throw new Exception('المبلغ لازم يكون أكبر من 0');
    if (!preg_match('~^\d{4}-\d{2}-\d{2}$~', $tx_date)) throw new Exception('صيغة التاريخ YYYY-MM-DD');

    $stm = pdo()->prepare("INSERT INTO transactions(user_id,type,amount,note,tx_date) VALUES(:uid,:t,:a,:n,:d)");
    $stm->execute([':uid'=>(int)$user['id'], ':t'=>$type, ':a'=>$amount, ':n'=>$note, ':d'=>$tx_date]);

    json_out(['ok'=>true,'id'=>(int)pdo()->lastInsertId()]);
  }

  if ($action === 'update') {
    csrf_verify();

    $id = (int)($_POST['id'] ?? 0);
    $amount = (float)($_POST['amount'] ?? 0);
    $note = trim((string)($_POST['note'] ?? ''));
    $tx_date = trim((string)($_POST['tx_date'] ?? ''));

    if ($id <= 0) throw new Exception('ID غير صحيح');
    if ($amount <= 0) throw new Exception('المبلغ لازم يكون أكبر من 0');
    if (!preg_match('~^\d{4}-\d{2}-\d{2}$~', $tx_date)) throw new Exception('صيغة التاريخ YYYY-MM-DD');

    $stm = pdo()->prepare("UPDATE transactions
                           SET amount=:a, note=:n, tx_date=:d, updated_at=datetime('now')
                           WHERE id=:id AND user_id=:uid AND settlement_id IS NULL");
    $stm->execute([':a'=>$amount, ':n'=>$note, ':d'=>$tx_date, ':id'=>$id, ':uid'=>(int)$user['id']]);

    if ($stm->rowCount() === 0) throw new Exception('السجل مؤرشف أو لا تملك صلاحية تعديله');
    json_out(['ok'=>true]);
  }

  if ($action === 'delete') {
    csrf_verify();

    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) throw new Exception('ID غير صحيح');

    $stm = pdo()->prepare("DELETE FROM transactions WHERE id=:id AND user_id=:uid AND settlement_id IS NULL");
    $stm->execute([':id'=>$id, ':uid'=>(int)$user['id']]);

    if ($stm->rowCount() === 0) throw new Exception('السجل مؤرشف أو لا تملك صلاحية حذفه');
    json_out(['ok'=>true]);
  }

  if ($action === 'settlement') {
    json_out(settlement_calc(null));
  }

  if ($action === 'settlements') {
    json_out(['ok'=>true,'items'=>settlements_list()]);
  }

  if ($action === 'settlement_details') {
    $sid = (int)($_GET['id'] ?? 0);
    if ($sid <= 0) json_out(['ok'=>false,'error'=>'Invalid id']);
    json_out(settlement_details($sid));
  }

  if ($action === 'create_settlement') {
    csrf_verify();
    $name = trim((string)($_POST['name'] ?? ''));
    if ($name === '') throw new Exception('اكتب اسم التسوية');

    $db = pdo();
    $db->beginTransaction();

    $ins = $db->prepare("INSERT INTO settlements(name, created_by) VALUES(:n,:by)");
    $ins->execute([':n'=>$name, ':by'=>(int)$user['id']]);
    $sid = (int)$db->lastInsertId();

    $up = $db->prepare("UPDATE transactions SET settlement_id=:sid WHERE settlement_id IS NULL");
    $up->execute([':sid'=>$sid]);

    $db->commit();
    json_out(['ok'=>true,'settlement_id'=>$sid]);
  }

  json_out(['ok'=>false,'error'=>'Unknown action']);
} catch (Throwable $e) {
  if (pdo()->inTransaction()) pdo()->rollBack();
  http_response_code(400);
  json_out(['ok'=>false,'error'=>$e->getMessage()]);
}
