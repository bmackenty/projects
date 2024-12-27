<?php 
    session_start(); 
    $base_url = "https://" . $_SERVER['HTTP_HOST'];
    require_once __DIR__ . '/../../Helpers/TimeHelper.php';  

// Auto-login with remember token
if (!isset($_SESSION['user']) && isset($_COOKIE['remember_token'])) {
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../Models/User.php';
    
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
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.tiny.cloud/1/0ej5pnow0o4gxdyaqdyz2zgdu0f4nulp55y17gr52byvbd35/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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
                        <li class="nav-item">
                            <span class="nav-link"><?= htmlspecialchars($_SESSION['user']['email']) ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $base_url ?>/logout">Logout</a>
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
    <main class="container py-4"></main>