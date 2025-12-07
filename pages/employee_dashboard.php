<?php
session_start();
require_once __DIR__ . '/../public/db.php';
require_once __DIR__ . '/../classes/Users.php'; // Assuming this path is correct

// Check login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['msg'] = "Please log in to access the dashboard.";
    header("Location: /new/index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$employee_id = $_SESSION['employee_id'];
$msg = isset($_SESSION['msg']) ? $_SESSION['msg'] : '';
unset($_SESSION['msg']);

// Keep form data if validation fails
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

// Background image logic
$backgroundImage = '/new/uploads/default_bg.jpg';
if (isset($_SESSION['bg_image'])) {
    $backgroundImage = $_SESSION['bg_image'];
} else {
    $_SESSION['bg_image'] = $backgroundImage;
}

// Fetch employee info (PHP logic retained)
$sql_emp = "SELECT e.*, d.department_name 
             FROM employees e 
             JOIN departments d ON e.department_id = d.department_id 
             WHERE e.employee_id = ?";
$stmt_emp = $conn->prepare($sql_emp);
$stmt_emp->bind_param("i", $employee_id);
$stmt_emp->execute();
$result_emp = $stmt_emp->get_result();
$employee_details = $result_emp->fetch_assoc();
$stmt_emp->close();

// Fetch latest attendance (PHP logic retained)
$sql_att = "SELECT DATE(created_at) AS date, check_in, check_out, status, remarks 
             FROM attendance 
             WHERE employee_id = ? 
             ORDER BY created_at DESC, check_in DESC 
             LIMIT 1";
$stmt_att = $conn->prepare($sql_att);
$stmt_att->bind_param("i", $employee_id);
$stmt_att->execute();
$result_att = $stmt_att->get_result();
$latest_attendance = $result_att->fetch_assoc();
$stmt_att->close();

// Handle background upload (PHP logic retained)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['bg_upload'])) {
    $uploadDir = __DIR__ . '/../images/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName = basename($_FILES['bg_upload']['name']);
    $targetFile = $uploadDir . $fileName;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES['bg_upload']['tmp_name'], $targetFile)) {
            $_SESSION['bg_image'] = '/new/images/' . $fileName;
            $msg = "Background updated successfully!";
        } else {
            $msg = "Upload failed.";
        }
    } else {
        $msg = "Only JPG, PNG, GIF allowed.";
    }

    $_SESSION['msg'] = $msg;
    header("Location: /new/pages/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Employee Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<link rel="stylesheet" href="../assets/css/dashboard.css">

<style>
body {
    background-image: url("<?php echo $backgroundImage; ?>");
}
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top" id="dashboardNavbar">
    <div class="container-fluid">
        <a class="navbar-brand text-white" href="/new/pages/dashboard.php">
            <i class="fas fa-chart-line me-2"></i> Dashboard
        </a>

        <span class="navbar-text ms-auto me-3 d-none d-sm-inline">
            Logged in as 
            <strong>
                <?php echo htmlspecialchars($employee_details['first_name'] . ' ' . $employee_details['last_name']); ?>
            </strong>
        </span>

        <a href="/new/handlers/logout.php" class="btn btn-danger btn-sm">
            <i class="fas fa-sign-out-alt me-1"></i> Logout
        </a>
    </div>
</nav>

<div class="dashboard-container">
    <div class="main-card">
        <h1>Welcome Back!</h1>

        <?php if ($msg): ?>
            <div class="message mb-4"><?php echo $msg; ?></div>
        <?php endif; ?>

        <div id="clock" class="fs-1 mb-4"></div>

        <div class="mb-4 button-group">
            <a href="/new/handlers/time_in.php" class="btn btn-success me-2">
                <i class="far fa-clock me-1"></i> TIME IN
            </a>
            <a href="/new/handlers/time_out.php" class="btn btn-danger">
                <i class="far fa-hand-paper me-1"></i> TIME OUT
            </a>
        </div>

        <h2>Your Profile</h2>
        <table class="table text-white">
            <tr><th>Name</th><td><?php echo $employee_details['first_name'] . ' ' . $employee_details['last_name']; ?></td></tr>
            <tr><th>Department</th><td><?php echo $employee_details['department_name']; ?></td></tr>
            <tr><th>Contact</th><td><?php echo $employee_details['contact_number']; ?></td></tr>
            <tr><th>Email</th><td><?php echo $employee_details['email']; ?></td></tr>
            <tr><th>Date Hired</th><td><?php echo $employee_details['date_hired']; ?></td></tr>
        </table>

        <a href="/new/pages/employee_dashboard.php?edit=1" class="btn btn-warning mt-3">
            <i class="fas fa-user-edit me-1"></i> Edit Profile
        </a>

        <?php if (isset($_GET['edit'])): ?>
        <h2 class="mt-4">Edit Profile</h2>
        <form method="POST" action="/new/handlers/update_profile.php" class="text-white">
            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">

            <div class="mb-3">
                <label>Contact Number</label>
                <input type="text" name="contact_number" class="form-control" 
                       value="<?php echo htmlspecialchars($form_data['contact_number'] ?? $employee_details['contact_number']); ?>" required>
            </div>

            <div class="mb-3">
                <label>Email (Gmail only)</label>
                <input type="email" name="email" pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$" class="form-control" 
                       value="<?php echo htmlspecialchars($form_data['email'] ?? $employee_details['email']); ?>" required>
            </div>

            <div class="mb-3">
                <label>New Password (leave blank to keep current)</label>
                <input type="password" name="password" class="form-control" placeholder="New Password">
            </div>

            <button class="btn btn-success w-100 mt-4">Save Changes</button>
        </form>
        <?php endif; ?>

        <h2 class="mt-5">Latest Attendance</h2>
        <?php if ($latest_attendance): ?>
        <table class="table text-white">
            <tr><th>Date</th><th>Check In</th><th>Check Out</th><th>Status</th><th>Remarks</th></tr>
            <tr class="latest-row">
                <td><?php echo $latest_attendance['date']; ?></td>
                <td><?php echo $latest_attendance['check_in']; ?></td>
                <td><?php echo $latest_attendance['check_out'] ?: '---'; ?></td>
                <td><?php echo $latest_attendance['status']; ?></td>
                <td><?php echo $latest_attendance['remarks']; ?></td>
            </tr>
        </table>
        <?php else: ?>
        <p>No attendance records found yet.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function updateClock() {
    const now = new Date();
    document.getElementById("clock").textContent =
        now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}
setInterval(updateClock, 1000);
updateClock();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>