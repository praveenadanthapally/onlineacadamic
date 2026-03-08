<?php
require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirectBasedOnRole();
}

$pageTitle = 'Welcome';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Online Academic System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="bi bi-mortarboard-fill me-2"></i>Online Academic
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-light btn-sm text-primary ms-2 px-3" href="/login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1>Online Academic System</h1>
            <p class="lead">A comprehensive platform for managing student records, marks, and academic performance</p>
            <div class="mt-4">
                <a href="/login.php" class="btn btn-light btn-lg me-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Get Started
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="container mb-5">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h4>Secure Access</h4>
                    <p class="text-muted">Role-based authentication system with password protection and session management for secure access to academic records.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-clipboard-data"></i>
                    </div>
                    <h4>Mark Management</h4>
                    <p class="text-muted">Efficiently manage and track student marks across multiple subjects with automatic grade calculation and performance analytics.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <h4>Performance Tracking</h4>
                    <p class="text-muted">Monitor academic progress with detailed reports, statistics, and visual representations of student performance.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- For Whom Section -->
    <section class="bg-white py-5">
        <div class="container">
            <h2 class="text-center mb-5">Designed For Everyone</h2>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-person-gear fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5>For Administrators</h5>
                            <p class="text-muted mb-0">Manage students, subjects, and marks with ease. Generate reports and monitor overall academic performance.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-mortarboard fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5>For Students</h5>
                            <p class="text-muted mb-0">View your marks, track your progress, and download your marksheets anytime, anywhere.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-light py-4 mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">
                        <i class="bi bi-mortarboard-fill me-2"></i>&copy; <?php echo date('Y'); ?> Online Academic System
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 text-muted">
                        <small>Designed for better education management</small>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
