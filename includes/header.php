<?php

?>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-lg" id="mainNavbar">
    <div class="container">
        
        <a class="navbar-brand fw-bold" href="#">
            <i class="fas fa-umbrella-beach me-2"></i> **Bulinaw Resort** <span class="portal-text d-none d-sm-inline">Employee Portal</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center"> 
                
                <li class="nav-item">
                    <a class="nav-link" 
                       href="javascript:void(0)" 
                       onclick="toggleForm('loginForm'); setActiveLink(this);" 
                       id="showLoginFormLink"
                       aria-label="Show Employee Login Form">
                        <i class="fas fa-sign-in-alt me-1"></i> **Employee Login**
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" 
                       href="javascript:void(0)" 
                       onclick="toggleForm('registerForm'); setActiveLink(this);" 
                       id="showRegisterFormLink"
                       aria-label="Show Employee Registration Form">
                        <i class="fas fa-user-plus me-1"></i> **New Hire Registration**
                    </a>
                </li>
                
                <li class="nav-item ms-lg-3">
                    <a class="nav-link btn btn-sm btn-admin-nav" 
                       href="javascript:void(0)" 
                       onclick="toggleForm('adminLoginForm'); setActiveLink(this);" 
                       id="showAdminFormLink"
                       aria-label="Show Administrator Login Form">
                        <i class="fas fa-user-shield me-1"></i> **Admin Access**
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>