<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit();
}

$email    = trim($_POST['email']    ?? '');
$password =       $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    header('Location: ../login.php?error=empty_fields');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../login.php?error=invalid_email');
    exit();
}

$stmt = $pdo->prepare(
    'SELECT id, first_name, last_name, password_hash, account_type
     FROM users WHERE email = ? LIMIT 1'
);
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password_hash'])) {

    session_regenerate_id(true);

    $_SESSION['user_id']      = $user['id'];
    $_SESSION['user_name']    = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['first_name']   = $user['first_name'];
    $_SESSION['account_type'] = $user['account_type'];

    // Route based on account type
    if ($user['account_type'] === 'agent') {
        header('Location: ../seller-dashboard.php');
    } else {
        header('Location: ../buyer-dashboard.php');
    }
    exit();
} else {
    header('Location: ../login.php?error=invalid');
    exit();
}
