<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Services/Logger.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']) ? true : false;
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
        
        if ($remember) {
            // Generate a secure token
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));
            
            // Store the remember token in the database
            $userModel->storeRememberToken($user['id'], $token, $expiry);
            
            // Set the remember me cookie
            setcookie('remember_token', $token, strtotime('+30 days'), '/', '', true, true);
        }
        
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