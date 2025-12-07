<?php

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Authorization required.']);
    exit;
}

require_once __DIR__ . '/../public/db.php';
require_once __DIR__ . '/../classes/Admin.php';

if (!isset($conn) || $conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['employee_id'])) {
    $employeeId = filter_var($_POST['employee_id'], FILTER_VALIDATE_INT);

    if ($employeeId === false) {
        echo json_encode(['error' => 'Invalid employee ID.']);
        exit;
    }

    $admin = new Admin($conn);
    
    if ($admin->deleteEmployee($employeeId)) {
        echo json_encode(['success' => 'Employee deleted successfully.']);
    } else {
        echo json_encode(['error' => 'Failed to delete employee. (Database error or ID not found)']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method or missing data.']);
}
?>