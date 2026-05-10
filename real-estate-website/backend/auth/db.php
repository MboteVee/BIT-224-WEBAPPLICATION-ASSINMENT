<?php
// db.php — database connection only.
// ob_start() is intentionally NOT here; it lives in log.php and reg.php
// before session_start() so it fires at the very top of each entry point.

$host     = 'localhost';
$dbname   = 'real_estate_db'; // ← your database name
$username = 'root';           // ← your DB username
$password = '';               // ← your DB password

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,   false);

} catch (PDOException $e) {
    error_log('DB connection failed: ' . $e->getMessage());

    // Redirect to the right page depending on which script included this file
    $script = basename($_SERVER['SCRIPT_FILENAME'] ?? '');
    if ($script === 'reg.php') {
        header('Location: ../register.html?error=system_error');
    } else {
        header('Location: ../login.html?error=system_error');
    }
    exit();
}