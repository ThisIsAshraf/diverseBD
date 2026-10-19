<?php require_once __DIR__ . '/app/bootstrap.php';
$items = cart_items();
if (!$items) {
    flash('error', 'Your bag is empty.');
    redirect('cart.php');
}
$subtotal = cart_subtotal($items);
$errors = [];
$couponCode = (string)($_SESSION['coupon_code'] ?? '');
$discount = 0;
$coupon = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (isset($_POST['apply_coupon'])) {
        $code = strtoupper(trim((string)($_POST['coupon_code'] ?? '')));
        $s = db()->prepare('SELECT * FROM coupons WHERE code=? AND active=1 AND (starts_at IS NULL OR starts_at<=NOW()) AND (ends_at IS NULL OR ends_at>=NOW()) AND (usage_limit IS NULL OR used_count<usage_limit)');
        $s->execute([$code]);
        $c = $s->fetch();
        if ($c && $subtotal >= (float)$c['min_order']) {
            $_SESSION['coupon_code'] = $code;
            flash('success', 'Coupon applied.');
        } else {
            unset($_SESSION['coupon_code']);
            flash('error', 'Coupon is invalid, expired, or does not meet the minimum order.');
        }
        redirect('checkout.php');
    }

    $name = trim((string)($_POST['customer_name'] ?? ''));
    $email = trim((string)($_POST['customer_email'] ?? ''));
    $phone = trim((string)($_POST['customer_phone'] ?? ''));
    $address = trim((string)($_POST['address'] ?? ''));
    $city = trim((string)($_POST['city'] ?? ''));
    $notes = trim((string)($_POST['notes'] ?? ''));

    if (mb_strlen($name) < 2) $errors[] = 'Please enter your full name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if (mb_strlen($phone) < 7) $errors[] = 'Please enter a valid phone number.';
    if (mb_strlen($address) < 8) $errors[] = 'Please enter your delivery address.';
    if ($city === '') $errors[] = 'Please enter your city or area.';

    foreach ($items as $i) {
        $s = db()->prepare('SELECT stock,active FROM products WHERE id=?');
        $s->execute([$i['id']]);
        $live = $s->fetch();
        if (!$live || !$live['active'] || (int)$live['stock'] < (int)$i['qty']) $errors[] = 'Stock changed for ' . $i['name'] . '. Please update your bag.';
    }

    if (!$errors) {
        $subtotal = cart_subtotal($items);
        $coupon = null;
        $discount = 0;
        $code = (string)($_SESSION['coupon_code'] ?? '');
        if ($code !== '') {
            $s = db()->prepare('SELECT * FROM coupons WHERE code=? AND active=1 AND (starts_at IS NULL OR starts_at<=NOW()) AND (ends_at IS NULL OR ends_at>=NOW()) AND (usage_limit IS NULL OR used_count<usage_limit)');
            $s->execute([$code]);
            $coupon = $s->fetch();
            if ($coupon && $subtotal >= (float)$coupon['min_order']) {
                $discount = $coupon['discount_type'] === 'percent' ? $subtotal * (float)$coupon['discount_value'] / 100 : (float)$coupon['discount_value'];
                $discount = min($subtotal, $discount);
                if ($coupon['max_discount'] !== null) $discount = min($discount, (float)$coupon['max_discount']);
            } else $coupon = null;
        }
        $shipping = 0;
        $total = max(0, $subtotal - $discount) + $shipping;
        $orderNo = 'DVBD' . date('ymd') . strtoupper(bin2hex(random_bytes(3)));

        try {
            $pdo = db();
            $pdo->beginTransaction();
            $ins = $pdo->prepare('INSERT INTO orders(order_number,customer_name,customer_email,customer_phone,address,city,notes,payment_method,status,subtotal,discount_amount,shipping_amount,total,coupon_code) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $ins->execute([$orderNo, $name, $email, $phone, $address, $city, $notes, 'COD', 'pending', $subtotal, $discount, $shipping, $total, $coupon['code'] ?? null]);
            $orderId = (int)$pdo->lastInsertId();

            $line = $pdo->prepare('INSERT INTO order_items(order_id,product_id,product_name,sku,quantity,unit_price,line_total) VALUES(?,?,?,?,?,?,?)');
            $stockUpdate = $pdo->prepare('UPDATE products SET stock=stock-? WHERE id=? AND stock>=?');

            foreach ($items as $i) {
                $stockUpdate->execute([$i['qty'], $i['id'], $i['qty']]);
                if ($stockUpdate->rowCount() !== 1) throw new RuntimeException('Inventory changed during checkout.');
                $line->execute([$orderId, $i['id'], $i['name'], $i['sku'] ?? null, $i['qty'], $i['price'], $i['line_total']]);
            }

            $pdo->prepare('INSERT INTO order_status_history(order_id,status,note) VALUES(?,?,?)')->execute([$orderId, 'pending', 'Order placed by customer']);
            if ($coupon) $pdo->prepare('UPDATE coupons SET used_count=used_count+1 WHERE id=?')->execute([$coupon['id']]);
            $pdo->commit();

            $s = $pdo->prepare('SELECT * FROM orders WHERE id=?');
            $s->execute([$orderId]);
            $order = $s->fetch();

            $s = $pdo->prepare('SELECT product_name name,quantity,unit_price price,line_total FROM order_items WHERE order_id=?');
            $s->execute([$orderId]);
            $mailItems = $s->fetchAll();

            // 1. Send Order Confirmation Receipt to Customer (No Invoice attached)
            email_customer_order_placed($order, $mailItems);

            // 2. Send New Order Alert to Admin
            email_admin_new_order($order, $mailItems);

            $_SESSION['last_order_number'] = $orderNo;
            unset($_SESSION['cart'], $_SESSION['coupon_code']);
            redirect('order-success.php');
        } catch (Throwable $e) {
            if (db()->inTransaction()) db()->rollBack();
            error_log('Checkout error: ' . $e->getMessage());
            $errors[] = 'We could not place the order. Please review your bag and try again.';
        }
    }
}

