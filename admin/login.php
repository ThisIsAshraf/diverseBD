<?php 
require_once dirname(__DIR__) . '/app/bootstrap.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown Browser';

    $s = db()->prepare('SELECT * FROM admins WHERE email = ?');
    $s->execute([$email]);
    $admin = $s->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_id'] = (int)$admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_email'] = $admin['email'];

        // 1. Log Successful Login Audit
        $log = db()->prepare('INSERT INTO admin_login_logs (admin_id, email_attempted, ip_address, user_agent, status) VALUES (?, ?, ?, ?, ?)');
        $log->execute([$admin['id'], $email, $ip, $userAgent, 'success']);

        // 2. Enforce Max Session Limit (Defaults to 2 concurrent sessions)
        $maxSessions = (int)site_setting('max_admin_sessions', '2');
        
        $sessionStmt = db()->prepare('SELECT id FROM admin_sessions WHERE admin_id = ? ORDER BY last_activity DESC');
        $sessionStmt->execute([$admin['id']]);
        $existingSessions = $sessionStmt->fetchAll(PDO::FETCH_COLUMN);

        // Prune oldest session(s) if threshold is met and current session isn't already active
        while (count($existingSessions) >= $maxSessions && !in_array(session_id(), $existingSessions, true)) {
            $oldestSessionId = array_pop($existingSessions);
            db()->prepare('DELETE FROM admin_sessions WHERE id = ?')->execute([$oldestSessionId]);
        }

        // Register / Touch Active Session
        $sessInsert = db()->prepare('INSERT INTO admin_sessions (id, admin_id, ip_address, user_agent) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE ip_address = VALUES(ip_address), user_agent = VALUES(user_agent), last_activity = NOW()');
        $sessInsert->execute([session_id(), $admin['id'], $ip, $userAgent]);

        flash('success', 'Welcome back, ' . $admin['name']);
        redirect('admin/index.php');
    } else {
        // Log Failed Attempt Audit
        $log = db()->prepare('INSERT INTO admin_login_logs (admin_id, email_attempted, ip_address, user_agent, status) VALUES (?, ?, ?, ?, ?)');
        $log->execute([$admin['id'] ?? null, $email, $ip, $userAgent, 'failed']);

        $error = 'Invalid email address or password.';
    }
}

$pageTitle = 'Admin Sign In';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> · <?= e(store_name()) ?></title>
    <link rel="stylesheet" href="<?= e(url('assets/style.css')) ?>">
</head>
<body style="display:flex; justify-content:center; align-items:center; min-height:100vh; background:#f4f6f8;">
    <div class="panel" style="width:100%; max-width:400px; padding:30px; border-radius:8px;">
        <h2 style="margin-top:0; text-align:center;"><?= e(store_name()) ?> Admin</h2>
        <?php if ($error): ?>
            <div class="notice error"><?= e($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <?= csrf_field() ?>
            <div class="field">
                <label>Email Address</label>
                <input type="email" name="email" required autofocus value="<?= e($_POST['email'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button class="button" style="width:100%; margin-top:10px;">Sign In</button>
        </form>
    </div>
</body>
</html>