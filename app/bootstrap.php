<?php

declare(strict_types=1);

// Tiny .env reader, no extra dependency required.
$envFile = dirname(__DIR__) . '/.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $value = trim($value);
        if (strlen($value) >= 2 && (($value[0] === '"' && str_ends_with($value, '"')) || ($value[0] === "'" && str_ends_with($value, "'")))) $value = substr($value, 1, -1);
        $_ENV[trim($key)] = $value;
    }
}

if (!function_exists('envv')) {
    function envv(string $key, string $default = ''): string
    {
        return (string)($_ENV[$key] ?? getenv($key) ?: $default);
    }
}

date_default_timezone_set(envv('APP_TIMEZONE', 'Asia/Dhaka'));
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

if (!function_exists('db')) {
    function db(): PDO
    {
        static $pdo = null;
        if ($pdo instanceof PDO) return $pdo;
        $dsn = 'mysql:host=' . envv('DB_HOST', '127.0.0.1') . ';port=' . envv('DB_PORT', '3306') . ';dbname=' . envv('DB_NAME', 'diverse') . ';charset=utf8mb4';
        $pdo = new PDO($dsn, envv('DB_USER', 'root'), envv('DB_PASS', ''), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]);
        return $pdo;
    }
}

if (!function_exists('e')) {
    function e(?string $v): string
    {
        return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('site_setting')) {
    function site_setting(string $key, string $default = ''): string
    {
        static $settings = null;
        if ($settings === null) {
            $settings = [];
            try {
                foreach (db()->query('SELECT setting_key, setting_value FROM site_settings')->fetchAll() as $row) {
                    $settings[(string)$row['setting_key']] = (string)($row['setting_value'] ?? '');
                }
            } catch (Throwable $e) {
                // Keep environment/default branding available if settings are not installed yet.
            }
        }
        return (string)($settings[$key] ?? $default);
    }
}

if (!function_exists('site_setting_save')) {
    function site_setting_save(string $key, string $value): void
    {
        $st = db()->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
        $st->execute([$key, $value]);
    }
}

if (!function_exists('store_name')) {
    function store_name(): string
    {
        return site_setting('site_name', envv('APP_NAME', 'Diverse'));
    }
}

if (!function_exists('brand_html')) {
    function brand_html(): string
    {
        $name = e(store_name());
        $logo = trim(site_setting('logo_url', ''));
        if ($logo !== '') return '<img class="brand-logo" src="' . e(url($logo)) . '" alt="' . $name . ' logo">';
        return $name;
    }
}

if (!function_exists('money')) {
    function money(float|int|string $amount): string
    {
        $symbol = site_setting('currency_symbol', envv('CURRENCY', '৳'));
        return e($symbol) . number_format((float)$amount, 2);
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        return rtrim(envv('APP_URL', 'http://localhost/diverse'), '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): never
    {
        header('Location: ' . url($path));
        exit;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (empty($_SESSION['_csrf'])) $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        return $_SESSION['_csrf'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('verify_csrf')) {
    function verify_csrf(): void
    {
        if (!hash_equals($_SESSION['_csrf'] ?? '', (string)($_POST['_csrf'] ?? ''))) {
            http_response_code(419);
            exit('Invalid session token. Refresh and try again.');
        }
    }
}

if (!function_exists('flash')) {
    function flash(string $key, ?string $value = null): ?string
    {
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return null;
        }
        $v = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $v;
    }
}

if (!function_exists('cart_count')) {
    function cart_count(): int
    {
        $n = 0;
        foreach (($_SESSION['cart'] ?? []) as $qty) $n += (int)$qty;
        return $n;
    }
}

if (!function_exists('cart_items')) {
    function cart_items(): array
    {
        $cart = $_SESSION['cart'] ?? [];
        if (!$cart) return [];
        $ids = array_values(array_filter(array_map('intval', array_keys($cart)), fn($id) => $id > 0));
        if (!$ids) return [];
        $marks = implode(',', array_fill(0, count($ids), '?'));
        $st = db()->prepare("SELECT id,sku,name,slug,price,compare_price,image_url,stock,active FROM products WHERE id IN ($marks)");
        $st->execute($ids);
        $out = [];
        foreach ($st->fetchAll() as $p) {
            $qty = max(1, (int)($cart[$p['id']] ?? 1));
            $p['qty'] = min($qty, (int)$p['stock']);
            $p['line_total'] = $p['qty'] * (float)$p['price'];
            if ($p['qty'] > 0 && $p['active']) $out[] = $p;
        }
        return $out;
    }
}

if (!function_exists('cart_subtotal')) {
    function cart_subtotal(array $items): float
    {
        return array_sum(array_map(fn($i) => (float)$i['line_total'], $items));
    }
}

if (!function_exists('admin_required')) {
    function admin_required(): void
    {
        if (empty($_SESSION['admin_id'])) {
            redirect('admin/login.php');
        }

        try {
            $check = db()->prepare('SELECT id FROM admin_sessions WHERE id = ? AND admin_id = ?');
            $check->execute([session_id(), $_SESSION['admin_id']]);

            if (!$check->fetch()) {
                unset($_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['admin_email']);
                flash('error', 'Your session was ended because another device signed in or it was revoked.');
                redirect('admin/login.php');
            }

            db()->prepare('UPDATE admin_sessions SET last_activity = NOW() WHERE id = ?')->execute([session_id()]);
        } catch (Throwable $e) {
            // Fallback if admin_sessions table is not installed yet
        }
    }
}

if (!function_exists('send_store_email')) {
    function send_store_email(string $to, string $name, string $subject, string $html): bool
    {
        $autoload = dirname(__DIR__) . '/vendor/autoload.php';

        // Helper function to prefer trimmed DB value, falling back to .env if empty
        $getSetting = function (string $key, string $envKey, string $default = ''): string {
            $dbVal = trim(site_setting($key, ''));
            return $dbVal !== '' ? $dbVal : envv($envKey, $default);
        };

        $mailHost = $getSetting('mail_host', 'MAIL_HOST', 'smtp.gmail.com');
        $mailPort = (int)$getSetting('mail_port', 'MAIL_PORT', '587');
        $mailEnc  = $getSetting('mail_encryption', 'MAIL_ENCRYPTION', 'tls');
        $mailUser = $getSetting('mail_username', 'MAIL_USERNAME', '');
        $mailPass = $getSetting('mail_password', 'MAIL_PASSWORD', '');
        $fromAddr = $getSetting('mail_from_address', 'MAIL_FROM_ADDRESS', $mailUser);
        $fromName = $getSetting('mail_from_name', 'MAIL_FROM_NAME', store_name());

        if (!is_file($autoload) || $mailUser === '' || $mailPass === '') {
            error_log('Email skipped: Missing mail credentials.');
            return false;
        }

        require_once $autoload;

        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = $mailHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $mailUser;
            $mail->Password   = $mailPass;
            $mail->Port       = $mailPort;
            $mail->SMTPSecure = $mailEnc;
            $mail->CharSet    = 'UTF-8';
            $mail->setFrom($fromAddr, $fromName);
            $mail->addAddress($to, $name);
            $mail->isHTML(true);
            $mail->Subject    = $subject;
            $mail->Body       = $html;
            $mail->AltBody    = strip_tags(str_replace(['<br>', '<br/>', '</p>'], "\n", $html));
            $mail->send();
            return true;
        } catch (Throwable $e) {
            error_log('Email send failed: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('email_order_customer')) {
    function email_order_customer(array $order, array $items): void
    {
        $rows = '';
        foreach ($items as $i) $rows .= '<tr><td>' . e($i['name']) . ' × ' . (int)$i['quantity'] . '</td><td style="text-align:right">' . money($i['line_total']) . '</td></tr>';
        $html = '<div style="font-family:Arial,sans-serif;max-width:620px;margin:auto"><h2>Thank you for your order!</h2><p>Hello ' . e($order['customer_name']) . ', your order has been received.</p><p><b>Order ID:</b> ' . e($order['order_number']) . '</p><table style="width:100%;border-collapse:collapse" cellpadding="10">' . $rows . '<tr><td>Subtotal</td><td style="text-align:right">' . money($order['subtotal']) . '</td></tr><tr><td>Discount</td><td style="text-align:right">-' . money($order['discount_amount']) . '</td></tr><tr><td>Delivery</td><td style="text-align:right">' . money($order['shipping_amount']) . '</td></tr><tr><th>Total due on delivery</th><th style="text-align:right">' . money($order['total']) . '</th></tr></table><p>Payment method: Cash on delivery</p><p>We will contact you if we need to confirm delivery details.</p><p>— ' . e(store_name()) . '</p></div>';

        send_store_email($order['customer_email'], $order['customer_name'], 'Order confirmation ' . $order['order_number'], $html);

        $admin = site_setting('admin_notification_email', envv('ADMIN_NOTIFICATION_EMAIL', ''));
        if ($admin) send_store_email($admin, 'Store Admin', 'New order ' . $order['order_number'], '<p>New COD order received: <b>' . e($order['order_number']) . '</b></p><p>Customer: ' . e($order['customer_name']) . ' — ' . e($order['customer_phone']) . '</p><p>Total: ' . money($order['total']) . '</p><p><a href="' . e(url('admin/orders.php')) . '">Open order management</a></p>');
    }
}
