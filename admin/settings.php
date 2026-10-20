<?php
require_once __DIR__ . '/_init.php';
$title = 'Store Branding, Mailer & Invoice Configuration';
$uploadDir = dirname(__DIR__) . '/storage/uploads';
$uploadUrl = 'storage/uploads';

if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = (string)($_POST['action'] ?? 'save_settings');

    if ($action === 'test_email') {
        $testEmail = trim((string)($_POST['test_email_address'] ?? ''));
        if (filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            $sent = send_store_email(
                $testEmail,
                'Admin Test User',
                'SMTP Configuration Test - ' . store_name(),
                '<h3>SMTP Setup Working!</h3><p>This is a test email sent from your ' . e(store_name()) . ' admin panel.</p>'
            );
            if ($sent) {
                flash('success', 'Test email sent successfully to ' . $testEmail);
            } else {
                flash('error', 'Failed to send test email. Please check your SMTP settings and password.');
            }
        } else {
            flash('error', 'Please enter a valid recipient email address.');
        }
        redirect('admin/settings.php');
    }

    if ($action === 'save_settings') {
        $name = trim((string)($_POST['site_name'] ?? ''));
        $announcement = trim((string)($_POST['announcement'] ?? ''));
        $adminEmail = trim((string)($_POST['admin_notification_email'] ?? ''));
        $currency = trim((string)($_POST['currency_symbol'] ?? '৳'));

        // SMTP Settings 
        $mailHost = trim((string)($_POST['mail_host'] ?? 'smtp.gmail.com'));
        $mailPort = trim((string)($_POST['mail_port'] ?? '587'));
        $mailEnc  = trim((string)($_POST['mail_encryption'] ?? 'tls'));
        $mailUser = trim((string)($_POST['mail_username'] ?? ''));
        $mailPass = trim((string)($_POST['mail_password'] ?? ''));
        $fromAddr = trim((string)($_POST['mail_from_address'] ?? ''));
        $fromName = trim((string)($_POST['mail_from_name'] ?? ''));

        // Invoice Customizations
        $invoiceColor = trim((string)($_POST['invoice_color'] ?? '#2c3e50'));
        $invoiceNote  = trim((string)($_POST['invoice_footer_note'] ?? 'Thank you for shopping with us!'));

        // Store Guarantee / Trust Badges Customization
        $trustKeys = [
            'trust_badge_1_title',
            'trust_badge_1_sub',
            'trust_badge_2_title',
            'trust_badge_2_sub',
            'trust_badge_3_title',
            'trust_badge_3_sub',
            'trust_badge_4_title',
            'trust_badge_4_sub'
        ];
        foreach ($trustKeys as $tk) {
            if (isset($_POST[$tk])) {
                site_setting_save($tk, trim((string)$_POST[$tk]));
            }
        }

        if ($name === '') {
            flash('error', 'Please enter a valid store name.');
            redirect('admin/settings.php');
        }

        // Save Branding 
        site_setting_save('site_name', $name);
        site_setting_save('announcement', $announcement);
        site_setting_save('admin_notification_email', $adminEmail);
        site_setting_save('currency_symbol', $currency);

        // Save SMTP Credentials 
        site_setting_save('mail_host', $mailHost);
        site_setting_save('mail_port', $mailPort);
        site_setting_save('mail_encryption', $mailEnc);
        site_setting_save('mail_username', $mailUser);
        site_setting_save('mail_password', $mailPass);
        site_setting_save('mail_from_address', $fromAddr);
        site_setting_save('mail_from_name', $fromName);

        // Save Invoice Customizations
        site_setting_save('invoice_color', $invoiceColor);
        site_setting_save('invoice_footer_note', $invoiceNote);

        // Handle Logo Removal 
        if (!empty($_POST['remove_logo'])) {
            $old = site_setting('logo_url', '');
            if ($old !== '' && str_starts_with($old, $uploadUrl . '/')) {
                $oldPath = dirname(__DIR__) . '/' . $old;
                if (is_file($oldPath)) @unlink($oldPath);
            }
            site_setting_save('logo_url', '');
        }

        // Handle Logo Upload 
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['logo'];
            if ($file['error'] === UPLOAD_ERR_OK && $file['size'] <= 2 * 1024 * 1024) {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($file['tmp_name']);
                $extensions = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/webp' => 'webp', 'image/gif' => 'gif'];
                if (isset($extensions[$mime])) {
                    $filename = 'logo-' . bin2hex(random_bytes(12)) . '.' . $extensions[$mime];
                    if (move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $filename)) {
                        site_setting_save('logo_url', $uploadUrl . '/' . $filename);
                    }
                }
            }
        }

        flash('success', 'Store configuration, mail credentials, homepage badges, and invoice settings updated successfully.');
        redirect('admin/settings.php');
    }
}

