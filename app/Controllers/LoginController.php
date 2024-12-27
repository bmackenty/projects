<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Services/Logger.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$logger = new Logger();

try {
    $logger->info('Login attempt', ['email' => $email]);
    
    $userModel = new User($pdo);
    $user = $userModel->findByEmail($email);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        
        $logger->info('Login successful', ['user_id' => $user['id'], 'email' => $email]);
        unset($_SESSION['error']);
        header('Location: /dashboard');
        exit;
    } else {
        $logger->warning('Login failed: Invalid credentials', ['email' => $email]);
        $_SESSION['error'] = 'Invalid email or password';
        header('Location: /login');
        exit;
    }
} catch (PDOException $e) {
    $logger->error('Database error during login', [
        'email' => $email,
        'error' => $e->getMessage()
    ]);
    $_SESSION['error'] = 'An error occurred: ' . $e->getMessage();
    header('Location: /login');
    exit;
}