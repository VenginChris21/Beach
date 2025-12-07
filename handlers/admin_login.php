<?php
session_start();
header('Content-Type: application/json');

define('ROOT_DIR', dirname(__DIR__));
define('DB_PATH', ROOT_DIR . '/public/db.php');
define('ADMIN_CLASS_PATH', ROOT_DIR . '/classes/Admin.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method.']);
    exit;
}

if (!file_exists(DB_PATH) || !file_exists(ADMIN_CLASS_PATH)) {
    echo json_encode(['error' => 'Critical backend files are missing.']);
    exit;
}

require_once DB_PATH;
require_once ADMIN_CLASS_PATH;

if (!isset($conn) || !$conn instanceof mysqli || $conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed. Please contact support.']);
    exit;
}

$username = trim($_POST['admin_username'] ?? '');
$password = trim($_POST['admin_password'] ?? '');

if (empty($username) || empty($password)) {
    echo json_encode(['error' => 'Username and password are required.']);
    exit;
}

try {
    $admin = new Admin($conn); 

    if ($admin->login($username, $password)) {
        echo json_encode([
            'success'  => true,
            'message'  => 'Login successful!',
            'redirect' => 'pages/admin_dashboard.php'
        ]);
    } else {
        echo json_encode(['error' => 'Invalid username or password.']);
    }

} catch (Exception $e) {
    error_log("Admin Login Exception: " . $e->getMessage());
    echo json_encode(['error' => 'An internal server error occurred.']);
}

exit;
?>
