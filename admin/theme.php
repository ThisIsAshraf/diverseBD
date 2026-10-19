<?php require_once __DIR__ . '/_init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'save_theme') {
        // Theme Colors
        site_setting_save('theme_primary_color', trim((string)($_POST['theme_primary_color'] ?? '#2c3e50')));
        site_setting_save('theme_header_bg', trim((string)($_POST['theme_header_bg'] ?? '#1a252f')));
        site_setting_save('theme_announcement_text', trim((string)($_POST['theme_announcement_text'] ?? '#ffffff')));
        site_setting_save('theme_body_bg', trim((string)($_POST['theme_body_bg'] ?? '#f8f9fa')));
        site_setting_save('theme_card_bg', trim((string)($_POST['theme_card_bg'] ?? '#ffffff')));
        site_setting_save('theme_border_color', trim((string)($_POST['theme_border_color'] ?? '#e0e0e0')));
        site_setting_save('theme_footer_bg', trim((string)($_POST['theme_footer_bg'] ?? '#111111')));
        site_setting_save('theme_footer_text', trim((string)($_POST['theme_footer_text'] ?? '#ffffff')));

        // Footer Contents
        site_setting_save('footer_about', trim((string)($_POST['footer_about'] ?? '')));
        site_setting_save('footer_phone', trim((string)($_POST['footer_phone'] ?? '')));
        site_setting_save('footer_email', trim((string)($_POST['footer_email'] ?? '')));
        site_setting_save('footer_address', trim((string)($_POST['footer_address'] ?? '')));
        site_setting_save('footer_copyright', trim((string)($_POST['footer_copyright'] ?? '')));

        // Social Media Links
        site_setting_save('social_facebook', trim((string)($_POST['social_facebook'] ?? '')));
        site_setting_save('social_instagram', trim((string)($_POST['social_instagram'] ?? '')));
        site_setting_save('social_whatsapp', trim((string)($_POST['social_whatsapp'] ?? '')));
        site_setting_save('social_tiktok', trim((string)($_POST['social_tiktok'] ?? '')));
        site_setting_save('social_youtube', trim((string)($_POST['social_youtube'] ?? '')));

        flash('success', 'Storefront theme, footer, and social links updated successfully.');
        redirect('admin/theme.php');
    }

    if ($action === 'apply_preset') {
        $preset = (string)($_POST['preset'] ?? 'classic');

        if ($preset === 'dark') {
            site_setting_save('theme_primary_color', '#e74c3c');
            site_setting_save('theme_header_bg', '#111111');
            site_setting_save('theme_announcement_text', '#ffffff');
            site_setting_save('theme_body_bg', '#181818');
            site_setting_save('theme_card_bg', '#222222');
            site_setting_save('theme_border_color', '#333333');
            site_setting_save('theme_footer_bg', '#0a0a0a');
            site_setting_save('theme_footer_text', '#ffffff');
        } elseif ($preset === 'minimal') {
            site_setting_save('theme_primary_color', '#000000');
            site_setting_save('theme_header_bg', '#000000');
            site_setting_save('theme_announcement_text', '#ffffff');
            site_setting_save('theme_body_bg', '#ffffff');
            site_setting_save('theme_card_bg', '#ffffff');
            site_setting_save('theme_border_color', '#eeeeee');
            site_setting_save('theme_footer_bg', '#000000');
            site_setting_save('theme_footer_text', '#ffffff');
        } elseif ($preset === 'warm') {
            site_setting_save('theme_primary_color', '#d35400');
            site_setting_save('theme_header_bg', '#2c3e50');
            site_setting_save('theme_announcement_text', '#f39c12');
            site_setting_save('theme_body_bg', '#fcf8f2');
            site_setting_save('theme_card_bg', '#ffffff');
            site_setting_save('theme_border_color', '#f0e6d2');
            site_setting_save('theme_footer_bg', '#2c3e50');
            site_setting_save('theme_footer_text', '#fcf8f2');
        } else { // Classic Default
            site_setting_save('theme_primary_color', '#2c3e50');
            site_setting_save('theme_header_bg', '#1a252f');
            site_setting_save('theme_announcement_text', '#ffffff');
            site_setting_save('theme_body_bg', '#f8f9fa');
            site_setting_save('theme_card_bg', '#ffffff');
            site_setting_save('theme_border_color', '#e0e0e0');
            site_setting_save('theme_footer_bg', '#111111');
            site_setting_save('theme_footer_text', '#ffffff');
        }

        flash('success', 'Applied ' . ucfirst($preset) . ' theme preset.');
        redirect('admin/theme.php');
    }
}

