<?php
declare(strict_types=1);
require __DIR__ . '/config.php';

start_session();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim((string)($_POST['username'] ?? ''));
  $password = (string)($_POST['password'] ?? '');

  if ($username === '' || $password === '') {
    $error = 'الرجاء إدخال اسم المستخدم وكلمة المرور.';
  } else {
    $stm = pdo()->prepare("SELECT id, username, display_name, password_hash FROM users WHERE username = :u LIMIT 1");
    $stm->execute([':u' => $username]);
    $u = $stm->fetch();

    if ($u && password_verify($password, (string)$u['password_hash'])) {
      unset($u['password_hash']);
      $_SESSION['user'] = $u;
      header('Location: dashboard.php');
      exit;
    }
    $error = 'بيانات الدخول غير صحيحة.';
  }
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>تسجيل الدخول</title>
<style>
  body{font-family:system-ui,Tahoma,Arial;background:#f6f7fb;margin:0;padding:18px}
  .card{max-width:420px;margin:40px auto;background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:16px}
  .h{font-weight:900;margin:0 0 12px}
  input{width:100%;padding:12px;border:1px solid #e5e7eb;border-radius:12px;margin-bottom:10px;font-size:14px}
  button{width:100%;padding:12px;border:0;border-radius:12px;background:#2563eb;color:#fff;font-weight:900;cursor:pointer}
  .err{background:#fef2f2;color:#991b1b;border:1px solid rgba(220,38,38,.30);padding:10px;border-radius:12px;margin-bottom:10px}
  .muted{color:#6b7280;font-size:12px}
  .num{direction:ltr;unicode-bidi:plaintext;text-align:left}
</style>
</head>
<body>
  <div class="card">
    <h2 class="h">تسجيل الدخول</h2>
    <?php if($error): ?><div class="err"><?= e($error) ?></div><?php endif; ?>
    <form method="post" autocomplete="on">
      <input name="username" autocomplete="username" placeholder="اسم المستخدم" value="<?= e($_POST['username'] ?? '') ?>">
      <input name="password" type="password" autocomplete="current-password" placeholder="كلمة المرور">
      <button>دخول</button>
    </form>
    <div class="muted" style="margin-top:10px">Accounting Partner Settlement System</div>
  </div>
</body>
</html>
