<?php
session_start();

require_once __DIR__ . '/../Services/Logger.php';

$logger = new Logger();

if (isset($_SESSION['user'])) {
    $logger->info('User logged out', ['user_id' => $_SESSION['user']['id'], 'email' => $_SESSION['user']['email']]);
    unset($_SESSION['user']);
    session_destroy();
}

header('Location: /login');
exit;