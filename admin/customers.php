<?php require_once __DIR__ . '/_init.php';

$search = trim((string)($_GET['q'] ?? ''));

$sql = "
    SELECT 
        customer_email,
        customer_name,
        customer_phone,
        city,
        COUNT(id) as total_orders,
        SUM(total) as lifetime_spend,
        MAX(created_at) as last_order_date
    FROM orders
    WHERE status NOT IN ('cancelled', 'returned')
";

$params = [];

if ($search !== '') {
    $sql .= " AND (customer_name LIKE ? OR customer_email LIKE ? OR customer_phone LIKE ? OR city LIKE ?)";
    $params = ["%$search%", "%$search%", "%$search%", "%$search%"];
}

$sql .= " GROUP BY customer_email, customer_phone ORDER BY lifetime_spend DESC LIMIT 200";

$stmt = db()->prepare($sql);
$stmt->execute($params);
$customers = $stmt->fetchAll();

admin_header('Customer Management'); ?>

<!-- Search Bar -->
<div class="panel" style="margin-bottom: 22px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <h2>Customer Directory</h2>
        <form method="get" class="inline-form" style="display:flex; gap:8px;">
            <input name="q" value="<?= e($search) ?>" placeholder="Search name, email, phone, city..." style="padding: 6px 12px; min-width: 250px;">
            <button class="button">Search</button>
            <?php if ($search !== ''): ?>
                <a class="button secondary" href="<?= e(url('admin/customers.php')) ?>">Clear</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Customers Table -->
<div class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>City / Location</th>
                    <th>Total Orders</th>
                    <th>Lifetime Spend</th>
                    <th>Last Active</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $c): ?>
                    <tr>
                        <td>
                            <b><?= e($c['customer_name']) ?></b><br>
                            <span class="muted small"><?= e($c['customer_email']) ?></span>
                        </td>
                        <td><?= e($c['customer_phone']) ?></td>
                        <td><?= e($c['city'] ?: '—') ?></td>
                        <td>
                            <span class="status" style="padding:2px 8px; font-weight:bold; background:#e8f8f5; color:#27ae60;">
                                <?= (int)$c['total_orders'] ?> orders
                            </span>
                        </td>
                        <td><b><?= money($c['lifetime_spend']) ?></b></td>
                        <td><?= date('M d, Y', strtotime($c['last_order_date'])) ?></td>
                        <td>
                            <a class="button secondary" style="padding:4px 8px; font-size:12px;" href="<?= e(url('admin/contacts.php')) ?>">Contact</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$customers): ?>
                    <tr>
                        <td colspan="7" style="text-align:center; padding:20px;">No customer records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <p class="muted small" style="margin-top:15px;">Customer spending excludes cancelled or returned orders.</p>
</div>

<?php admin_footer(); ?>