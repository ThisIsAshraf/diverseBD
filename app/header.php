<?php
require_once __DIR__ . '/bootstrap.php';

$storeName = store_name();
$pageTitle = $pageTitle ?? $storeName;
$announcement = site_setting('announcement', 'New season, new essentials — free delivery offers may apply');
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | <?= e($storeName) ?></title>
    <link rel="stylesheet" href="<?= e(url('assets/style.css')) ?>">
</head>

<body>
    <div class="announcement"><?= e($announcement) ?></div>
    <header class="site-header"><a class="brand" href="<?= e(url('index.php')) ?>"><?= brand_html() ?></a>
        <nav><a href="<?= e(url('index.php')) ?>">Shop</a><a href="<?= e(url('index.php?category=new-arrivals')) ?>">New arrivals</a><a href="<?= e(url('contact.php')) ?>">Contact</a><a class="cart-link" href="<?= e(url('cart.php')) ?>">Bag (<?= cart_count() ?>)</a><a href="<?= e(url('track-order.php')) ?>">Track Order</a></nav>
    </header>
    <main class="container">
        <?php if ($m = flash('success')): ?><div class="notice success"><?= e($m) ?></div><?php endif; ?><?php if ($m = flash('error')): ?><div class="notice error"><?= e($m) ?></div><?php endif; ?>