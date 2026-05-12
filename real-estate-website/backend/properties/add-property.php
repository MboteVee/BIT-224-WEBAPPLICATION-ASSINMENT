<?php
// backend/properties/add-property.php - COMPLETE FIX
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
session_start();
require_once '../auth/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get form data
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$price = floatval($_POST['price'] ?? 0);
$location = trim($_POST['location'] ?? '');
$bedrooms = intval($_POST['bedrooms'] ?? 0);
$bathrooms = intval($_POST['bathrooms'] ?? 0);
$area_size = intval($_POST['area_size'] ?? 0);
$property_type = $_POST['property_type'] ?? 'house';
$user_id = $_SESSION['user_id'];

// Validate
if (empty($title) || empty($description) || $price <= 0 || empty($location)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
    exit;
}

// Handle image upload
$image_url = '';
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowed)]);
        exit;
    }
    
    // Use correct absolute path
    $upload_dir = '/var/www/html/BIT-224-WEBAPPLICATION-ASSINMENT/real-estate-website/uploads/';
    
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $filename = time() . '_' . uniqid() . '.' . $ext;
    $target = $upload_dir . $filename;
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $image_url = 'uploads/' . $filename;
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save image file']);
        exit;
    }
}

// Insert into database
try {
    $sql = "INSERT INTO properties (user_id, title, description, price, location, bedrooms, bathrooms, area_size, property_type, image_url) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$user_id, $title, $description, $price, $location, $bedrooms, $bathrooms, $area_size, $property_type, $image_url]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Property added successfully', 'image_url' => $image_url]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database insert failed']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>