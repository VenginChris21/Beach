<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$root = '/new/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Bulinaw Beach Resort</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $root ?>assets/css/admin_dashboard.css">
    
    <style>
        .navbar {
            background-color: #1B2A41;
            border-bottom: 2px solid #41EAD4;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
        }
        .navbar-brand {
            color: #41EAD4 !important;
            font-weight: 700;
        }
        .nav-link {
            color: #E0FBFC !important;
            transition: color 0.3s;
        }
        .nav-link:hover {
            color: #41EAD4 !important;
        }
        .navbar-text {
            color: #92B4F4;
            margin-right: 15px;
        }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid container">
        <a class="navbar-brand" href="<?= $root ?>pages/admin_dashboard.php">
            Bulinaw Admin
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="<?= $root ?>pages/admin_dashboard.php">Dashboard</a>
                </li>
            </ul>
            <span class="navbar-text">
                Welcome, <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?>
            </span>
            <a href="<?= $root ?>handlers/logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-4">
