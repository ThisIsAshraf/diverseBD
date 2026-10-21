
<?php
require_once __DIR__ . '/../middleware.php';

// Requires 'customers:read' scope
authenticate_api_request('customers:read');

$email = trim((string)($_GET['email'] ?? ''));
$phone = trim((string)($_GET['phone'] ?? ''));
$id    = (int)($_GET['id'] ?? 0);

if ($email === '' && $phone === '' && $id <= 0) {
    api_response(400, [
        'status' => 'error',
        'message' => 'Bad Request: Provide id, email, or phone query parameter.'
    ]);
}

try {
    $db = db();
    $where  = [];
    $params = [];

    if ($id > 0) {
        $where[]  = 'id = ?';
        $params[] = $id;
    } elseif ($email !== '') {
        $where[]  = 'email = ?';
        $params[] = $email;
    } elseif ($phone !== '') {
        $where[]  = 'phone = ?';
        $params[] = $phone;
    }

    $stmt = $db->prepare('SELECT id, name, email, phone, address, city, status, created_at FROM users WHERE ' . implode(' AND ', $where) . ' LIMIT 1');
    $stmt->execute($params);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        api_response(404, [
            'status' => 'error',
            'message' => 'Customer profile not found.'
        ]);
    }

    api_response(200, [
        'status' => 'success',
        'data' => $customer
    ]);
} catch (Throwable $e) {
    api_response(500, [
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
