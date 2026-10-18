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

$pageTitle = 'Shop clothing & essentials';
require_once __DIR__ . '/app/header.php';
?>

<!-- Dynamic Hero Banner Slider -->
<?php if ($slides && $q === '' && $cat === ''): ?>
    <section class="hero-slider-container" style="position:relative; overflow:hidden; border-radius:12px; margin-bottom:30px; background:#f8f9fa;">
        <div class="hero-slides-wrapper" style="display:flex; transition:transform 0.5s ease-in-out;">
            <?php foreach ($slides as $idx => $sl): $bgImage = !empty($sl['image_url']) ? 'url(' . e(url($sl['image_url'])) . ')' : 'none';
            ?>
                <div class="hero-slide-item" style="min-width:100%; box-sizing:border-box; padding:60px 40px; background-image:<?= $bgImage ?>; background-size:cover; background-position:center; position:relative;">
                    <div style="max-width:600px; background:rgba(255,255,255,0.85); padding:25px; border-radius:8px; backdrop-filter:blur(4px);">
                        <?php if ($sl['eyebrow']): ?>
                            <div class="eyebrow" style="text-transform:uppercase; letter-spacing:1.5px; font-weight:600; font-size:13px; color:#666; margin-bottom:8px;"><?= e($sl['eyebrow']) ?></div>
                        <?php endif; ?>
                        <h1 style="font-size:38px; margin-bottom:12px; line-height:1.15;"><?= e($sl['title']) ?></h1>
                        <?php if ($sl['subtitle']): ?>
                            <p class="muted" style="font-size:15px; margin-bottom:20px; color:#444;"><?= e($sl['subtitle']) ?></p>
                        <?php endif; ?>
                        <a class="button" href="<?= e(url($sl['button_url'] ?: 'index.php')) ?>" style="padding:10px 22px;"><?= e($sl['button_text'] ?: 'Explore Collection') ?></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if (count($slides) > 1): ?>
            <button onclick="prevSlide()" style="position:absolute; left:15px; top:50%; transform:translateY(-50%); background:rgba(255,255,255,0.8); border:none; width:36px; height:36px; border-radius:50%; cursor:pointer; font-weight:bold;">❮</button>
            <button onclick="nextSlide()" style="position:absolute; right:15px; top:50%; transform:translateY(-50%); background:rgba(255,255,255,0.8); border:none; width:36px; height:36px; border-radius:50%; cursor:pointer; font-weight:bold;">❯</button>
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
            setInterval(nextSlide, 5000);
        }
    </script>
<?php else: ?>
    <!-- Fallback Hero Banner if no slides exist -->
    <section class="hero" style="padding: 60px 0; text-align: left; background: var(--panel-bg, #f8f9fa); border-radius: 12px; margin-bottom: 30px;">
        <div style="max-width: 650px; padding: 0 20px;">
            <div class="eyebrow" style="text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600; font-size: 13px; color: #666; margin-bottom: 8px;">New Season Essentials</div>
            <h1 style="font-size: 42px; margin-bottom: 12px; line-height: 1.15;">Wear what feels like you.</h1>
            <p class="muted" style="font-size: 16px; margin-bottom: 24px; color: #555;">Discover versatile fits, modern wardrobe staples, and premium everyday quality designed for confidence.</p>
            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                <a class="button" href="#shop" style="padding: 12px 24px;">Explore Collection</a>
                <a class="button secondary" href="<?= e(url('index.php?category=new-arrivals')) ?>" style="padding: 12px 24px;">New Arrivals</a>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Trust & Value Badges Bar -->
<div class="trust-bar" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 40px; background: #fff; padding: 20px; border-radius: 8px; border: 1px solid var(--line, #eee); text-align: center;">
    <div>
        <strong style="display:block; font-size: 15px;">🚀 Fast Delivery</strong>
        <span class="muted small" style="color: #666; font-size: 13px;">Quick processing & dispatch across Bangladesh</span>
    </div>
    <div>
        <strong style="display:block; font-size: 15px;">💵 Cash on Delivery</strong>
        <span class="muted small" style="color: #666; font-size: 13px;">Pay safely when your order arrives</span>
    </div>
    <div>
        <strong style="display:block; font-size: 15px;">✨ Quality Assured</strong>
        <span class="muted small" style="color: #666; font-size: 13px;">Crafted with premium fabrics & details</span>
    </div>
</div>

<!-- Visual Category Grid -->
<?php if ($categories && $q === '' && $cat === ''): ?>
    <div style="margin-bottom: 40px;">
        <div class="eyebrow" style="text-transform: uppercase; font-size: 12px; letter-spacing: 1px; color: #777;">Browse by style</div>
        <h2 style="margin-top: 4px; margin-bottom: 18px;">Featured Categories</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px;">
            <?php foreach ($categories as $c): ?>
                <a href="<?= e(url('index.php?category=' . urlencode($c['slug']))) ?>" style="text-decoration: none; color: inherit; background: #fff; border: 1px solid var(--line, #eee); padding: 18px 15px; border-radius: 8px; transition: transform 0.2s, box-shadow 0.2s; display: block;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='none'">
                    <h4 style="margin: 0 0 6px 0; font-size: 16px;"><?= e($c['name']) ?></h4>
                    <span class="muted small" style="color: #888; font-size: 12px;"><?= (int)$c['product_count'] ?> Products</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Catalog Header & Search -->
