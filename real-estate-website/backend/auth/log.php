<?php
// FIX 1: ob_start() must come first — before session_start() — so any
//         accidental whitespace or BOM never corrupts headers.
ob_start();
session_start();

require_once __DIR__ . '/db.php';

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.html');
    exit();
}

$email    = trim($_POST['email']    ?? '');
// FIX 2: trim() password so a string of spaces doesn't pass empty() check
$password = trim($_POST['password'] ?? '');

// Empty-field check
if (empty($email) || empty($password)) {
    header('Location: ../login.html?error=empty_fields');
    exit();
}

// FIX 3: Invalid email now redirects to ?error=invalid_email, not empty_fields
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../login.html?error=invalid_email');
    exit();
}

// FIX 4: Wrap the SELECT in try/catch — if the table doesn't exist or the
//         DB drops mid-request, the script redirects cleanly instead of crashing.
try {
    $stmt = $pdo->prepare(
        'SELECT id, first_name, last_name, password_hash, account_type
         FROM users
         WHERE email = ?
         LIMIT 1'
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    error_log('Login query failed: ' . $e->getMessage());
    header('Location: ../login.html?error=system_error');
    exit();
}

// Verify the user exists and the password is correct
if ($user && password_verify($password, $user['password_hash'])) {

    // Regenerate session ID to prevent session-fixation attacks
    session_regenerate_id(true);

    $_SESSION['user_id']      = $user['id'];
    $_SESSION['user_name']    = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['account_type'] = $user['account_type'];

    header('Location: ../index.html');
    exit();

} else {
    // Generic — don't reveal whether the email or password was wrong
    header('Location: ../login.html?error=invalid');
    exit();
}