<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/TaskAssignment.php';
require_once __DIR__ . '/../Services/Logger.php';

class UserController {
    private $userModel;
    private $taskAssignmentModel;
    private $logger;

    public function __construct() {
        global $pdo;
        $this->userModel = new User($pdo);
        $this->taskAssignmentModel = new TaskAssignment($pdo);
        $this->logger = new Logger();
    }

    public function profile() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    
        $user = $this->userModel->getUser($_SESSION['user']['id']);
        $assignedTasks = $this->taskAssignmentModel->getUserAssignedTasks($_SESSION['user']['id']);
        
        require_once __DIR__ . '/../Views/users/profile.php';
    }

    public function update() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $name = $_POST['name'] ?? '';
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $new_password_confirm = $_POST['new_password_confirm'] ?? '';

        // Add validation and update logic from ProfileController
        // ... (copy the update logic from ProfileController)

        header('Location: /users/profile?edit=1');
        exit;
    }

    public function view($id) {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /login');
            exit;
        }

        $user = $this->userModel->getUser($id);
        if (!$user) {
            require_once __DIR__ . '/../Views/errors/user_not_found.php';
            return;
        }

        $assignedTasks = $this->taskAssignmentModel->getUserAssignedTasks($id);
        require_once __DIR__ . '/../Views/users/view.php';
    }
} 