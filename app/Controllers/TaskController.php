<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/Task.php';
require_once __DIR__ . '/../Models/Comment.php';
require_once __DIR__ . '/../Models/Project.php';
require_once __DIR__ . '/../Services/Logger.php';
require_once __DIR__ . '/../Models/Upload.php';
require_once __DIR__ . '/../Models/TaskAssignment.php';
require_once __DIR__ . '/../Models/User.php';

class TaskController {
    private $taskModel;
    private $commentModel;
    private $projectModel;
    private $uploadModel;
    private $logger;
    private $pdo;
    private $taskAssignmentModel;
    private $userModel;

    public function __construct() {
        global $pdo;
        $this->taskModel = new Task($pdo);
        $this->commentModel = new Comment($pdo);
        $this->projectModel = new Project($pdo);
        $this->uploadModel = new Upload($pdo);
        $this->logger = new Logger();
        $this->pdo = $pdo;
        $this->taskAssignmentModel = new TaskAssignment($pdo);
        $this->userModel = new User($pdo);
    }

    public function showCreateForm($project_id) {
        $projectTasks = $this->taskModel->getAllTasksByProjectId($project_id);
        $users = $this->userModel->getAllUsers();
        require_once __DIR__ . '/../Views/tasks/create.php';
    }

    public function create($project_id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Invalid request method');
        }

        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $status = $_POST['status'] ?? 'pending';
        $time = $_POST['time'] ?? 0;
        $parent_task_id = !empty($_POST['parent_task_id']) ? $_POST['parent_task_id'] : null;
        $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
        $assigned_user = !empty($_POST['assigned_user']) ? $_POST['assigned_user'] : null;

        if (empty($name) || empty($description)) {
            throw new Exception('Name and description are required');
        }

        try {
            $this->pdo->beginTransaction();
            
            // Create the task
            $task_id = $this->taskModel->createTask($name, $description, $status, $time, $parent_task_id, $due_date);
            
            // Assign to project
            $this->taskModel->assignTaskToProject($task_id, $project_id);
            
            // Assign to user if specified
            if ($assigned_user) {
                $this->taskAssignmentModel->assignTask($task_id, $assigned_user);
            }
            
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function edit($id) {
        $task = $this->taskModel->getTask($id);
        if (!$task) {
            require_once __DIR__ . '/../Views/errors/task_not_found.php';
            return;
        }

        $project = $this->projectModel->getProjectByTaskId($id);
        $projectTasks = $this->taskModel->getAllTasksByProjectId($project['id']);
        $commentModel = $this->commentModel;
        
        $users = $this->userModel->getAllUsers();
        $assignment = $this->taskAssignmentModel->getTaskAssignment($id);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $status = $_POST['status'] ?? 'pending';
            $time = $_POST['time'] ?? 0;
            $parent_task_id = !empty($_POST['parent_task_id']) ? $_POST['parent_task_id'] : null;
            $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
    
            try {
                $this->taskModel->updateTask($id, $name, $description, $status, $time, $parent_task_id, $due_date);
                $this->logger->info('Task updated', ['id' => $id]);
                $_SESSION['success'] = 'Task updated successfully';
                header('Location: /projects');
                exit;
            } catch (PDOException $e) {
                $this->logger->error('Error updating task', ['error' => $e->getMessage()]);
                $_SESSION['error'] = 'Error updating task: ' . $e->getMessage();
            }

            // Handle assignment
            $assigned_user = !empty($_POST['assigned_user']) ? $_POST['assigned_user'] : null;
            if ($assigned_user) {
                $this->taskAssignmentModel->assignTask($id, $assigned_user);
            } else {
                $this->taskAssignmentModel->unassignTask($id);
            }
        }
        
        require_once __DIR__ . '/../Views/tasks/edit.php';
    }

