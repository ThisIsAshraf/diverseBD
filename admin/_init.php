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
                <a class="brand" href="<?= e(url('admin/index.php')) ?>"><?= brand_html() ?></a>
                <a href="<?= e(url('admin/index.php')) ?>">Dashboard</a>
                <a href="<?= e(url('admin/products.php')) ?>">Products & inventory</a>
                <a href="<?= e(url('admin/categories.php')) ?>">Categories</a>
                <a href="<?= e(url('admin/orders.php')) ?>">Order management</a>
                <a href="<?= e(url('admin/reports.php')) ?>">Sales reports</a>
                <a href="<?= e(url('admin/coupons.php')) ?>">Discounts & promotions</a>
                <a href="<?= e(url('admin/customers.php')) ?>">Customers</a>
                <a href="<?= e(url('admin/contacts.php')) ?>">Contact messages</a>
                <a href="<?= e(url('admin/settings.php')) ?>">Store branding</a>
                <a href="<?= e(url('admin/stock-logs.php')) ?>">Stock logs</a>
                <a href="<?= e(url('admin/slides.php')) ?>">Hero Banner Slides</a>
                <a href="<?= e(url('admin/social.php')) ?>">Social Media Feed</a>
                <a href="<?= e(url('admin/testimonials.php')) ?>">Customer Reviews</a>
                <a href="<?= e(url('admin/security.php')) ?>">Admin Security & Sessions</a>
                <a href="<?= e(url('index.php')) ?>" target="_blank">View storefront ↗</a>
                <a href="<?= e(url('admin/logout.php')) ?>">Sign out</a>
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