// Current Color Settings
$themePrimary     = site_setting('theme_primary_color', '#2c3e50');
$themeHeaderBg    = site_setting('theme_header_bg', '#1a252f');
$themeAnnounceText = site_setting('theme_announcement_text', '#ffffff');
$themeBodyBg      = site_setting('theme_body_bg', '#f8f9fa');
$themeCardBg      = site_setting('theme_card_bg', '#ffffff');
$themeBorder      = site_setting('theme_border_color', '#e0e0e0');
$themeFooterBg    = site_setting('theme_footer_bg', '#111111');
$themeFooterText  = site_setting('theme_footer_text', '#ffffff');

// Current Footer Content Settings
$footerAbout     = site_setting('footer_about', 'Modern wardrobe staples, everyday fits, and premium quality crafted for style.');
$footerPhone     = site_setting('footer_phone', '+880 1700-000000');
$footerEmail     = site_setting('footer_email', 'hello.divers.almarez@gmail.com');
$footerAddress   = site_setting('footer_address', 'Dhaka, Bangladesh');
$footerCopyright = site_setting('footer_copyright', '© ' . date('Y') . ' ' . store_name() . '. All rights reserved.');

// Current Social Links Settings
$socialFacebook  = site_setting('social_facebook', '');
$socialInstagram = site_setting('social_instagram', '');
$socialWhatsapp  = site_setting('social_whatsapp', '');
$socialTiktok    = site_setting('social_tiktok', '');
$socialYoutube   = site_setting('social_youtube', '');

admin_header('Storefront Theme & Footer Control'); ?>

<!-- Preset Quick Actions -->
<div class="panel" style="margin-bottom:22px;">
    <h2>One-Click Theme Presets</h2>
    <p class="muted small">Quickly set pre-configured color palettes across your site.</p>
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:12px;">
        <form method="post" style="margin:0;"><?= csrf_field() ?><input type="hidden" name="action" value="apply_preset"><input type="hidden" name="preset" value="classic"><button class="button secondary">Classic Navy</button></form>
        <form method="post" style="margin:0;"><?= csrf_field() ?><input type="hidden" name="action" value="apply_preset"><input type="hidden" name="preset" value="minimal"><button class="button secondary">Monochrome Minimal</button></form>
        <form method="post" style="margin:0;"><?= csrf_field() ?><input type="hidden" name="action" value="apply_preset"><input type="hidden" name="preset" value="dark"><button class="button secondary">Midnight Dark</button></form>
        <form method="post" style="margin:0;"><?= csrf_field() ?><input type="hidden" name="action" value="apply_preset"><input type="hidden" name="preset" value="warm"><button class="button secondary">Warm Terracotta</button></form>
    </div>
</div>

