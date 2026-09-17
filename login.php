<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';

if (current_user() !== null) {
    header('Location: index.php');
    exit;
}

$username = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน';
    } else {
        $stmt = $pdo->prepare(
            'SELECT id, username, password_hash, display_name
             FROM users
             WHERE username = :username'
        );
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, (string) $user['password_hash'])) {
            $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
        } else {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id' => (int) $user['id'],
                'username' => (string) $user['username'],
                'display_name' => (string) $user['display_name'],
            ];

            $target = $_SESSION['redirect_after_login'] ?? 'index.php';
            unset($_SESSION['redirect_after_login']);

            if (!is_string($target) || !preg_match('/^[a-z0-9_\-\.\/\?=&]+$/i', $target)) {
                $target = 'index.php';
            }

            header('Location: ' . $target);
            exit;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>เข้าสู่ระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/app.css" rel="stylesheet">
</head>
<body class="login-page">
    <main>
        <div class="login-card">
            <div class="login-brand">แจ้งซ่อม</div>
            <h1>เข้าสู่ระบบ</h1>

            <?php if ($error !== ''): ?>
                <div class="banner banner-error" role="alert"><?= h($error) ?></div>
            <?php endif; ?>

            <form method="post" novalidate>
                <div class="mb-3">
                    <label for="username" class="form-label">ชื่อผู้ใช้</label>
                    <input
                        type="text"
                        class="form-control"
                        id="username"
                        name="username"
                        autocomplete="username"
                        autofocus
                        value="<?= h($username) ?>"
                    >
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">รหัสผ่าน</label>
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                    >
                </div>
                <button type="submit" class="btn btn-primary">เข้าสู่ระบบ</button>
            </form>
            <p class="login-hint">บัญชีทดสอบ admin / Admin123!</p>
        </div>
    </main>
</body>
</html>
