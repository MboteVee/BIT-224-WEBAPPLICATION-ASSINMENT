<?php
// backend/properties/update-property.php
header('Content-Type: application/json');
session_start();
require_once '../auth/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property_id = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? null;
    $user_id = $_SESSION['user_id'];
    
    if ($status) {
        $stmt = $pdo->prepare("UPDATE properties SET status = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$status, $property_id, $user_id]);
        echo json_encode(['success' => true, 'message' => 'Property status updated']);
    }
}
?>