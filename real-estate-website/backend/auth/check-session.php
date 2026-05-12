<?php
// backend/auth/check-session.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');

session_start();

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'logged_in' => true,
        'user_id' => $_SESSION['user_id'],
        'user_name' => $_SESSION['user_name'],
        'user_email' => $_SESSION['user_email']
    ]);
} else {
    echo json_encode(['logged_in' => false]);
}
?>