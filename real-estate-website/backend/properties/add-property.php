<?php
// backend/properties/add-property.php - COMPLETE FIX
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
session_start();
require_once '../auth/db.php';

// Log function for debugging
function log_message($msg) {
    error_log(date('[Y-m-d H:i:s] ') . $msg . "\n", 3, '/tmp/upload_debug.log');
}

log_message("=== New upload attempt ===");
log_message("Session user_id: " . ($_SESSION['user_id'] ?? 'not set'));

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

log_message("POST data: " . print_r($_POST, true));
log_message("FILES data: " . print_r($_FILES, true));

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
    log_message("Processing image: " . $_FILES['image']['name']);
    
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
    $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_ext, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowed)]);
        exit;
    }
    
    // Get absolute path
    $project_root = '/var/www/html/BIT-224-WEBAPPLICATION-ASSINMENT/real-estate-website/';
    $upload_dir = $project_root . 'uploads/';
    
    log_message("Upload directory: " . $upload_dir);
    
    // Create directory if needed
    if (!file_exists($upload_dir)) {
        log_message("Creating uploads directory");
        mkdir($upload_dir, 0777, true);
    }
    
    // Generate unique filename
    $filename = time() . '_' . uniqid() . '.' . $file_ext;
    $upload_path = $upload_dir . $filename;
    
    log_message("Target path: " . $upload_path);
    
    // Move file
    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
        $image_url = 'uploads/' . $filename;
        log_message("✅ Image uploaded: " . $image_url);
    } else {
        $upload_error = error_get_last();
        log_message("❌ Upload failed: " . print_r($upload_error, true));
        echo json_encode(['success' => false, 'message' => 'Failed to save image. Check permissions.']);
        exit;
    }
} else {
    log_message("No image uploaded or upload error");
}

// Insert into database
try {
    log_message("Inserting into database...");
    $sql = "INSERT INTO properties (user_id, title, description, price, location, bedrooms, bathrooms, area_size, property_type, image_url, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'available')";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$user_id, $title, $description, $price, $location, $bedrooms, $bathrooms, $area_size, $property_type, $image_url]);
    
    if ($result) {
        $property_id = $pdo->lastInsertId();
        log_message("✅ Property inserted with ID: " . $property_id);
        echo json_encode(['success' => true, 'message' => 'Property added successfully', 'image_url' => $image_url]);
    } else {
        log_message("❌ Database insert failed");
        echo json_encode(['success' => false, 'message' => 'Failed to save to database']);
    }
} catch (PDOException $e) {
    log_message("❌ Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>