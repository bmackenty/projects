<?php
session_start();

require_once __DIR__ . '/../Services/Logger.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/User.php';

$logger = new Logger();

if (isset($_SESSION['user'])) {
    $logger->info('User logged out', ['user_id' => $_SESSION['user']['id'], 'email' => $_SESSION['user']['email']]);
    
    // Clear remember me cookie and token if exists
    if (isset($_COOKIE['remember_token'])) {
        $userModel = new User($pdo);
        $userModel->deleteRememberToken($_COOKIE['remember_token']);
        setcookie('remember_token', '', time() - 3600, '/');
    }
    
    unset($_SESSION['user']);
    session_destroy();
}

header('Location: /login');
exit;