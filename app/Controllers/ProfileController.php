<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Services/Logger.php';

class ProfileController {
    private $userModel;
    private $logger;

    public function __construct() {
        global $pdo;
        $this->userModel = new User($pdo);
        $this->logger = new Logger();
    }

    public function index() {
        if (!isset($_SESSION['user'])) {
            $this->logger->warning('Unauthorized profile access attempt');
            header('Location: /login');
            exit;
        }

        try {
            $user = $this->userModel->findById($_SESSION['user']['id']);
            
            if (!$user) {
                $this->logger->error('User not found', ['user_id' => $_SESSION['user']['id']]);
                require_once __DIR__ . '/../Views/errors/user_not_found.php';
                return;
            }

            require_once __DIR__ . '/../Views/profile.php';
        } catch (Exception $e) {
            $this->logger->error('Error loading profile', ['error' => $e->getMessage()]);
            require_once __DIR__ . '/../Views/errors/user_error.php';
        }
    }

    public function update() {
        if (!isset($_SESSION['user'])) {
            $this->logger->warning('Unauthorized profile update attempt');
            header('Location: /login');
            exit;
        }

        $name = $_POST['name'] ?? '';
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $new_password_confirm = $_POST['new_password_confirm'] ?? '';

        try {
            // Validate name
            if (empty($name)) {
                $this->logger->warning('Profile update failed: Name is required', ['user_id' => $_SESSION['user']['id']]);
                $_SESSION['error'] = 'Name is required';
                header('Location: /profile');
                exit;
            }

            // If changing password, validate password fields
            if (!empty($new_password)) {
                if (empty($current_password)) {
                    $this->logger->warning('Profile update failed: Current password is required', ['user_id' => $_SESSION['user']['id']]);
                    $_SESSION['error'] = 'Current password is required';
                    header('Location: /profile');
                    exit;
                }

                if ($new_password !== $new_password_confirm) {
                    $this->logger->warning('Profile update failed: New passwords do not match', ['user_id' => $_SESSION['user']['id']]);
                    $_SESSION['error'] = 'New passwords do not match';
                    header('Location: /profile');
                    exit;
                }

                if (!$this->userModel->verifyPassword($_SESSION['user']['id'], $current_password)) {
                    $this->logger->warning('Profile update failed: Invalid current password', ['user_id' => $_SESSION['user']['id']]);
                    $_SESSION['error'] = 'Current password is incorrect';
                    header('Location: /profile');
                    exit;
                }

                $this->userModel->updateProfile($_SESSION['user']['id'], $name, $new_password);
            } else {
                $this->userModel->updateProfile($_SESSION['user']['id'], $name);
            }

            $this->logger->info('Profile updated successfully', ['user_id' => $_SESSION['user']['id']]);
            $_SESSION['success'] = 'Profile updated successfully';
            header('Location: /profile');
            exit;

        } catch (Exception $e) {
            $this->logger->error('Error updating profile', [
                'user_id' => $_SESSION['user']['id'],
                'error' => $e->getMessage()
            ]);
            $_SESSION['error'] = 'An error occurred while updating your profile';
            header('Location: /profile');
            exit;
        }
    }
} 