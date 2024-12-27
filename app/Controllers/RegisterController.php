<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Services/Logger.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';
$logger = new Logger();

$logger->info('Registration attempt', ['email' => $email]);

// Basic validation
if ($password !== $password_confirm) {
    $logger->warning('Registration failed: Passwords do not match', ['email' => $email]);
    $_SESSION['error'] = 'Passwords do not match';
    header('Location: /register');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $logger->warning('Registration failed: Invalid email format', ['email' => $email]);
    $_SESSION['error'] = 'Invalid email format';
    header('Location: /register');
    exit;
}

try {
    $userModel = new User($pdo);
    
    if ($userModel->findByEmail($email)) {
        $logger->warning('Registration failed: Email already exists', ['email' => $email]);
        $_SESSION['error'] = 'Email already exists';
        header('Location: /register');
        exit;
    }

    $user_id = $userModel->create($email, $password);
    $logger->info('User registered successfully', ['user_id' => $user_id, 'email' => $email]);

    $_SESSION['user'] = [
        'id' => $user_id,
        'email' => $email,
        'role' => 'user'
    ];

    unset($_SESSION['error']);
    header('Location: /dashboard');
    exit;

} catch (PDOException $e) {
    $logger->error('Database error during registration', [
        'email' => $email,
        'error' => $e->getMessage()
    ]);
    $_SESSION['error'] = 'An error occurred: ' . $e->getMessage();
    header('Location: /register');
    exit;
}