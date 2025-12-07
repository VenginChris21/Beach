<?php
session_start();
include('../classes/Users.php');
$users = new Users();

$response = ['error' => null, 'success' => null, 'redirect' => null];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $contact    = trim($_POST['contact_number'] ?? '');
    $department_id = (int)($_POST['department_id'] ?? 0);
    $username   = trim($_POST['username'] ?? '');
    $password   = $_POST['password'] ?? '';

    // Validate
    if (!$first_name || !$last_name || !$email || !$username || !$password || !$department_id) {
        $response['error'] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['error'] = "Invalid email format.";
    } elseif (strlen($password) < 8) {
        $response['error'] = "Password must be at least 8 characters.";
    }

    // If validation passes
    if (!$response['error']) {
        $data = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'contact_number' => $contact,
            'department_id' => $department_id,
            'username' => $username,
            'password' => $password,
            'date_hired' => date('Y-m-d'),
            'status' => 'Active',
            'role' => 'employee'
        ];

        $result = $users->signup($data);

        switch ($result) {
            case 1:
                $response['success'] = "Registration successful!";
                $response['redirect'] = "pages/home.php";
                break;
            case 3:
                $response['error'] = "Username or email already exists.";
                break;
            case 4:
                $response['error'] = "Invalid department selected.";
                break;
            default:
                $response['error'] = "Database error. Please try again.";
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
