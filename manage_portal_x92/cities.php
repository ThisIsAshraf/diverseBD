<?php require_once __DIR__ . '/_init.php';
admin_required();
require_permission('settings');

$title = 'Delivery Locations & Shipping Rates';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'save') {
        $id     = (int)($_POST['id'] ?? 0);
        $name   = trim((string)($_POST['city_name'] ?? ''));
        $zone   = (string)($_POST['zone_code'] ?? 'outside_dhaka');
        $cost   = max(0, (float)($_POST['shipping_cost'] ?? 120.00));

        if ($name === '') {
            flash('error', 'City / Division name is required.');
            redirect('manage_portal_x92/cities.php');
        }

        try {
            if ($id > 0) {
                $st = db()->prepare('UPDATE shipping_cities SET city_name = ?, zone_code = ?, shipping_cost = ? WHERE id = ?');
                $st->execute([$name, $zone, $cost, $id]);
                flash('success', 'Delivery location updated.');
            } else {
                $st = db()->prepare('INSERT INTO shipping_cities (city_name, zone_code, shipping_cost) VALUES (?, ?, ?)');
                $st->execute([$name, $zone, $cost]);
                flash('success', 'New delivery location added.');
            }
        } catch (Throwable $e) {
            flash('error', 'Could not save location. City name must be unique.');
        }
        redirect('manage_portal_x92/cities.php');
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        db()->prepare('DELETE FROM shipping_cities WHERE id = ?')->execute([$id]);
        flash('success', 'Delivery location removed.');
        redirect('manage_portal_x92/cities.php');
    }
}

$edit = null;
if (isset($_GET['edit'])) {
    $st = db()->prepare('SELECT * FROM shipping_cities WHERE id = ?');
    $st->execute([(int)$_GET['edit']]);
    $edit = $st->fetch();
}

$cities = db()->query('SELECT * FROM shipping_cities ORDER BY zone_code ASC, city_name ASC')->fetchAll();
admin_header($title);
?>

<div class="panel" style="margin-bottom: 22px;">
    <h2><?= $edit ? 'Edit Location / Rate' : 'Add New Delivery Location' ?></h2>
    <form method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">

        <div class="form-grid">
            <div class="field">
                <label>City / Division Name *</label>
                <input name="city_name" required value="<?= e($edit['city_name'] ?? '') ?>" placeholder="e.g. Cumilla, Gazipur">
            </div>

            <div class="field">
                <label>Delivery Zone *</label>
                <select name="zone_code" required>
                    <option value="inside_dhaka" <?= ($edit['zone_code'] ?? '') === 'inside_dhaka' ? 'selected' : '' ?>>Inside Dhaka (Metropolitan)</option>
                    <option value="outside_dhaka" <?= ($edit['zone_code'] ?? '') === 'outside_dhaka' ? 'selected' : '' ?>>Outside Dhaka (District / Division)</option>
                </select>
            </div>

            <div class="field">
                <label>Shipping Delivery Fee (৳) *</label>
                <input type="number" step="0.01" min="0" name="shipping_cost" required value="<?= e((string)($edit['shipping_cost'] ?? 120.00)) ?>">
            </div>
        </div>

        <p style="margin-top: 15px;">
            <button class="button">Save Location</button>
            <?php if ($edit): ?>
                <a class="button secondary" href="<?= e(url('manage_portal_x92/cities.php')) ?>">Cancel Edit</a>
            <?php endif; ?>
        </p>
    </form>
</div>

<div class="panel">
    <h2>Active Delivery Locations & Rates</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>City / Division</th>
                    <th>Zone</th>
                    <th>Delivery Fee</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cities as $c): ?>
                    <tr>
                        <td><b><?= e($c['city_name']) ?></b></td>
                        <td>
                            <span class="status" style="padding:2px 8px; font-weight:bold; background: <?= $c['zone_code'] === 'inside_dhaka' ? '#e8f8f5; color:#27ae60;' : '#fef9e7; color:#f39c12;' ?>">
                                <?= $c['zone_code'] === 'inside_dhaka' ? 'Inside Dhaka' : 'Outside Dhaka' ?>
                            </span>
                        </td>
                        <td><b><?= money($c['shipping_cost']) ?></b></td>
                        <td style="display:flex; gap:6px;">
                            <a class="button secondary" style="padding:4px 8px; font-size:12px;" href="<?= e(url('manage_portal_x92/cities.php?edit=' . $c['id'])) ?>">Edit</a>
                            <form method="post" style="display:inline; margin:0;" onsubmit="return confirm('Delete this delivery location?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                <button class="button secondary" style="padding:4px 8px; font-size:12px; background:#fde8e8; color:#e74c3c;">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php admin_footer(); ?>