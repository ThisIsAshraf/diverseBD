<?php
require_once __DIR__ . '/app/bootstrap.php';

$q = trim((string)($_GET['q'] ?? ''));
$cat = trim((string)($_GET['category'] ?? ''));
$sql = 'SELECT p.*, c.name category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.active=1';
$args = [];

if ($q !== '') {
    $sql .= ' AND (p.name LIKE ? OR p.description LIKE ?)';
    $args[] = '\%' . $q . '%';
    $args[] = '\%' . $q . '%';
}

if ($cat !== '' && $cat !== 'new-arrivals') {
    $sql .= ' AND c.slug=?';
    $args[] = $cat;
}

if ($cat === 'new-arrivals') {
    $sql .= ' AND p.created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY)';
}

$sql .= ' ORDER BY p.created_at DESC LIMIT 24';
$st = db()->prepare($sql);
$st->execute($args);
$products = $st->fetchAll();

// Dynamic Hero Banner Slides
$slides = db()->query('SELECT * FROM hero_slides WHERE active=1 ORDER BY sort_order ASC, id DESC')->fetchAll();

// Dynamic Customer Testimonials
$testimonials = db()->query('SELECT * FROM testimonials WHERE active=1 ORDER BY sort_order ASC, id DESC')->fetchAll();

