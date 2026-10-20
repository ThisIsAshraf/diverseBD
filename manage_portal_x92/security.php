<?php require_once __DIR__ . '/_init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = (string)($_POST['action'] ?? '');

    // Save Max Session Limit
    if ($action === 'save_limits') {
        $maxSessions = max(1, (int)($_POST['max_admin_sessions'] ?? 2));
        site_setting_save('max_admin_sessions', (string)$maxSessions);
        flash('success', 'Maximum concurrent session limit updated to ' . $maxSessions . '.');
        redirect('manage_portal_x92/security.php');
    }

    // Revoke Remote Session
    if ($action === 'revoke_session') {
        $sessId = (string)($_POST['session_id'] ?? '');
        if ($sessId !== session_id()) {
            db()->prepare('DELETE FROM admin_sessions WHERE id = ?')->execute([$sessId]);
            flash('success', 'Remote session revoked successfully.');
        }
        redirect('manage_portal_x92/security.php');
    }
}

// Current Session Limit Setting (Defaults to 2)
$maxSessionsLimit = (int)site_setting('max_admin_sessions', '2');

// Fetch Active Sessions & Login Audit Logs
$activeSessions = db()->query('
    SELECT s.*, a.name admin_name, a.email admin_email
    FROM admin_sessions s
    JOIN admins a ON a.id = s.admin_id
    ORDER BY s.last_activity DESC
')->fetchAll();

$loginLogs = db()->query('
    SELECT l.*, a.name admin_name
    FROM admin_login_logs l
    LEFT JOIN admins a ON a.id = l.admin_id
    ORDER BY l.id DESC LIMIT 100
')->fetchAll();

admin_header('Admin Security & Session Control'); ?>

<!-- Concurrent Session Limit Card -->
<div class="panel" style="margin-bottom:25px; max-width: 600px;">
    <h2>Concurrent Login Limits</h2>
    <p class="muted small">Control how many active devices can be logged into an admin account simultaneously.</p>
    <form method="post" style="display:flex; gap:12px; align-items:flex-end;">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save_limits">
        <div class="field" style="margin:0; flex:1;">
            <label>Max Concurrent Admin Sessions</label>
            <input type="number" min="1" max="10" name="max_admin_sessions" value="<?= e((string)$maxSessionsLimit) ?>" required>
        </div>
        <button class="button">Save Limit</button>
    </form>
</div>

<!-- Active Sessions Table -->
<div class="panel" style="margin-bottom:25px;">
    <h2>Active Admin Sessions (<?= count($activeSessions) ?> / <?= $maxSessionsLimit ?> Active)</h2>
    <p class="muted small">All active devices currently authorized in the admin portal.</p>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Admin</th>
                    <th>IP Address</th>
                    <th>Browser / Device</th>
                    <th>Last Active</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activeSessions as $s):
                    $isCurrent = ($s['id'] === session_id());
                ?>
                    <tr>
                        <td><b><?= e($s['admin_name']) ?></b><br><span class="muted small"><?= e($s['admin_email']) ?></span></td>
                        <td><code><?= e($s['ip_address']) ?></code></td>
                        <td style="max-width:280px; font-size:12px; color:#555; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;" title="<?= e($s['user_agent']) ?>">
                            <?= e($s['user_agent']) ?>
                        </td>
                        <td><?= date('M d, Y g:i a', strtotime($s['last_activity'])) ?></td>
                        <td>
                            <?php if ($isCurrent): ?>
                                <span class="status" style="padding:2px 8px; background:#e8f8f5; color:#27ae60; font-weight:bold;">Current Device</span>
                            <?php else: ?>
                                <span class="status" style="padding:2px 8px; background:#f4f6f8; color:#777;">Remote Device</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!$isCurrent): ?>
                                <form method="post" style="margin:0;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="revoke_session">
                                    <input type="hidden" name="session_id" value="<?= e($s['id']) ?>">
                                    <button class="button secondary" style="padding:4px 8px; font-size:12px; background:#fde8e8; color:#e74c3c;">Revoke</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$activeSessions): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:20px;">No active sessions.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Login History Audit Log -->
<div class="panel">
    <h2>Login History Audit Log</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Attempted Email</th>
                    <th>IP Address</th>
                    <th>Browser Signature</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($loginLogs as $l): ?>
                    <tr>
                        <td><?= date('M d, Y g:i a', strtotime($l['created_at'])) ?></td>
                        <td><b><?= e($l['email_attempted']) ?></b></td>
                        <td><code><?= e($l['ip_address']) ?></code></td>
                        <td style="max-width:300px; font-size:12px; color:#555; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;" title="<?= e($l['user_agent']) ?>">
                            <?= e($l['user_agent']) ?>
                        </td>
                        <td>
                            <span class="status" style="padding:2px 8px; font-weight:bold; font-size:12px; background: <?= $l['status'] === 'success' ? '#e8f8f5; color:#27ae60;' : '#fde8e8; color:#e74c3c;' ?>">
                                <?= strtoupper(e($l['status'])) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php admin_footer(); ?>