<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Riyadh');
error_reporting(E_ALL);
ini_set('display_errors', '0');

define('APP_NAME', 'AirLinkUp Accounting');
define('DB_SQLITE_PATH', __DIR__ . '/data/accounting.sqlite');

define('APP_COOKIE_DOMAIN', 'your_domain'); // عدّلها إذا اختلف الدومين
define('APP_COOKIE_SECURE', true);
define('APP_COOKIE_SAMESITE', 'Lax');
define('APP_SESSION_LIFETIME', 0);

function start_session(): void {
  if (session_status() === PHP_SESSION_ACTIVE) return;

  session_set_cookie_params([
    'lifetime' => APP_SESSION_LIFETIME,
    'path'     => '/',
    'domain'   => APP_COOKIE_DOMAIN,
    'secure'   => APP_COOKIE_SECURE,
    'httponly' => true,
    'samesite' => APP_COOKIE_SAMESITE,
  ]);

  session_start();

  if (empty($_SESSION['_regen'])) {
    session_regenerate_id(true);
    $_SESSION['_regen'] = time();
  }
}

function pdo(): PDO {
  static $pdo = null;
  if ($pdo instanceof PDO) return $pdo;

  $dir = dirname(DB_SQLITE_PATH);
  if (!is_dir($dir)) @mkdir($dir, 0755, true);

  $pdo = new PDO('sqlite:' . DB_SQLITE_PATH, null, null, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);

  $pdo->exec("PRAGMA foreign_keys = ON;");
  $pdo->exec("PRAGMA journal_mode = WAL;");
  $pdo->exec("PRAGMA synchronous = NORMAL;");

  bootstrap_schema($pdo);

  return $pdo;
}

function bootstrap_schema(PDO $db): void {
  $db->exec("
    CREATE TABLE IF NOT EXISTS users (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      username TEXT NOT NULL UNIQUE,
      display_name TEXT NOT NULL DEFAULT '',
      password_hash TEXT NOT NULL,
      created_at TEXT NOT NULL DEFAULT (datetime('now'))
    );
  ");

  $db->exec("
    CREATE TABLE IF NOT EXISTS settlements (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      name TEXT NOT NULL,
      created_by INTEGER NOT NULL,
      created_at TEXT NOT NULL DEFAULT (datetime('now')),
      FOREIGN KEY(created_by) REFERENCES users(id)
    );
  ");

  $db->exec("
    CREATE TABLE IF NOT EXISTS transactions (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      user_id INTEGER NOT NULL,
      type TEXT NOT NULL CHECK(type IN ('receipt','expense')),
      amount REAL NOT NULL DEFAULT 0,
      tx_date TEXT NOT NULL,
      note TEXT NOT NULL DEFAULT '',
      settlement_id INTEGER NULL,
      created_at TEXT NOT NULL DEFAULT (datetime('now')),
      updated_at TEXT NULL,
      FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
      FOREIGN KEY(settlement_id) REFERENCES settlements(id) ON DELETE SET NULL
    );
  ");

  $db->exec("CREATE INDEX IF NOT EXISTS idx_tx_settlement ON transactions(settlement_id);");
  $db->exec("CREATE INDEX IF NOT EXISTS idx_tx_user_type ON transactions(user_id, type);");
}

function e(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function json_out(array $data): void {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}

function require_login(): array {
  start_session();
  if (empty($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
  }
  return $_SESSION['user'];
}

function csrf_token(): string {
  start_session();
  if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
  return (string)$_SESSION['csrf'];
}

function csrf_verify(): void {
  start_session();
  $sess = (string)($_SESSION['csrf'] ?? '');
  $post = (string)($_POST['csrf'] ?? '');
  $hdr  = (string)($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
  $token = $post ?: $hdr;

  if (!$sess || !$token || !hash_equals($sess, $token)) {
    http_response_code(403);
    json_out(['ok'=>false,'error'=>'CSRF token invalid']);
  }
}

function normalize_type(string $t): string {
  $t = strtolower(trim($t));
  return ($t === 'expense') ? 'expense' : 'receipt';
}
