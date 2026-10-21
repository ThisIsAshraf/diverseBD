<?php
require_once __DIR__ . '/../middleware.php';

// Requires 'orders:read' scope
$apiKey = authenticate_api_request('orders:read');

$orderId     = trim((string)($_GET['order_id'] ?? ''));
$orderNumber = trim((string)($_GET['order_number'] ?? ''));
$phone       = trim((string)($_GET['phone'] ?? ''));

if ($orderId === '' && $orderNumber === '') {
    api_response(400, [
        'status' => 'error',
        'message' => 'Bad Request: Please provide either order_id or order_number query parameter.'
    ]);
}

try {
    $db = db();
    $where  = [];
    $params = [];

    if ($orderId !== '') {
        $where[]  = 'id = ?';
        $params[] = $orderId;
    } elseif ($orderNumber !== '') {
        $where[]  = 'order_number = ?';
        $params[] = $orderNumber;
    }

    if ($phone !== '') {
        $where[]  = 'customer_phone = ?';
        $params[] = $phone;
    }

    $sql = 'SELECT id, order_number, customer_name, customer_email, customer_phone, address, city, payment_method, status, tracking_number, total, created_at FROM orders WHERE ' . implode(' AND ', $where) . ' LIMIT 1';

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        api_response(404, [
            'status' => 'error',
            'message' => 'Order not found.'
        ]);
    }

    api_response(200, [
        'status' => 'success',
        'data' => $order
    ]);
} catch (Throwable $e) {
    api_response(500, [
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
