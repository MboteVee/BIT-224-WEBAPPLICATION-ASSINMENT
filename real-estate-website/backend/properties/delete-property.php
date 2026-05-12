<?php
// backend/properties/delete-property.php
header('Content-Type: application/json');
session_start();
require_once '../auth/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' || $_SERVER['REQUEST_METHOD'] === 'POST') {
    $property_id = $_GET['id'] ?? $_POST['id'] ?? null;
    $user_id = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("DELETE FROM properties WHERE id = ? AND user_id = ?");
    
    if ($stmt->execute([$property_id, $user_id]) && $stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Property deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Property not found or unauthorized']);
    }
}
?>