    public function addComment($task_id) {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = $_POST['content'] ?? '';
            $user_id = $_SESSION['user']['id'];
            $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;

            try {
                $this->commentModel->createComment($task_id, $user_id, $content, $parent_id);
                $this->logger->info('Comment added', ['task_id' => $task_id]);
                $_SESSION['success'] = 'Comment added successfully';
            } catch (PDOException $e) {
                $this->logger->error('Error adding comment', ['error' => $e->getMessage()]);
                $_SESSION['error'] = 'Error adding comment: ' . $e->getMessage();
            }
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }

    public function view($id) {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        try {
            $task = $this->taskModel->getTask($id);
            if (!$task) {
                require_once __DIR__ . '/../Views/errors/task_not_found.php';
                return;
            }

            $project = $this->projectModel->getProjectByTaskId($id);
            $childTasks = $this->taskModel->getChildTasks($id);
            $taskHierarchy = $this->taskModel->getTaskHierarchy($id);
            $uploads = $this->uploadModel->getUploadsByTaskId($id);
            $assignment = $this->taskAssignmentModel->getTaskAssignment($id);
            
            // Initialize the comment model
            require_once __DIR__ . '/../Models/Comment.php';
            $commentModel = new Comment($this->pdo);

            require_once __DIR__ . '/../Views/tasks/view.php';
        } catch (Exception $e) {
            $this->logger->error('Error viewing task', ['error' => $e->getMessage()]);
            $_SESSION['error'] = 'Error viewing task: ' . $e->getMessage();
            header('Location: /dashboard');
            exit;
        }
    }

    public function uploadFile($task_id) {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        if (!isset($_FILES['file'])) {
            $_SESSION['error'] = 'No file uploaded';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        try {
            $uploadModel = new Upload($this->pdo);
            $uploadModel->addUpload($task_id, $_FILES['file']);
            
            $this->logger->info('File uploaded', ['task_id' => $task_id]);
            $_SESSION['success'] = 'File uploaded successfully';
        } catch (Exception $e) {
            $this->logger->error('Error uploading file', ['error' => $e->getMessage()]);
            $_SESSION['error'] = 'Error uploading file: ' . $e->getMessage();
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function serveFile($filename) {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        try {
            $uploadModel = new Upload($this->pdo);
            $filePath = $uploadModel->getFilePath($filename);
            
            if (!file_exists($filePath)) {
                header('HTTP/1.0 404 Not Found');
                echo 'File not found';
                return;
            }

            $mimeType = mime_content_type($filePath);
            header('Content-Type: ' . $mimeType);
            header('Content-Length: ' . filesize($filePath));
            header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
            
            readfile($filePath);
        } catch (Exception $e) {
            header('HTTP/1.0 404 Not Found');
            echo 'File not available';
        }
    }

    public function renameUpload($upload_id) {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        if (!isset($_POST['new_filename'])) {
            $_SESSION['error'] = 'New filename is required';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        try {
            $uploadModel = new Upload($this->pdo);
            $uploadModel->renameFile($upload_id, $_POST['new_filename']);
            
            $this->logger->info('File renamed', ['upload_id' => $upload_id]);
            $_SESSION['success'] = 'File renamed successfully';
        } catch (Exception $e) {
            $this->logger->error('Error renaming file', ['error' => $e->getMessage()]);
            $_SESSION['error'] = 'Error renaming file: ' . $e->getMessage();
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function delete($id) {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $_SESSION['error'] = 'Unauthorized access';
            header('Location: /dashboard');
            exit;
        }

        try {
            // Get the task and its project before deletion for redirection
            $task = $this->taskModel->getTask($id);
            $project = $this->projectModel->getProjectByTaskId($id);
            
            if (!$task) {
                throw new Exception('Task not found');
            }
            
            // Delete the task (transaction is handled in the model)
            $this->taskModel->deleteTask($id);
            
            $this->logger->info('Task deleted', ['task_id' => $id]);
            $_SESSION['success'] = 'Task deleted successfully';
            
            // Redirect back to the project view
            header("Location: /projects/view/{$project['id']}");
            exit;
            
        } catch (Exception $e) {
            $this->logger->error('Error deleting task', ['error' => $e->getMessage()]);
            $_SESSION['error'] = 'Error deleting task: ' . $e->getMessage();
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }




}