admin_header($title);
$currentLogo = site_setting('logo_url', '');
$adminEmail  = site_setting('admin_notification_email', envv('ADMIN_NOTIFICATION_EMAIL', ''));
$currency    = site_setting('currency_symbol', envv('CURRENCY', '৳'));

// SMTP Settings 
$mailHost = site_setting('mail_host', envv('MAIL_HOST', 'smtp.gmail.com'));
$mailPort = site_setting('mail_port', envv('MAIL_PORT', '587'));
$mailEnc  = site_setting('mail_encryption', envv('MAIL_ENCRYPTION', 'tls'));
$mailUser = site_setting('mail_username', envv('MAIL_USERNAME', ''));
$mailPass = site_setting('mail_password', envv('MAIL_PASSWORD', ''));
$fromAddr = site_setting('mail_from_address', envv('MAIL_FROM_ADDRESS', ''));
$fromName = site_setting('mail_from_name', envv('MAIL_FROM_NAME', store_name()));

// Invoice Settings
$invoiceColor = site_setting('invoice_color', '#2c3e50');
$invoiceNote  = site_setting('invoice_footer_note', 'Thank you for shopping with us!');
?>

<div class="panel" style="max-width:800px; margin-bottom:25px;">
    <h2>Store Branding & Identity</h2>
    <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save_settings">

        <div class="form-grid">
            <div class="field">
                <label>Store Name *</label>
                <input name="site_name" required value="<?= e(store_name()) ?>">
            </div>
            <div class="field">
                <label>Currency Symbol *</label>
                <input name="currency_symbol" required value="<?= e($currency) ?>">
            </div>
        </div>

        <div class="field">
            <label>Admin Notification Email</label>
            <input type="email" name="admin_notification_email" value="<?= e($adminEmail) ?>">
            <p class="small muted">Receives order alerts when a customer places an order.</p>
        </div>

        <div class="field">
            <label>Header Announcement Bar</label>
            <input name="announcement" value="<?= e(site_setting('announcement', '')) ?>">
        </div>

        <div class="field">
            <label>Store Logo</label>
            <input name="logo" type="file" accept="image/*">
            <?php if ($currentLogo !== ''): ?>
                <img src="<?= e(url($currentLogo)) ?>" style="max-height: 40px; display: block; margin: 8px 0;">
                <label><input type="checkbox" name="remove_logo" value="1" style="width:auto"> Remove logo</label>
            <?php endif; ?>
        </div>

        <hr style="margin:25px 0; border:0; border-top:1px solid var(--line,#eee);">

        <h2>SMTP & Mailer Settings</h2>
        <div class="form-grid">
            <div class="field">
                <label>SMTP Host</label>
                <input name="mail_host" value="<?= e($mailHost) ?>" placeholder="smtp.gmail.com">
            </div>
            <div class="field">
                <label>SMTP Port</label>
                <input name="mail_port" value="<?= e($mailPort) ?>" placeholder="587">
            </div>
            <div class="field">
                <label>Encryption</label>
                <select name="mail_encryption">
                    <option value="tls" <?= $mailEnc === 'tls' ? 'selected' : '' ?>>TLS (Port 587)</option>
                    <option value="ssl" <?= $mailEnc === 'ssl' ? 'selected' : '' ?>>SSL (Port 465)</option>
                </select>
            </div>
            <div class="field">
                <label>SMTP Username / Gmail Email</label>
                <input type="email" name="mail_username" value="<?= e($mailUser) ?>">
            </div>
            <div class="field">
                <label>SMTP Password / App Password</label>
                <input type="password" name="mail_password" value="<?= e($mailPass) ?>">
            </div>
            <div class="field">
                <label>Sender From Email</label>
                <input type="email" name="mail_from_address" value="<?= e($fromAddr) ?>">
            </div>
            <div class="field" style="grid-column: span 2;">
                <label>Sender From Name</label>
                <input name="mail_from_name" value="<?= e($fromName) ?>">
            </div>
        </div>

        <hr style="margin:25px 0; border:0; border-top:1px solid var(--line,#eee);">

        <h2>Homepage Store Guarantees / Badges</h2>
        <p class="muted small" style="margin-top:-5px; margin-bottom:15px;">Customize the titles and subtitles for the 4 highlight boxes on the storefront.</p>
        <div class="form-grid">
            <div class="field">
                <label>Badge 1 Title (🚀 Shipping)</label>
                <input name="trust_badge_1_title" value="<?= e(site_setting('trust_badge_1_title', 'Nationwide Shipping')) ?>">
            </div>
            <div class="field">
                <label>Badge 1 Subtitle</label>
                <input name="trust_badge_1_sub" value="<?= e(site_setting('trust_badge_1_sub', 'Fast delivery across Bangladesh')) ?>">
            </div>

            <div class="field">
                <label>Badge 2 Title (💵 Payment)</label>
                <input name="trust_badge_2_title" value="<?= e(site_setting('trust_badge_2_title', 'Cash on Delivery')) ?>">
            </div>
            <div class="field">
                <label>Badge 2 Subtitle</label>
                <input name="trust_badge_2_sub" value="<?= e(site_setting('trust_badge_2_sub', 'Pay safely at your doorstep')) ?>">
            </div>

            <div class="field">
                <label>Badge 3 Title (✨ Quality)</label>
                <input name="trust_badge_3_title" value="<?= e(site_setting('trust_badge_3_title', 'Premium Fabric')) ?>">
            </div>
            <div class="field">
                <label>Badge 3 Subtitle</label>
                <input name="trust_badge_3_sub" value="<?= e(site_setting('trust_badge_3_sub', 'Crafted for long-lasting comfort')) ?>">
            </div>

            <div class="field">
                <label>Badge 4 Title (🔄 Exchange)</label>
                <input name="trust_badge_4_title" value="<?= e(site_setting('trust_badge_4_title', 'Hassle-Free Exchange')) ?>">
            </div>
            <div class="field">
                <label>Badge 4 Subtitle</label>
                <input name="trust_badge_4_sub" value="<?= e(site_setting('trust_badge_4_sub', 'Easy returns & order support')) ?>">
            </div>
        </div>

        <hr style="margin:25px 0; border:0; border-top:1px solid var(--line,#eee);">

        <h2>Invoice Design Customization</h2>
        <div class="form-grid">
            <div class="field">
                <label>Invoice Primary Accent Color</label>
                <input type="color" name="invoice_color" value="<?= e($invoiceColor) ?>" style="height:40px; padding:2px; cursor:pointer;">
            </div>
            <div class="field">
                <label>Invoice Footer / Thank You Note</label>
                <input name="invoice_footer_note" value="<?= e($invoiceNote) ?>" placeholder="e.g. Thank you for shopping with us!">
            </div>
        </div>

        <button type="submit" style="margin-top:20px;">Save All Settings</button>
    </form>
</div>

<!-- Send Test Email Panel -->
<div class="panel" style="max-width:800px;">
    <h2>Test Mail Configuration</h2>
    <p class="muted">Send a test email to verify your SMTP configuration and Gmail App Password.</p>
    <form method="post" class="inline-form" style="display:flex; gap:10px;">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="test_email">
        <input type="email" name="test_email_address" required placeholder="Enter recipient email..." style="padding:8px 12px; flex:1;">
        <button class="button secondary">Send Test Email</button>
    </form>
</div>

<?php admin_footer(); ?>