<?php
require_once __DIR__ . '/bootstrap.php';

$storeName = store_name();
$pageTitle = $pageTitle ?? $storeName;
$announcement = site_setting('announcement', 'New season, new essentials — free delivery offers may apply');

// Fetch Storefront Theme Preferences (Single clean call)
$themePrimary     = site_setting('theme_primary_color', '#2c3e50');
$themeHeaderBg    = site_setting('theme_header_bg', '#1a252f');
$themeAnnounceText = site_setting('theme_announcement_text', '#ffffff');
$themeBodyBg      = site_setting('theme_body_bg', '#f8f9fa');
$themeCardBg      = site_setting('theme_card_bg', '#ffffff');
$themeBorder      = site_setting('theme_border_color', '#e0e0e0');
$themeFooterBg    = site_setting('theme_footer_bg', '#111111');
$themeFooterText  = site_setting('theme_footer_text', '#ffffff');

// Protocol-agnostic CSS URL (Works on both HTTP and HTTPS)
$cssUrl = preg_replace('/^https?:/', '', url('assets/style.css'));
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | <?= e($storeName) ?></title>

    <!-- Google Search Console Verification Tag -->
    <meta name="google-site-verification" content="vyZTQMWGBgop0OdB97oEAbU1p4rZsjmSJkxrn5UM_Kg" />

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?= e($cssUrl) ?>?v=<?= time() ?>">

    <!-- Dynamic Theme Styling Overrides -->
    <style type="text/css">
        :root {
            --accent: <?= e($themePrimary) ?>;
            --line: <?= e($themeBorder) ?>;
            --panel-bg: <?= e($themeCardBg) ?>;
        }

        body {
            background-color: <?= e($themeBodyBg) ?>;
        }

        .announcement {
            background-color: <?= e($themeHeaderBg) ?>;
            color: <?= e($themeAnnounceText) ?>;
        }

        .button,
        button:not(.secondary) {
            background-color: <?= e($themePrimary) ?>;
            border-color: <?= e($themePrimary) ?>;
        }

        .product-card,
        .panel,
        .trust-bar {
            background-color: <?= e($themeCardBg) ?>;
            border-color: <?= e($themeBorder) ?>;
        }

        .tag {
            background-color: <?= e($themePrimary) ?>;
        }

        .site-footer {
            background-color: <?= e($themeFooterBg) ?>;
            color: <?= e($themeFooterText) ?>;
        }

        .site-footer a,
        .site-footer p,
        .site-footer span {
            color: <?= e($themeFooterText) ?>;
        }
    </style>
</head>

<body>
    <div class="announcement"><?= e($announcement) ?></div>
    <header class="site-header">
        <a class="brand" href="<?= e(url('index.php')) ?>"><?= brand_html() ?></a>
        <nav>
            <a href="<?= e(url('index.php')) ?>">Shop</a>
            <a href="<?= e(url('index.php?category=new-arrivals')) ?>">New arrivals</a>
            <a href="<?= e(url('track-order.php')) ?>">Track Order</a>
            <a href="<?= e(url('contact.php')) ?>">Contact</a>
            <a class="cart-link" href="<?= e(url('cart.php')) ?>">Bag (<?= cart_count() ?>)</a>
        </nav>
    </header>
    <main class="container">
        <?php if ($m = flash('success')): ?><div class="notice success"><?= e($m) ?></div><?php endif; ?>
        <?php if ($m = flash('error')): ?><div class="notice error"><?= e($m) ?></div><?php endif; ?>