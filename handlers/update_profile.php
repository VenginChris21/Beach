<?php
session_start();
require_once __DIR__ . '/../classes/Users.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['msg'] = "Please log in to update your profile.";
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['msg'] = "Invalid request method.";
    header("Location: ../pages/employee_dashboard.php?edit=1");
    exit;
}

$email = trim($_POST['email'] ?? '');
$contact_number = trim($_POST['contact_number'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validate email
if (!preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email)) {
    $_SESSION['msg'] = "Please enter a valid Gmail address.";
    header("Location: ../pages/employee_dashboard.php?edit=1");
    exit;
}

// Validate contact number
if (!preg_match('/^[0-9]{7,15}$/', $contact_number)) {
    $_SESSION['msg'] = "Please enter a valid contact number (7-15 digits).";
    header("Location: ../pages/employee_dashboard.php?edit=1");
    exit;
}

// Update account
$users = new Users();
$updateData = [
    'email' => $email,
    'contact_number' => $contact_number,
    'password' => $password
];

$response = $users->updateAccount($user_id, $updateData);

// Always redirect to the edit form
$_SESSION['msg'] = $response['success'] 
    ? $response['message'] 
    : ($response['message'] ?? "Update failed. Please try again.");

header("Location: ../pages/employee_dashboard.php?edit=1");
exit;
?>
