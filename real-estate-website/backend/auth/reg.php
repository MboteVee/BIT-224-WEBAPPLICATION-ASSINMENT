<?php
// backend/auth/reg.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $full_name = trim($data['full_name'] ?? '');
    $email = trim($data['email'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $password = $data['password'] ?? '';
    
    // Validation
    if (empty($full_name) || empty($email) || empty($phone) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }
    
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
        exit;
    }
    
    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit;
    }
    
    // Create user
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password) VALUES (?, ?, ?, ?)");
    
    if ($stmt->execute([$full_name, $email, $phone, $hashed_password])) {
        session_start();
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['user_name'] = $full_name;
        $_SESSION['user_email'] = $email;
        
        echo json_encode(['success' => true, 'message' => 'Registration successful', 'user_id' => $_SESSION['user_id']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration failed']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>