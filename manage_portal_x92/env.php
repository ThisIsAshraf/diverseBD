<?php
require_once __DIR__ . '/_init.php';
admin_required();
require_permission('env'); // Enforces access control check

// Restricted to Super Admins only
admin_required();
if (!is_super_admin()) {
    flash('error', 'Access denied: Only Super Administrators can manage environment settings.');
    redirect('manage_portal_x92/index.php');
}

$title = '.ENV Configuration Manager';
$envPath = dirname(__DIR__) . '/.env';
$backupPath = dirname(__DIR__) . '/.env.backup';

// Helper to parse .env into an associative array (Filtering out any _KEY duplicates)
function parse_env_file(string $filePath): array
{
    if (!is_file($filePath)) return [];
    $vars = [];
    foreach (file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $key = strtoupper(trim($key));
        $value = trim($value);

        // Ignore any keys that end in _KEY
        if (str_ends_with($key, '_KEY')) continue;

        if (strlen($value) >= 2 && (($value[0] === '"' && str_ends_with($value, '"')) || ($value[0] === "'" && str_ends_with($value, "'")))) {
            $value = substr($value, 1, -1);
        }
        $vars[$key] = $value;
    }
    return $vars;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'save_env') {
        $rawEnv = $_POST['env'] ?? [];
        $envVars = [];

        foreach ($rawEnv as $k => $v) {
            $k = strtoupper(trim((string)$k));

            // Skip any array keys ending in _KEY
            if ($k === '' || str_ends_with($k, '_KEY')) continue;

            $envVars[$k] = trim((string)$v);
        }

        // Build new clean .env file content
        $lines = ["# Generated via Admin Panel on " . date('Y-m-d H:i:s')];
        foreach ($envVars as $k => $v) {
            // Quote value if it contains spaces
            if (str_contains($v, ' ')) {
                $v = '"' . $v . '"';
            }
            $lines[] = "{$k}={$v}";

            // Sync key settings directly into site_settings DB table
            site_setting_save(strtolower($k), $v);
        }

        $newEnvContent = implode("\n", $lines) . "\n";

        // Create backup of current .env before overwriting
        if (is_file($envPath)) {
            copy($envPath, $backupPath);
        }

        if (file_put_contents($envPath, $newEnvContent) !== false) {
            flash('success', '.env file updated and cleaned successfully!');
        } else {
            flash('error', 'Failed to save .env file. Please check server file write permissions.');
        }

        redirect('manage_portal_x92/env.php');
    }

    if ($action === 'restore_backup') {
        if (is_file($backupPath)) {
            copy($backupPath, $envPath);
            flash('success', '.env restored from backup.');
        } else {
            flash('error', 'No backup file found.');
        }
        redirect('manage_portal_x92/env.php');
    }
}

$currentVars = parse_env_file($envPath);
admin_header($title);
?>

<div class="panel" style="max-width:850px; margin:0 auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
        <div>
            <h2>Environment Variable Settings</h2>
            <p class="muted small" style="margin:4px 0 0 0;">
                Parsed from <code>.env</code> file. Changes saved here will update <code>.env</code> and sync to database settings.
            </p>
        </div>

        <?php if (is_file($backupPath)): ?>
            <form method="post" onsubmit="return confirm('Restore from previous backup?');" style="margin:0;">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="restore_backup">
                <button type="submit" class="button secondary" style="padding:6px 12px; font-size:12px;">🔄 Restore Backup</button>
            </form>
        <?php endif; ?>
    </div>

    <form method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save_env">

        <div id="envFieldsContainer">
            <?php foreach ($currentVars as $key => $val): ?>
                <div class="form-grid env-row" style="margin-bottom:12px; align-items:center;">
                    <div class="field" style="margin:0;">
                        <label>VARIABLE NAME</label>
                        <input value="<?= e($key) ?>" readonly style="background:#f4f4f4; font-weight:bold;">
                    </div>
                    <div class="field" style="margin:0;">
                        <label>VALUE</label>
                        <input type="<?= str_contains(strtolower($key), 'pass') || str_contains(strtolower($key), 'secret') ? 'password' : 'text' ?>" name="env[<?= e($key) ?>]" value="<?= e($val) ?>" placeholder="Value...">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="margin-top:25px; display:flex; gap:12px;">
            <button class="button">Save Settings & Clean .env</button>
        </div>
    </form>
</div>

<?php admin_footer(); ?>