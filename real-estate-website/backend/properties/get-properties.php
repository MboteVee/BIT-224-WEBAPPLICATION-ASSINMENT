<?php
// backend/properties/get-properties.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../auth/db.php';

$user_id = $_GET['user_id'] ?? null;

try {
    // Check if status column exists
    $checkColumn = $pdo->query("SHOW COLUMNS FROM properties LIKE 'status'");
    $hasStatus = $checkColumn->rowCount() > 0;
    
    if ($hasStatus) {
        $sql = "SELECT p.*, u.full_name as seller_name, u.email as seller_email, u.phone as seller_phone 
                FROM properties p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.status = 'available'";
    } else {
        $sql = "SELECT p.*, u.full_name as seller_name, u.email as seller_email, u.phone as seller_phone 
                FROM properties p 
                JOIN users u ON p.user_id = u.id";
    }
    
    $params = [];
    
    if ($user_id) {
        $sql .= " AND p.user_id = ?";
        $params[] = $user_id;
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $properties = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'properties' => $properties]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>