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

$sql_select = "SELECT attendance_id FROM attendance 
               WHERE employee_id = ? 
               AND DATE(created_at) = ? 
               AND check_out IS NULL 
               ORDER BY created_at DESC 
               LIMIT 1";

$stmt_select = $conn->prepare($sql_select);
$stmt_select->bind_param("is", $employee_id, $current_date);
$stmt_select->execute();
$result_select = $stmt_select->get_result();

if ($result_select->num_rows === 0) {
    $_SESSION['msg'] = "⚠️ You must Time In first today.";
    $stmt_select->close();
    header("Location: ../pages/employee_dashboard.php");
    exit;
}

$attendance_id = $result_select->fetch_assoc()['attendance_id'];
$stmt_select->close();

$status = 'Time Out';
$remarks = 'Checked out successfully.';

$sql_update = "UPDATE attendance SET check_out = ?, status = ?, remarks = ? WHERE attendance_id = ?";
$stmt_update = $conn->prepare($sql_update);
$stmt_update->bind_param("sssi", $current_time, $status, $remarks, $attendance_id);
$stmt_update->execute();

$_SESSION['msg'] = "✅ Time Out recorded at $current_time.";
$stmt_update->close();
$conn->close();

header("Location: ../pages/employee_dashboard.php");
exit;
?>
