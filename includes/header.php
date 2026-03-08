<?php
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Online Academic System</title>
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
                    <?php if (isLoggedIn()): ?>
                        <?php if (isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/admin/dashboard') !== false ? 'active' : ''; ?>" href="/admin/dashboard.php">
                                    <i class="bi bi-speedometer2 me-1"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/admin/students') !== false ? 'active' : ''; ?>" href="/admin/students.php">
                                    <i class="bi bi-people me-1"></i>Students
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/admin/subjects') !== false ? 'active' : ''; ?>" href="/admin/subjects.php">
                                    <i class="bi bi-book me-1"></i>Subjects
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/admin/marks') !== false ? 'active' : ''; ?>" href="/admin/marks.php">
                                    <i class="bi bi-clipboard-data me-1"></i>Marks
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/student/dashboard') !== false ? 'active' : ''; ?>" href="/student/dashboard.php">
                                    <i class="bi bi-speedometer2 me-1"></i>My Dashboard
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $_SERVER['PHP_SELF'] === '/index.php' ? 'active' : ''; ?>" href="/">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-light btn-sm text-primary ms-2 px-3" href="/login.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <main class="container py-4">
        <?php displayFlashMessage(); ?>
