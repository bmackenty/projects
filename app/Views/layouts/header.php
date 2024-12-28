<?php 
    session_start(); 
    $base_url = "https://" . $_SERVER['HTTP_HOST'];
    require_once __DIR__ . '/../../Helpers/TimeHelper.php';  

// Debug the path resolution
$config_path = __DIR__ . '/../../../config/database.php';
error_log("Attempting to load database config from: " . $config_path);
error_log("File exists check: " . (file_exists($config_path) ? 'true' : 'false'));

// Auto-login with remember token
if (!isset($_SESSION['user']) && isset($_COOKIE['remember_token'])) {
    require_once $config_path;  // This should create the $pdo variable
    require_once __DIR__ . '/../../Models/User.php';
    
    if (isset($pdo)) {  // Check if $pdo exists before using it
        $userModel = new User($pdo);
        $user = $userModel->findByRememberToken($_COOKIE['remember_token']);
        
        if ($user) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
        } else {
            // Invalid or expired token, clear the cookie
            setcookie('remember_token', '', time() - 3600, '/');
        }
    } else {
        error_log("Database connection not established - PDO variable is not set");
    }
}

error_log("Current working directory: " . getcwd());

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.tiny.cloud/1/0ej5pnow0o4gxdyaqdyz2zgdu0f4nulp55y17gr52byvbd35/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?= $base_url ?>">Project Manager</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">Dashboard</a>
                    </li>
                    <?php if(isset($_SESSION['user'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $base_url ?>/projects">Projects</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if(isset($_SESSION['user'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <?= htmlspecialchars($_SESSION['user']['email']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= $base_url ?>/profile">Profile</a></li>
                                <li><a class="dropdown-item" href="<?= $base_url ?>/logout">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $base_url ?>/login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $base_url ?>/register">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="py-4">