<!-- Custom Controls Panel -->
<div class="panel" style="max-width:800px; margin-bottom:22px;">
    <form method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save_theme">

        <h2>Theme & Color Palette</h2>
        <div class="form-grid">
            <div class="field">
                <label>Primary Accent Color</label>
                <input type="color" name="theme_primary_color" value="<?= e($themePrimary) ?>" style="height:40px; padding:2px; cursor:pointer;">
            </div>

            <div class="field">
                <label>Header / Announcement Background</label>
                <input type="color" name="theme_header_bg" value="<?= e($themeHeaderBg) ?>" style="height:40px; padding:2px; cursor:pointer;">
            </div>

            <div class="field">
                <label>Announcement Text Color</label>
                <input type="color" name="theme_announcement_text" value="<?= e($themeAnnounceText) ?>" style="height:40px; padding:2px; cursor:pointer;">
            </div>

            <div class="field">
                <label>Page Body Background</label>
                <input type="color" name="theme_body_bg" value="<?= e($themeBodyBg) ?>" style="height:40px; padding:2px; cursor:pointer;">
            </div>

            <div class="field">
                <label>Product Card Background</label>
                <input type="color" name="theme_card_bg" value="<?= e($themeCardBg) ?>" style="height:40px; padding:2px; cursor:pointer;">
            </div>

            <div class="field">
                <label>Card / Section Border Color</label>
                <input type="color" name="theme_border_color" value="<?= e($themeBorder) ?>" style="height:40px; padding:2px; cursor:pointer;">
            </div>

            <div class="field">
                <label>Footer Background Color</label>
                <input type="color" name="theme_footer_bg" value="<?= e($themeFooterBg) ?>" style="height:40px; padding:2px; cursor:pointer;">
            </div>

            <div class="field">
                <label>Footer Text Color</label>
                <input type="color" name="theme_footer_text" value="<?= e($themeFooterText) ?>" style="height:40px; padding:2px; cursor:pointer;">
            </div>
        </div>

        <hr style="margin:25px 0; border:0; border-top:1px solid var(--line,#eee);">

        <h2>Footer Content Configuration</h2>
        <div class="field">
            <label>Brand Bio / About Text</label>
            <textarea name="footer_about" rows="3" placeholder="Brief summary of your store"><?= e($footerAbout) ?></textarea>
        </div>

        <div class="form-grid">
            <div class="field">
                <label>Support Phone</label>
                <input name="footer_phone" value="<?= e($footerPhone) ?>" placeholder="+880 1700-000000">
            </div>

            <div class="field">
                <label>Support Email</label>
                <input type="email" name="footer_email" value="<?= e($footerEmail) ?>" placeholder="support@yourdomain.com">
            </div>

            <div class="field" style="grid-column: span 2;">
                <label>Store Physical Address</label>
                <input name="footer_address" value="<?= e($footerAddress) ?>" placeholder="Dhaka, Bangladesh">
            </div>

            <div class="field" style="grid-column: span 2;">
                <label>Copyright Bar Text</label>
                <input name="footer_copyright" value="<?= e($footerCopyright) ?>" placeholder="© 2026 DiverseBD. All rights reserved.">
            </div>
        </div>

        <hr style="margin:25px 0; border:0; border-top:1px solid var(--line,#eee);">

        <h2>Social Media Links</h2>
        <div class="form-grid">
            <div class="field">
                <label>Facebook Page URL</label>
                <input name="social_facebook" value="<?= e($socialFacebook) ?>" placeholder="https://facebook.com/yourpage">
            </div>

            <div class="field">
                <label>Instagram Profile URL</label>
                <input name="social_instagram" value="<?= e($socialInstagram) ?>" placeholder="https://instagram.com/yourprofile">
            </div>

            <div class="field">
                <label>WhatsApp Number / Link</label>
                <input name="social_whatsapp" value="<?= e($socialWhatsapp) ?>" placeholder="https://wa.me/8801700000000">
            </div>

            <div class="field">
                <label>TikTok Profile URL</label>
                <input name="social_tiktok" value="<?= e($socialTiktok) ?>" placeholder="https://tiktok.com/@yourhandle">
            </div>

            <div class="field" style="grid-column: span 2;">
                <label>YouTube Channel URL</label>
                <input name="social_youtube" value="<?= e($socialYoutube) ?>" placeholder="https://youtube.com/@yourchannel">
            </div>
        </div>

        <button class="button" style="margin-top:20px;">Save Theme, Footer & Social Settings</button>
    </form>
</div>

<?php admin_footer(); ?>