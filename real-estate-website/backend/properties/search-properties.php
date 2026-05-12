<?php
// backend/properties/search-properties.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../auth/db.php';

$query = $_GET['query'] ?? '';
$min_price = $_GET['min_price'] ?? 0;
$max_price = $_GET['max_price'] ?? 10000000;
$bedrooms = $_GET['bedrooms'] ?? null;
$location = $_GET['location'] ?? '';

$sql = "SELECT p.*, u.full_name as seller_name, u.email as seller_email, u.phone as seller_phone 
        FROM properties p 
        JOIN users u ON p.user_id = u.id 
        WHERE 1=1";
$params = [];

if (!empty($query)) {
    $sql .= " AND (p.title LIKE ? OR p.description LIKE ? OR p.location LIKE ?)";
    $search = "%$query%";
    $params = array_merge($params, [$search, $search, $search]);
}

if ($min_price > 0) {
    $sql .= " AND p.price >= ?";
    $params[] = $min_price;
}

if ($max_price < 10000000) {
    $sql .= " AND p.price <= ?";
    $params[] = $max_price;
}

if ($bedrooms && $bedrooms > 0) {
    $sql .= " AND p.bedrooms = ?";
    $params[] = $bedrooms;
}

if (!empty($location)) {
    $sql .= " AND p.location LIKE ?";
    $params[] = "%$location%";
}

$sql .= " ORDER BY p.created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $properties = $stmt->fetchAll();
    echo json_encode(['success' => true, 'properties' => $properties]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>