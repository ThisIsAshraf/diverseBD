<?php require dirname(__DIR__) . '/app/bootstrap.php';
admin_required();
function admin_header(string $title): void
{ ?>
    <!doctype html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title><?= e($title) ?> · <?= e(store_name()) ?> Admin</title>
        <link rel="stylesheet" href="<?= e(url('assets/style.css')) ?>">
    </head>

    <body class="admin-body">
        <div class="admin-layout">
            <aside class="admin-sidebar">
                <a class="brand" href="<?= e(url('manage_portal_x92/index.php')) ?>"><?= brand_html() ?></a>
                <a href="<?= e(url('manage_portal_x92/index.php')) ?>">Dashboard</a>
                <a href="<?= e(url('manage_portal_x92/products.php')) ?>">Products & inventory</a>
                <a href="<?= e(url('manage_portal_x92/categories.php')) ?>">Categories</a>
                <a href="<?= e(url('manage_portal_x92/orders.php')) ?>">Order management</a>
                <a href="<?= e(url('manage_portal_x92/reports.php')) ?>">Sales reports</a>
                <a href="<?= e(url('manage_portal_x92/coupons.php')) ?>">Discounts & promotions</a>
                <a href="<?= e(url('manage_portal_x92/customers.php')) ?>">Customers</a>
                <a href="<?= e(url('manage_portal_x92/users.php')) ?>">Customer Account Management</a>
                <a href="<?= e(url('manage_portal_x92/contacts.php')) ?>">Contact messages</a>
                <a href="<?= e(url('manage_portal_x92/settings.php')) ?>">Store branding</a>
                <a href="<?= e(url('manage_portal_x92/popup.php')) ?>">Manage PopUp Banner</a>
                <a href="<?= e(url('manage_portal_x92/theme.php')) ?>">Storefront Theme</a>
                <a href="<?= e(url('manage_portal_x92/stock-logs.php')) ?>">Stock logs</a>
                <a href="<?= e(url('manage_portal_x92/slides.php')) ?>">Hero Banner Slides</a>
                <a href="<?= e(url('manage_portal_x92/social.php')) ?>">Social Media Feed</a>
                <a href="<?= e(url('manage_portal_x92/testimonials.php')) ?>">Customer Reviews</a>
                <a href="<?= e(url('manage_portal_x92/api_keys.php')) ?>">API Integrations</a>
                <a href="<?= e(url('manage_portal_x92/analytics.php')) ?>">Analytics & Traffic</a>
                <a href="<?= e(url('manage_portal_x92/security.php')) ?>">Admin Security & Sessions</a>
                <a href="<?= e(url('index.php')) ?>" target="_blank">View storefront ↗</a>
                <a href="<?= e(url('manage_portal_x92/logout.php')) ?>">Sign out</a>
            </aside>
            <section class="admin-main">
                <div class="admin-top">
                    <div>
                        <div class="eyebrow">Store administration</div>
                        <h1 class="page-title" style="font-size:29px"><?= e($title) ?></h1>
                    </div>
                    <span class="small">Signed in as <?= e((string)($_SESSION['admin_name'] ?? 'Admin')) ?></span>
                </div>
                <?php if ($m = flash('success')): ?><div class="notice success"><?= e($m) ?></div><?php endif; ?>
                <?php if ($m = flash('error')): ?><div class="notice error"><?= e($m) ?></div><?php endif; ?>
            <?php }

        function admin_footer(): void
        { ?>
            </section>
        </div>
    </body>

    </html>
<?php } ?>