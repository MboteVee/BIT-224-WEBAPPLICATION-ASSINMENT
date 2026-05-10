<?php
ob_start();
session_start();

require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php');
    exit();
}

// ── 1. Collect & sanitise inputs ────────────────────────────────────────────
$account_type     = trim($_POST['account_type']      ?? 'buyer');
// FIX 10: Cap at 50 chars to match VARCHAR(50) in schema.sql
$first_name       = mb_substr(trim($_POST['firstName']   ?? ''), 0, 50);
$last_name        = mb_substr(trim($_POST['lastName']    ?? ''), 0, 50);
// Lowercase email so "User@Gmail.com" and "user@gmail.com" are the same account
$email            = strtolower(trim($_POST['email']      ?? ''));
$phone            = mb_substr(trim($_POST['phoneNumber'] ?? ''), 0, 20);
$password         = trim($_POST['password']              ?? '');
$confirm_password = trim($_POST['confirm_password']      ?? '');

// ── 2. Required-field check ──────────────────────────────────────────────────
if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
    header('Location: ../register.php?error=empty_fields');
    exit();
}

// ── 3. Email format ──────────────────────────────────────────────────────────
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../register.php?error=invalid_email');
    exit();
}

// ── 4. Password length ───────────────────────────────────────────────────────
if (strlen($password) < 8) {
    header('Location: ../register.php?error=weak_password');
    exit();
}

// ── 5. Passwords match ───────────────────────────────────────────────────────
if ($password !== $confirm_password) {
    header('Location: ../register.php?error=password_mismatch');
    exit();
}

// ── 6. Whitelist account_type ────────────────────────────────────────────────
if (!in_array($account_type, ['buyer', 'agent'], true)) {
    $account_type = 'buyer';
}

// ── 7. Duplicate email check ─────────────────────────────────────────────────
try {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        header('Location: ../register.php?error=email_taken');
        exit();
    }
} catch (PDOException $e) {
    error_log('Email check failed: ' . $e->getMessage());
    header('Location: ../register.php?error=system_error');
    exit();
}

// ── 8. Hash & insert ─────────────────────────────────────────────────────────
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare(
        'INSERT INTO users (account_type, first_name, last_name, email, phone_number, password_hash)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$account_type, $first_name, $last_name, $email, $phone, $hashed_password]);

    header('Location: ../login.php?success=registered');
    exit();

} catch (PDOException $e) {
    error_log('Registration insert failed: ' . $e->getMessage());
    header('Location: ../register.php?error=system_error');
    exit();
}