$categories = db()->query('
    SELECT c.*, COUNT(p.id) AS product_count 
    FROM categories c 
    LEFT JOIN products p ON p.category_id = c.id AND p.active = 1 
    GROUP BY c.id 
    ORDER BY c.name
')->fetchAll();

// Dynamic Store Guarantees / Badges from Site Settings
$b1Title = site_setting('trust_badge_1_title', 'Nationwide Shipping');
$b1Sub   = site_setting('trust_badge_1_sub', 'Fast delivery across Bangladesh');

$b2Title = site_setting('trust_badge_2_title', 'Cash on Delivery');
$b2Sub   = site_setting('trust_badge_2_sub', 'Pay safely at your doorstep');

$b3Title = site_setting('trust_badge_3_title', 'Premium Fabric');
$b3Sub   = site_setting('trust_badge_3_sub', 'Crafted for long-lasting comfort');

$b4Title = site_setting('trust_badge_4_title', 'Hassle-Free Exchange');
$b4Sub   = site_setting('trust_badge_4_sub', 'Easy returns & order support');

// --- Dynamic SEO Management Settings ---
$pageTitle       = site_setting('seo_meta_title', 'Shop Modern Clothing & Wardrobe Essentials \vert{} DiverseBD');
$metaDescription = site_setting('seo_meta_description', 'Discover modern clothing, premium wardrobe staples, and new season drops with nationwide Cash on Delivery in Bangladesh.');
$metaKeywords    = site_setting('seo_meta_keywords', 'clothing, fashion, online shop, bangladesh, modern fit, wardrobe');
$ogImage         = site_setting('seo_og_image', site_setting('logo_url', ''));

// Dynamic Page Overrides based on category or search query
if ($cat) {
    $catName = ucfirst(str_replace('-', ' ', $cat));
    $pageTitle = e($catName) . ' Collection | ' . store_name();
    $metaDescription = 'Explore our ' . e($catName) . ' collection. Premium quality apparel delivered nationwide.';
} elseif ($q) {
    $pageTitle = 'Search results for "' . e($q) . '" | ' . store_name();
}

require_once __DIR__ . '/app/header.php';
?>

<!-- Embedded Modern Styling Overrides for Homepage -->
<style>
    .hero-slider-container {
        position: relative;
        overflow: hidden;
        border-radius: 16px;
        margin-bottom: 35px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .hero-slides-wrapper {
        display: flex;
        transition: transform 0.6s cubic-bezier(0.25, 1, 0.5, 1);
    }

    .hero-slide-item {
        min-width: 100%;
        box-sizing: border-box;
        padding: 80px 50px;
        background-size: cover;
        background-position: center;
        position: relative;
        display: flex;
        align-items: center;
        min-height: 420px;
    }

    .hero-glass-card {
        max-width: 580px;
        background: rgba(255, 255, 255, 0.88);
        padding: 35px 30px;
        border-radius: 12px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    }

    .trust-badge-card {
        background: #fff;
        padding: 22px 18px;
        border-radius: 12px;
        border: 1px solid var(--line, #eee);
        text-align: center;
        transition: transform 0.25s, box-shadow 0.25s;
    }

    .trust-badge-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
    }

    .category-card {
        background: #fff;
        border: 1px solid var(--line, #eee);
        padding: 22px 18px;
        border-radius: 12px;
        text-decoration: none;
        color: inherit;
        display: block;
        transition: all 0.25s ease;
    }

    .category-card:hover {
        transform: translateY(-4px);
        border-color: var(--green, #25483d);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
    }

    .product-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid var(--line, #eee);
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        position: relative;
        display: flex;
        flex-direction: column;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.07);
    }

    .product-image-container {
        position: relative;
        aspect-ratio: 3/4;
        overflow: hidden;
        background: var(--cream, #f7f5ef);
        display: block;
    }

    .product-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .product-card:hover .product-image-container img {
        transform: scale(1.06);
    }

    .badge-discount {
        position: absolute;
        top: 12px;
        left: 12px;
        background: #e74c3c;
        color: #fff;
        font-weight: 700;
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 4px;
        letter-spacing: 0.5px;
    }

    .badge-soldout {
        position: absolute;
        top: 12px;
        right: 12px;
        background: rgba(0, 0, 0, 0.7);
        color: #fff;
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 4px;
    }

    .product-info {
        padding: 16px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .product-info h3 {
        font-size: 15px;
        margin: 0 0 8px 0;
        font-weight: 600;
        line-height: 1.3;
    }

    .product-info h3 a {
        text-decoration: none;
        color: inherit;
        transition: color 0.2s;
    }

    .product-info h3 a:hover {
        color: var(--green, #25483d);
    }

    .price-wrap {
        margin-top: auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 10px;
    }

    .quick-add-btn {
        width: 100%;
        margin-top: 12px;
        padding: 10px;
        font-size: 13px;
        border-radius: 6px;
        border: none;
        background: var(--green, #25483d);
        color: #fff;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.2s;
    }

    .quick-add-btn:hover {
        opacity: 0.9;
    }
</style>

<!-- Dynamic Hero Banner Slider -->
<?php if ($slides && $q === '' && $cat === ''): ?>
    <section class="hero-slider-container">
        <div class="hero-slides-wrapper">
            <?php foreach ($slides as $idx => $sl): $bgImage = !empty($sl['image_url']) ? 'url(' . e(url($sl['image_url'])) . ')' : 'none'; ?>
                <div class="hero-slide-item" style="background-image: <?= $bgImage ?>;">
                    <div class="hero-glass-card">
                        <?php if ($sl['eyebrow']): ?>
                            <div class="eyebrow" style="text-transform:uppercase; letter-spacing:1.5px; font-weight:700; font-size:12px; color:var(--green, #25483d); margin-bottom:8px;"><?= e($sl['eyebrow']) ?></div>
                        <?php endif; ?>
                        <h1 style="font-size:38px; font-family:'Playfair Display', serif; margin:0 0 12px 0; line-height:1.15; font-weight:700;"><?= e($sl['title']) ?></h1>
                        <?php if ($sl['subtitle']): ?>
                            <p class="muted" style="font-size:15px; margin-bottom:22px; color:#444; line-height:1.5;"><?= e($sl['subtitle']) ?></p>
                        <?php endif; ?>
                        <a class="button" href="<?= e(url($sl['button_url'] ?: 'index.php')) ?>" style="padding:12px 26px; border-radius:6px; font-weight:600;"><?= e($sl['button_text'] ?: 'Explore Collection') ?> →</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if (count($slides) > 1): ?>
            <button onclick="prevSlide()" style="position:absolute; left:20px; top:50%; transform:translateY(-50%); background:rgba(255,255,255,0.9); border:none; width:42px; height:42px; border-radius:50%; cursor:pointer; font-size:18px; box-shadow:0 4px 10px rgba(0,0,0,0.1);">❮</button>
            <button onclick="nextSlide()" style="position:absolute; right:20px; top:50%; transform:translateY(-50%); background:rgba(255,255,255,0.9); border:none; width:42px; height:42px; border-radius:50%; cursor:pointer; font-size:18px; box-shadow:0 4px 10px rgba(0,0,0,0.1);">❯</button>
        <?php endif; ?>
    </section>

    <script>
        let currentSlideIndex = 0;
        const slidesWrapper = document.querySelector('.hero-slides-wrapper');
        const totalSlides = <?= count($slides) ?>;

        function showSlide(index) {
            if (index >= totalSlides) currentSlideIndex = 0;
            else if (index < 0) currentSlideIndex = totalSlides - 1;
            else currentSlideIndex = index;
            slidesWrapper.style.transform = `translateX(-${currentSlideIndex * 100}%)`;
        }

        function nextSlide() {
            showSlide(currentSlideIndex + 1);
        }

        function prevSlide() {
            showSlide(currentSlideIndex - 1);
        }
        if (totalSlides > 1) {
            setInterval(nextSlide, 6000);
        }
    </script>
<?php else: ?>
    <!-- Modern Fallback Hero Banner -->
    <section class="hero" style="padding: 70px 40px; background: linear-gradient(120deg, #f7f5ef 0%, #e9eee9 100%); border-radius: 16px; margin-bottom: 35px;">
        <div style="max-width: 650px;">
            <div class="eyebrow" style="text-transform: uppercase; letter-spacing: 2px; font-weight: 700; font-size: 12px; color: var(--green, #25483d); margin-bottom: 10px;">New Season Drops</div>
            <h1 style="font-size: 46px; font-family:'Playfair Display', serif; margin:0 0 14px 0; line-height: 1.1;">Wear what feels like you.</h1>
            <p class="muted" style="font-size: 16px; margin-bottom: 28px; color: #555; line-height:1.6;">Discover versatile fits, modern wardrobe staples, and premium everyday quality crafted for ultimate style & confidence.</p>
            <div style="display:flex; gap:14px; flex-wrap:wrap;">
                <a class="button" href="#shop" style="padding: 13px 28px; border-radius:6px;">Explore Collection</a>
                <a class="button secondary" href="<?= e(url('index.php?category=new-arrivals')) ?>" style="padding: 13px 28px; border-radius:6px;">New Arrivals 🔥</a>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Dynamic Trust & Store Guarantees Bar -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 45px;">
    <div class="trust-badge-card">
        <span style="font-size: 24px; display:block; margin-bottom:6px;">🚀</span>
        <strong style="display:block; font-size: 15px; margin-bottom:3px;"><?= e($b1Title) ?></strong>
        <span class="muted small" style="color: #666; font-size: 13px;"><?= e($b1Sub) ?></span>
    </div>
    <div class="trust-badge-card">
        <span style="font-size: 24px; display:block; margin-bottom:6px;">💵</span>
        <strong style="display:block; font-size: 15px; margin-bottom:3px;"><?= e($b2Title) ?></strong>
        <span class="muted small" style="color: #666; font-size: 13px;"><?= e($b2Sub) ?></span>
    </div>
    <div class="trust-badge-card">
        <span style="font-size: 24px; display:block; margin-bottom:6px;">✨</span>
        <strong style="display:block; font-size: 15px; margin-bottom:3px;"><?= e($b3Title) ?></strong>
        <span class="muted small" style="color: #666; font-size: 13px;"><?= e($b3Sub) ?></span>
    </div>
    <div class="trust-badge-card">
        <span style="font-size: 24px; display:block; margin-bottom:6px;">🔄</span>
        <strong style="display:block; font-size: 15px; margin-bottom:3px;"><?= e($b4Title) ?></strong>
        <span class="muted small" style="color: #666; font-size: 13px;"><?= e($b4Sub) ?></span>
    </div>
</div>

<!-- Visual Category Navigation -->
<?php if ($categories && $q === '' && $cat === ''): ?>
    <div style="margin-bottom: 45px;">
        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom: 20px;">
            <div>
                <div class="eyebrow" style="text-transform: uppercase; font-size: 12px; letter-spacing: 1.5px; color: #888;">Curated Styles</div>
                <h2 style="margin: 4px 0 0 0; font-family:'Playfair Display', serif; font-size:28px;">Featured Categories</h2>
            </div>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 18px;">
            <?php foreach ($categories as $c): ?>
                <a class="category-card" href="<?= e(url('index.php?category=' . urlencode($c['slug']))) ?>">
                    <h4 style="margin: 0 0 6px 0; font-size: 17px; font-weight:600;"><?= e($c['name']) ?></h4>
                    <span class="muted small" style="color: #888; font-size: 13px;"><?= (int)$c['product_count'] ?> Products</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Catalog Header & Search Controls -->
<div class="section-heading" style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">
    <div>
        <div class="eyebrow" style="text-transform: uppercase; font-size: 12px; letter-spacing: 1.5px; color: #888;">The Collection</div>
        <h2 id="shop" style="margin: 4px 0 0 0; font-family:'Playfair Display', serif; font-size:32px;">
            <?= $cat ? 'Category: ' . e(ucfirst(str_replace('-', ' ', $cat))) : ($q ? 'Results for "' . e($q) . '"' : 'Featured Catalog') ?>
        </h2>
    </div>
    <form class="inline-form" method="get" style="display: flex; gap: 8px;">
        <?php if ($cat): ?>
            <input type="hidden" name="category" value="<?= e($cat) ?>">
        <?php endif; ?>
        <input name="q" placeholder="Search products..." value="<?= e($q) ?>" style="padding: 10px 14px; border: 1px solid #ddd; border-radius: 6px; min-width: 220px;">
        <button class="button" style="padding: 10px 18px; border-radius:6px;">Search</button>
    </form>
</div>

<!-- Category Filter Pills -->
<div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:30px;">
    <a class="button <?= ($cat === '' && $q === '') ? '' : 'secondary' ?>" href="<?= e(url('index.php#shop')) ?>" style="border-radius:20px; padding:8px 18px; font-size:13px;">All Items</a>
    <a class="button <?= ($cat === 'new-arrivals') ? '' : 'secondary' ?>" href="<?= e(url('index.php?category=new-arrivals#shop')) ?>" style="border-radius:20px; padding:8px 18px; font-size:13px;">🔥 New Arrivals</a>
    <?php foreach ($categories as $c): ?>
        <a class="button <?= ($cat === $c['slug']) ? '' : 'secondary' ?>" href="<?= e(url('index.php?category=' . urlencode($c['slug']) . '#shop')) ?>" style="border-radius:20px; padding:8px 18px; font-size:13px;">
            <?= e($c['name']) ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- Product Grid Listing -->
<?php if (!$products): ?>
    <div class="empty" style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 12px; border: 1px solid var(--line, #eee);">
        <h2 style="font-size: 22px; margin-bottom:8px;">No products found</h2>
        <p class="muted" style="margin-bottom:20px;">Try adjusting your search terms or filters to find what you are looking for.</p>
        <a class="button" href="<?= e(url('index.php#shop')) ?>" style="border-radius:6px;">View All Products</a>
    </div>
<?php else: ?>
    <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 24px;">
        <?php foreach ($products as $p): $imgSrc = $p['image_url'] ? (str_starts_with($p['image_url'], 'http') ? $p['image_url'] : url($p['image_url'])) : 'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?w=800';
            $discountPct = 0;
            if (!empty($p['compare_price']) && $p['compare_price'] > $p['price']) {
                $discountPct = round((($p['compare_price'] - $p['price']) / $p['compare_price']) * 100);
            }
        ?>
            <article class="product-card">
                <a class="product-image-container" href="<?= e(url('product.php?slug=' . urlencode($p['slug']))) ?>">
                    <img loading="lazy" src="<?= e($imgSrc) ?>" alt="<?= e($p['name']) ?>">
                    <?php if ($discountPct > 0): ?>
                        <span class="badge-discount">-<?= $discountPct ?>% OFF</span>
                    <?php endif; ?>
                    <?php if ((int)$p['stock'] < 1): ?>
                        <span class="badge-soldout">Sold Out</span>
                    <?php endif; ?>
                </a>

                <div class="product-info">
                    <h3>
                        <a href="<?= e(url('product.php?slug=' . urlencode($p['slug']))) ?>"><?= e($p['name']) ?></a>
                    </h3>

                    <div class="price-wrap">
                        <div>
                            <span class="price" style="font-size: 16px; font-weight: 700; color: var(--green, #25483d);"><?= money($p['price']) ?></span>
                            <?php if (!empty($p['compare_price']) && $p['compare_price'] > $p['price']): ?>
                                <span class="old-price" style="font-size: 13px; text-decoration: line-through; color: #999; margin-left: 6px;"><?= money($p['compare_price']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ((int)$p['stock'] > 0): ?>
                        <a class="quick-add-btn" href="<?= e(url('product.php?slug=' . urlencode($p['slug']))) ?>" style="text-align:center; text-decoration:none; display:block;">
                            View Item & Order
                        </a>
                    <?php else: ?>
                        <button class="quick-add-btn" disabled style="background:#ccc; cursor:not-allowed;">Out of Stock</button>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Dynamic Customer Testimonials -->
<?php if ($testimonials): ?>
    <section style="margin: 70px 0 30px 0; background: #fff; padding: 45px 30px; border-radius: 16px; border: 1px solid var(--line, #eee);">
        <div style="text-align: center; margin-bottom: 35px;">
            <div class="eyebrow" style="text-transform: uppercase; font-size: 12px; letter-spacing: 1.5px; color: #888;">Community Feedback</div>
            <h2 style="font-family:'Playfair Display', serif; font-size:32px; margin-top:4px;">Loved By Our Customers</h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 22px;">
            <?php foreach ($testimonials as $t): ?>
                <div style="background: #fdfdfd; padding: 25px; border-radius: 12px; border: 1px solid #f0f0f0; box-shadow:0 4px 12px rgba(0,0,0,0.02);">
                    <div style="color: #f39c12; margin-bottom: 10px; font-size:16px;"><?= str_repeat('★', (int)$t['rating']) ?></div>
                    <p style="font-size: 14px; color: #444; line-height: 1.6; margin-bottom: 15px;">"<?= e($t['review_text']) ?>"</p>
                    <strong style="font-size:14px; color: var(--green, #25483d);">— <?= e($t['customer_name']) ?></strong>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<!-- Social Community Grid -->
<?php
$socialItems = db()->query('SELECT * FROM social_links WHERE active=1 ORDER BY sort_order ASC, id DESC LIMIT 6')->fetchAll();
if ($socialItems):
?>
    <section style="margin: 50px 0 20px 0; background: #fff; padding: 40px 30px; border-radius: 16px; border: 1px solid var(--line, #eee);">
        <div style="text-align: center; margin-bottom: 30px;">
            <div class="eyebrow" style="text-transform: uppercase; font-size: 12px; letter-spacing: 1.5px; color: #888;">Follow The Feed</div>
            <h2 style="font-family:'Playfair Display', serif; font-size:30px; margin-top:4px;">#DiverseStyle on Social</h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 16px;">
            <?php foreach ($socialItems as $s): $img = $s['image_url'] ? (str_starts_with($s['image_url'], 'http') ? $s['image_url'] : url($s['image_url'])) : 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=500'; ?>
                <a href="<?= e($s['post_url']) ?>" target="_blank" rel="noopener" style="display:block; text-decoration:none; color:inherit; border-radius:10px; overflow:hidden; border:1px solid #eee; transition:transform 0.25s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
                    <div style="aspect-ratio:1/1; overflow:hidden;">
                        <img src="<?= e($img) ?>" alt="<?= e($s['platform']) ?>" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    <div style="padding:10px; background:#fff; text-align:center;">
                        <strong style="display:block; font-size:13px;"><?= e($s['platform']) ?></strong>
                        <?php if ($s['title']): ?>
                            <span class="muted small" style="font-size:11px; color:#888; display:block; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;"><?= e($s['title']) ?></span>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/app/footer.php'; ?>