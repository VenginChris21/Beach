<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bulinaw Beach Resort - Employee Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/home.css">
</head>
<body>

<?php include_once('../includes/header.php'); ?>

<div class="center-wrapper">
    <div class="w-100" style="max-width: 550px;">

        <form id="loginForm" class="form-card hidden">
            <h3>Employee Login</h3>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn">Login</button>
            <div id="loginResponse" class="mt-2 fw-bold"></div>
        </form>

        <form id="registerForm" class="form-card hidden">
            <h3>Register</h3>
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="contact_number" placeholder="Contact Number" required>
            <select id="departmentSelect" name="department_id" required>
                <option disabled selected>Select Department</option>
                <option value="1">HR</option>
                <option value="2">Employee</option>
                <option value="3">Finance</option>
                <option value="4">Marketing</option>
                <option value="5">Operations</option>
            </select>
            <input type="text" name="username" placeholder="Choose Username" required>
            <input type="password" name="password" placeholder="Choose Password" required>
            <button type="submit" class="btn">Register</button>
            <div id="registerResponse" class="mt-2 fw-bold"></div>
        </form>

        <form id="adminLoginForm" class="form-card hidden">
    <h3 class="text-danger">Admin Login</h3>
    <input type="text" name="admin_username" placeholder="Admin Username" required>
    <input type="password" name="admin_password" placeholder="Password" required>
    <button type="submit" class="btn btn-admin">Admin Login</button>
    <div id="adminResponse" class="mt-2 fw-bold"></div>
</form>
    </div>
</div>

<?php include_once('../includes/footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/home.js"></script>
</body>
</html>