<?php
// handlers/login.php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../public/db.php';
// We'll use the Admin class to handle login, assuming you want Admin Login here
// OR, if this is for the general Employee login, we need to adapt the logic.

// *** CRITICAL ASSUMPTION: Based on your previous files, Admin login is separate.
// *** This file will be modified to handle a generic Employee/User login.
// *** Since you didn't provide a 'Users' class using mysqli, we'll use raw mysqli/DB.php logic.

$response = ['success' => false, 'error' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $response['error'] = 'Username and password are required.';
        echo json_encode($response);
        exit;
    }
    
    // --- DATABASE QUERY LOGIC ---
    // Query: Join users and employees to get login data and redirect info
    $stmt = $conn->prepare("
        SELECT u.user_id, u.password AS hash, u.role, 
               e.employee_id, e.first_name, e.last_name 
        FROM users u
        JOIN employees e ON u.employee_id = e.employee_id
        WHERE u.username = ?
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['hash'])) {
            // Successful Login
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['employee_id'] = $user['employee_id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['role'] = $user['role']; 
            
            // Determine redirect based on role
            $redirect_path = ($user['role'] === 'admin') ? 'pages/admin_dashboard.php' : 'pages/employee_dashboard.php';

            $response = [
                'success' => 'Login successful!', 
                'redirect' => $redirect_path
            ];
            
            // Update last login (optional but good practice)
            $update_stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
            $update_stmt->bind_param("i", $user['user_id']);
            $update_stmt->execute();
            $update_stmt->close();
            
        } else {
            $response['error'] = 'Incorrect password.';
        }
    } else {
        $response['error'] = 'Username not found.';
    }

    $stmt->close();
} else {
    $response['error'] = 'Invalid request method.';
}

echo json_encode($response);
exit;
?>