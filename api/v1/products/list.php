<?php
require_once __DIR__ . '/../middleware.php';

// Requires 'products:read' scope
authenticate_api_request('products:read');

$limit  = max(1, min(100, (int)($_GET['limit'] ?? 20)));
$page   = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;
$catId  = (int)($_GET['category_id'] ?? 0);

try {
    $db = db();

    $where  = ['p.active = 1'];
    $params = [];

    if ($catId > 0) {
        $where[]  = 'p.category_id = ?';
        $params[] = $catId;
    }

    $whereSql = 'WHERE ' . implode(' AND ', $where);

    $sql = "SELECT p.id, p.category_id, c.name AS category_name, p.name, p.slug, p.sku, p.description, p.price, p.compare_price, p.image_url, p.stock, p.created_at 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            {$whereSql} 
            ORDER BY p.id DESC LIMIT ? OFFSET ?";

    $stmt = $db->prepare($sql);

    $i = 1;
    foreach ($params as $param) {
        $stmt->bindValue($i++, $param, PDO::PARAM_INT);
    }
    $stmt->bindValue($i++, $limit, PDO::PARAM_INT);
    $stmt->bindValue($i++, $offset, PDO::PARAM_INT);
    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    api_response(200, [
        'status' => 'success',
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'count' => count($products)
        ],
        'data' => $products
    ]);
} catch (Throwable $e) {
    api_response(500, [
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
