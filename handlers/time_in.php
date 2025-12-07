<?php
session_start();
date_default_timezone_set('Asia/Manila');

require_once __DIR__ . '/../public/db.php';

if (!isset($_SESSION['employee_id'])) {
    $_SESSION['msg'] = "⚠️ Session expired. Please log in.";
    header("Location: ../index.php");
    exit;
}

$employee_id = $_SESSION['employee_id'];
$current_time = date('h:i:s A');
$current_date = date('Y-m-d');

$sql_check = "SELECT attendance_id FROM attendance WHERE employee_id = ? AND DATE(created_at) = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("is", $employee_id, $current_date);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    $_SESSION['msg'] = "⚠️ You have already timed in today.";
    $stmt_check->close();
    header("Location: ../pages/employee_dashboard.php");
    exit;
}
$stmt_check->close();

$status = 'Time In';
$remarks = 'Checked in successfully.';

$sql_insert = "INSERT INTO attendance (employee_id, check_in, status, remarks) VALUES (?, ?, ?, ?)";
$stmt_insert = $conn->prepare($sql_insert);
$stmt_insert->bind_param("isss", $employee_id, $current_time, $status, $remarks);
$stmt_insert->execute();

$_SESSION['msg'] = "✅ Time In recorded at $current_time.";
$stmt_insert->close();
$conn->close();

header("Location: ../pages/employee_dashboard.php");
exit;
?>
