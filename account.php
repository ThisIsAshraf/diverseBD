<?php
require_once __DIR__ . '/app/bootstrap.php';

// Requires customer to be logged in
$u = user_required();

$errors = [];

// Handle Profile & Address Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    verify_csrf();
    $name    = trim((string)($_POST['name'] ?? ''));
    $phone   = trim((string)($_POST['phone'] ?? ''));
    $address = trim((string)($_POST['address'] ?? ''));
    $city    = trim((string)($_POST['city'] ?? ''));

    if (mb_strlen($name) < 2) $errors[] = 'Full name is required.';

    if (!$errors) {
        $st = db()->prepare('UPDATE users SET name=?, phone=?, address=?, city=? WHERE id=?');
        $st->execute([$name, $phone, $address, $city, $u['id']]);
        flash('success', 'Your account details and saved address have been updated.');
        redirect('account.php');
    }
}

// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    verify_csrf();
    $currentPass = (string)($_POST['current_password'] ?? '');
    $newPass     = (string)($_POST['new_password'] ?? '');

    if (!password_verify($currentPass, $u['password_hash'])) {
        $errors[] = 'Your current password was incorrect.';
    } elseif (strlen($newPass) < 6) {
        $errors[] = 'New password must be at least 6 characters.';
    }

    if (!$errors) {
        $hash = password_hash($newPass, PASSWORD_DEFAULT);
        db()->prepare('UPDATE users SET password_hash=? WHERE id=?')->execute([$hash, $u['id']]);
        flash('success', 'Your password has been changed successfully.');
        redirect('account.php');
    }
}

// Fetch Customer's Past Orders
$ordersStmt = db()->prepare('SELECT * FROM orders WHERE user_id=? OR customer_email=? ORDER BY id DESC');
$ordersStmt->execute([$u['id'], $u['email']]);
$userOrders = $ordersStmt->fetchAll();

$pageTitle = 'My Account';
require_once __DIR__ . '/app/header.php';
?>

<div style="max-width: 900px; margin: 30px auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
        <h1 style="margin:0;">My Account</h1>
        <a href="<?= e(url('logout.php')) ?>" class="button secondary" style="padding:8px 16px;">Log Out</a>
    </div>

    <?php if ($errors): ?>
        <div class="notice error">
            <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 25px;">

        <!-- Saved Delivery Profile -->
        <div class="panel">
            <h2>Saved Shipping Details</h2>
            <p class="muted small" style="margin-bottom:15px;">These details automatically pre-fill your checkout page.</p>
            <form method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="update_profile" value="1">
                <div class="field"><label>Full Name *</label><input name="name" required value="<?= e($u['name']) ?>"></div>
                <div class="field"><label>Email Address</label><input value="<?= e($u['email']) ?>" disabled style="background:#f4f4f4;"></div>
                <div class="field"><label>Phone Number</label><input name="phone" value="<?= e($u['phone'] ?? '') ?>"></div>
                <div class="field"><label>Delivery Address</label><textarea name="address"><?= e($u['address'] ?? '') ?></textarea></div>
                <div class="field"><label>City / Area</label><input name="city" value="<?= e($u['city'] ?? '') ?>"></div>
                <button class="button" style="width:100%;">Save Address Info</button>
            </form>

            <hr style="margin:25px 0;">

            <h3>Security & Password</h3>
            <form method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="change_password" value="1">
                <div class="field"><label>Current Password</label><input type="password" name="current_password" required></div>
                <div class="field"><label>New Password (min 6 chars)</label><input type="password" name="new_password" required></div>
                <button class="button secondary" style="width:100%;">Update Password</button>
            </form>
        </div>

        <!-- Order History -->
        <div class="panel">
            <h2>Order History</h2>
            <?php if (!$userOrders): ?>
                <p class="muted">You haven't placed any orders yet.</p>
                <a href="<?= e(url('index.php')) ?>" class="button secondary">Start Shopping</a>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($userOrders as $o): ?>
                                <tr>
                                    <td><b><?= e($o['order_number']) ?></b></td>
                                    <td><b><?= money($o['total']) ?></b></td>
                                    <td>
                                        <span class="status" style="padding:2px 6px; font-size:11px; font-weight:bold; border-radius:4px; background:<?= in_array($o['status'], ['delivered', 'confirmed']) ? '#e8f8f5;color:#27ae60;' : ($o['status'] === 'cancelled' ? '#fde8e8;color:#e74c3c;' : '#fef9e7;color:#f39c12;') ?>">
                                            <?= ucfirst(e($o['status'])) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/app/footer.php'; ?>