<div class="section-heading" style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">
    <div>
        <div class="eyebrow" style="text-transform: uppercase; font-size: 12px; letter-spacing: 1px; color: #777;">The Collection</div>
        <h2 id="shop" style="margin: 4px 0 0 0;"><?= $cat ? 'Category: ' . e(ucfirst(str_replace('-', ' ', $cat))) : ($q ? 'Search results for "' . e($q) . '"' : 'Find Your Fit') ?></h2>
    </div>
    <form class="inline-form" method="get" style="display: flex; gap: 8px;">
        <?php if ($cat): ?>
            <input type="hidden" name="category" value="<?= e($cat) ?>">
        <?php endif; ?>
        <input name="q" placeholder="Search styles…" value="<?= e($q) ?>" style="padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px;">
        <button class="button">Search</button>
    </form>
</div>

<!-- Category Filter Pills -->
<div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:25px;">
    <a class="button <?= ($cat === '' && $q === '') ? '' : 'secondary' ?>" href="<?= e(url('index.php')) ?>">All items</a>
    <a class="button <?= ($cat === 'new-arrivals') ? '' : 'secondary' ?>" href="<?= e(url('index.php?category=new-arrivals')) ?>">🔥 New Arrivals</a>
    <?php foreach ($categories as $c): ?>
        <a class="button <?= ($cat === $c['slug']) ? '' : 'secondary' ?>" href="<?= e(url('index.php?category=' . urlencode($c['slug']))) ?>">
            <?= e($c['name']) ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- Product Grid Listing -->
<?php if (!$products): ?>
    <div class="empty" style="text-align: center; padding: 50px 20px; background: #fff; border-radius: 8px; border: 1px solid var(--line, #eee);">
        <h2>No products found</h2>
        <p class="muted">Try adjusting your search terms or filter to find what you are looking for.</p>
        <a class="button" href="<?= e(url('index.php')) ?>">View all products</a>
    </div>
<?php else: ?>
    <div class="product-grid">
        <?php foreach ($products as $p): $imgSrc = $p['image_url'] ? (str_starts_with($p['image_url'], 'http') ? $p['image_url'] : url($p['image_url'])) : 'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?w=800';
        ?>
            <article class="product-card">
                <a class="product-image" href="<?= e(url('product.php?slug=' . urlencode($p['slug']))) ?>">
                    <img loading="lazy" src="<?= e($imgSrc) ?>" alt="<?= e($p['name']) ?>">
                </a>
                <h3><a href="<?= e(url('product.php?slug=' . urlencode($p['slug']))) ?>"><?= e($p['name']) ?></a></h3>
                <div class="price">
                    <?= money($p['price']) ?>
                    <?php if (!empty($p['compare_price']) && $p['compare_price'] > $p['price']): ?>
                        <span class="old-price"><?= money($p['compare_price']) ?></span>
                    <?php endif; ?>
                </div>
                <?php if ((int)$p['stock'] < 1): ?>
                    <span class="tag">Sold out</span>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- DYNAMIC CUSTOMER TESTIMONIALS SECTION -->
<?php if ($testimonials): ?>
    <div style="margin: 60px 0 20px 0; background: #fff; padding: 35px 25px; border-radius: 12px; border: 1px solid var(--line, #eee);">
        <div style="text-align: center; margin-bottom: 30px;">
            <div class="eyebrow" style="text-transform: uppercase; font-size: 12px; letter-spacing: 1px; color: #777;">Real Feedback</div>
            <h2>What Our Customers Say</h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
            <?php foreach ($testimonials as $t): ?>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <div style="color: #f39c12; margin-bottom: 8px;"><?= str_repeat('★', (int)$t['rating']) ?></div>
                    <p style="font-size: 14px; color: #444; line-height: 1.5; margin-bottom: 12px;">"<?= e($t['review_text']) ?>"</p>
                    <strong>— <?= e($t['customer_name']) ?></strong>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<!-- SOCIAL MEDIA GRID SECTION -->
<?php
$socialItems = db()->query('SELECT * FROM social_links WHERE active=1 ORDER BY sort_order ASC, id DESC LIMIT 6')->fetchAll();
if ($socialItems):
?>
    <div style="margin: 60px 0 20px 0; background: #fff; padding: 35px 25px; border-radius: 12px; border: 1px solid var(--line, #eee);">
        <div style="text-align: center; margin-bottom: 25px;">
            <div class="eyebrow" style="text-transform: uppercase; font-size: 12px; letter-spacing: 1px; color: #777;">Follow Us</div>
            <h2 style="margin-top:4px;">Join Our Social Community</h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 15px;">
            <?php foreach ($socialItems as $s):
                $img = $s['image_url'] ? (str_starts_with($s['image_url'], 'http') ? $s['image_url'] : url($s['image_url'])) : 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=500';
            ?>
                <a href="<?= e($s['post_url']) ?>" target="_blank" style="display:block; text-decoration:none; color:inherit; position:relative; overflow:hidden; border-radius:8px; border:1px solid #eee;">
                    <div style="aspect-ratio:1/1; overflow:hidden;">
                        <img src="<?= e($img) ?>" alt="<?= e($s['platform']) ?>" style="width:100%; height:100%; object-fit:cover; transition:transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    </div>
                    <div style="padding:10px; background:#fff; text-align:center;">
                        <strong style="display:block; font-size:13px;"><?= e($s['platform']) ?></strong>
                        <?php if ($s['title']): ?>
                            <span class="muted small" style="font-size:11px; display:block; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;"><?= e($s['title']) ?></span>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/app/footer.php'; ?>