if (isset($_SESSION['coupon_code'])) {
    $s = db()->prepare('SELECT * FROM coupons WHERE code=? AND active=1 AND (starts_at IS NULL OR starts_at<=NOW()) AND (ends_at IS NULL OR ends_at>=NOW()) AND (usage_limit IS NULL OR used_count<usage_limit)');
    $s->execute([$_SESSION['coupon_code']]);
    $coupon = $s->fetch();
    if ($coupon && $subtotal >= (float)$coupon['min_order']) {
        $discount = $coupon['discount_type'] === 'percent' ? $subtotal * (float)$coupon['discount_value'] / 100 : (float)$coupon['discount_value'];
        $discount = min($subtotal, $discount);
        if ($coupon['max_discount'] !== null) $discount = min($discount, (float)$coupon['max_discount']);
    } else {
        unset($_SESSION['coupon_code']);
        $coupon = null;
    }
}

$pageTitle = 'Checkout';
require_once __DIR__ . '/app/header.php'; ?>
<h1 class="page-title">Checkout</h1>
<p class="muted">Cash on delivery · No online payment required</p>

<?php if ($errors): ?>
    <div class="notice error">
        <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="checkout-layout">
    <form method="post" class="panel">
        <?= csrf_field() ?>
        <h2>Delivery details</h2>
        <div class="field"><label>Full name *</label><input name="customer_name" required value="<?= e((string)($_POST['customer_name'] ?? '')) ?>"></div>
        <div class="form-grid">
            <div class="field"><label>Email address *</label><input type="email" name="customer_email" required value="<?= e((string)($_POST['customer_email'] ?? '')) ?>"></div>
            <div class="field"><label>Phone number *</label><input name="customer_phone" required value="<?= e((string)($_POST['customer_phone'] ?? '')) ?>"></div>
        </div>
        <div class="field"><label>Full delivery address *</label><textarea name="address" required><?= e((string)($_POST['address'] ?? '')) ?></textarea></div>
        <div class="field"><label>City / Area *</label><input name="city" required value="<?= e((string)($_POST['city'] ?? '')) ?>"></div>
        <div class="field"><label>Order notes (optional)</label><textarea name="notes"><?= e((string)($_POST['notes'] ?? '')) ?></textarea></div>
        <div class="notice success">Payment method: <b>Cash on delivery</b>. Pay the delivery person when your order arrives.</div>
        <button style="width:100%">Place order · <?= money($subtotal - $discount) ?></button>
    </form>
    <aside class="panel">
        <h2>Order summary</h2>
        <?php foreach ($items as $i): ?>
            <div style="display:flex;justify-content:space-between;gap:10px;padding:12px 0;border-bottom:1px solid var(--line)"><span><?= e($i['name']) ?> × <?= (int)$i['qty'] ?></span><b><?= money($i['line_total']) ?></b></div>
        <?php endforeach; ?>
        <form method="post" style="margin:18px 0">
            <?= csrf_field() ?>
            <div class="field"><label>Discount code</label><input name="coupon_code" placeholder="Enter coupon" value="<?= e((string)($coupon['code'] ?? '')) ?>"></div>
            <button class="button secondary" name="apply_coupon" value="1">Apply coupon</button>
            <?php if ($coupon): ?><button class="button secondary" name="remove_coupon" value="1" formaction="<?= e(url('remove-coupon.php')) ?>">Remove</button><?php endif; ?>
        </form>
        <div style="display:flex;justify-content:space-between;padding:8px 0"><span>Subtotal</span><span><?= money($subtotal) ?></span></div>
        <div style="display:flex;justify-content:space-between;padding:8px 0"><span>Discount</span><span>-<?= money($discount) ?></span></div>
        <div style="display:flex;justify-content:space-between;padding:8px 0"><span>Delivery</span><span>Free</span></div>
        <hr>
        <div style="display:flex;justify-content:space-between;font-size:18px"><b>Total due</b><b><?= money($subtotal - $discount) ?></b></div>
    </aside>
</div>
<?php require_once __DIR__ . '/app/footer.php'; ?>