</main>

<?php
// Dynamic Footer Values (Falls back to default defaults if not set in admin)
$footerAbout     = site_setting('footer_about', 'Everyday clothing, thoughtfully selected.');
$footerPhone     = site_setting('footer_phone', '');
$footerEmail     = site_setting('footer_email', '');
$footerAddress   = site_setting('footer_address', '');
$footerCopyright = site_setting('footer_copyright', '© ' . date('Y') . ' ' . store_name() . '. All rights reserved.');
?>

<footer class="site-footer">
    <div>
        <a class="brand" href="<?= e(url('index.php')) ?>"><?= brand_html() ?></a>
        <p><?= e($footerAbout) ?></p>
        <?php if ($footerAddress): ?>
            <p class="small muted">📍 <?= e($footerAddress) ?></p>
        <?php endif; ?>
    </div>

    <div>
        <b>Customer care</b>
        <p><a href="<?= e(url('contact.php')) ?>">Contact us</a></p>
        <p><a href="<?= e(url('track-order.php')) ?>">Track order</a></p>
        <?php if ($footerPhone): ?>
            <p class="small muted">📞 <?= e($footerPhone) ?></p>
        <?php endif; ?>
        <?php if ($footerEmail): ?>
            <p class="small muted">📧 <?= e($footerEmail) ?></p>
        <?php endif; ?>
    </div>

    <div>
        <b>Information</b>
        <p><a href="<?= e(url('contact.php')) ?>">Shipping & returns</a></p>
        <p><a href="<?= e(url('admin/login.php')) ?>">Staff login</a></p>
    </div>

    <small><?= e($footerCopyright) ?></small>
</footer>

</body>

</html>