<?php
require_once __DIR__ . '/app/bootstrap.php';

$slug = trim((string)($_GET['slug'] ?? ''));
if ($slug === '') {
    redirect('index.php');
}

// Fetch main product details
$s = db()->prepare('SELECT p.*, c.name category_name, c.slug category_slug FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.slug=? AND p.active=1');
$s->execute([$slug]);
$product = $s->fetch();

if (!$product) {
    http_response_code(404);
    $pageTitle = 'Product Not Found';
    require_once __DIR__ . '/app/header.php';
    echo '<div class="empty"><h2>Product not found</h2><p>The product you are looking for does not exist or has been removed.</p><a class="button" href="' . e(url('index.php')) . '">Return to shop</a></div>';
    require_once __DIR__ . '/app/footer.php';
    exit;
}

// Fetch additional gallery images
$gs = db()->prepare('SELECT * FROM product_images WHERE product_id=? ORDER BY id ASC');
$gs->execute([$product['id']]);
$gallery = $gs->fetchAll();

// Handle Add to Bag POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    verify_csrf();
    $qty = max(1, (int)($_POST['qty'] ?? 1));
    if ($qty > (int)$product['stock']) {
        flash('error', 'Only ' . $product['stock'] . ' units available in stock.');
    } else {
        $_SESSION['cart'][$product['id']] = ($_SESSION['cart'][$product['id']] ?? 0) + $qty;
        flash('success', $product['name'] . ' added to your bag.');
        redirect('cart.php');
    }
}

$pageTitle = $product['name'];
require_once __DIR__ . '/app/header.php';

// Format main cover image source
$mainImg = $product['image_url'] ? (str_starts_with($product['image_url'], 'http') ? $product['image_url'] : url($product['image_url'])) : 'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?w=800';
?>

<div class="product-detail-layout" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; margin-bottom: 40px;">

    <!-- Left Column: Product Gallery -->
    <div class="product-gallery">
        <!-- Main Featured Photo -->
        <div class="main-image-wrap" style="background:#fff; border:1px solid var(--line, #eee); border-radius:8px; overflow:hidden; margin-bottom:12px;">
            <img id="mainProductImg" src="<?= e($mainImg) ?>" alt="<?= e($product['name']) ?>" style="width:100%; height:auto; display:block; object-fit:cover; aspect-ratio:1/1;">
        </div>

        <!-- Gallery Thumbnails Strip -->
        <?php if ($gallery): ?>
            <div class="thumbnails" style="display:flex; gap:10px; flex-wrap:wrap;">
                <!-- Main image thumbnail option -->
                <img class="thumb-img active" src="<?= e($mainImg) ?>" onclick="switchImage(this)" style="width:70px; height:70px; object-fit:cover; border-radius:4px; border:2px solid var(--accent, #333); cursor:pointer;">

                <!-- Additional gallery image thumbnails -->
                <?php foreach ($gallery as $g):
                    $gUrl = str_starts_with($g['image_url'], 'http') ? $g['image_url'] : url($g['image_url']);
                ?>
                    <img class="thumb-img" src="<?= e($gUrl) ?>" onclick="switchImage(this)" style="width:70px; height:70px; object-fit:cover; border-radius:4px; border:1px solid #ddd; cursor:pointer;">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Column: Product Information -->
    <div class="product-info">
        <?php if ($product['category_name']): ?>
            <div class="eyebrow"><a href="<?= e(url('index.php?category=' . urlencode($product['category_slug']))) ?>" style="text-decoration:none; color:inherit;"><?= e($product['category_name']) ?></a></div>
        <?php endif; ?>

        <h1 style="margin: 6px 0 12px 0; font-size: 32px;"><?= e($product['name']) ?></h1>
        <p class="muted small" style="margin-bottom: 12px;">SKU: <?= e($product['sku']) ?></p>

        <div class="price" style="font-size:24px; font-weight:bold; margin-bottom:20px;">
            <?= money($product['price']) ?>
            <?php if (!empty($product['compare_price']) && $product['compare_price'] > $product['price']): ?>
                <span class="old-price" style="text-decoration:line-through; color:#888; font-size:18px; margin-left:8px;"><?= money($product['compare_price']) ?></span>
            <?php endif; ?>
        </div>

        <?php if ((int)$product['stock'] > 0): ?>
            <form method="post" style="margin-bottom:24px; max-width:300px;">
                <?= csrf_field() ?>
                <div class="field" style="margin-bottom:12px;">
                    <label>Quantity</label>
                    <input type="number" name="qty" value="1" min="1" max="<?= (int)$product['stock'] ?>" style="padding:8px; width:100%;">
                </div>
                <button class="button" name="add_to_cart" value="1" style="width:100%; padding:12px;">Add to Bag</button>
            </form>
        <?php else: ?>
            <div class="notice error" style="margin-bottom:24px;">Out of Stock</div>
        <?php endif; ?>

        <div class="description" style="line-height:1.6; color:#444; border-top:1px solid var(--line, #eee); padding-top:20px;">
            <h3>Product Overview</h3>
            <p style="white-space:pre-wrap;"><?= e($product['description'] ?: 'No description available for this item.') ?></p>
        </div>
    </div>
</div>

<!-- Interactive Thumbnail Switching Script -->
<script>
    function switchImage(thumbEl) {
        document.getElementById('mainProductImg').src = thumbEl.src;
        document.querySelectorAll('.thumb-img').forEach(el => {
            el.style.border = '1px solid #ddd';
        });
        thumbEl.style.border = '2px solid var(--accent, #333)';
    }
</script>

<?php require_once __DIR__ . '/app/footer.php'; ?>