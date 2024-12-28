<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/Task.php';
require_once __DIR__ . '/../Models/Comment.php';
require_once __DIR__ . '/../Models/Project.php';
require_once __DIR__ . '/../Services/Logger.php';
require_once __DIR__ . '/../Models/Upload.php';

class TaskController {
    private $taskModel;
    private $commentModel;
    private $projectModel;
    private $uploadModel;
    private $logger;
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->taskModel = new Task($pdo);
        $this->commentModel = new Comment($pdo);
        $this->projectModel = new Project($pdo);
        $this->uploadModel = new Upload($pdo);
        $this->logger = new Logger();
        $this->pdo = $pdo;
    }

    public function showCreateForm($project_id) {
        $projectTasks = $this->taskModel->getAllTasksByProjectId($project_id);
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

        if (empty($name) || empty($description)) {
            throw new Exception('Name and description are required');
        }

        try {
            $this->pdo->beginTransaction();
            $task_id = $this->taskModel->createTask($name, $description, $status, $time, $parent_task_id, $due_date);
            $this->taskModel->assignTaskToProject($task_id, $project_id);
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

            try {
                $this->commentModel->createComment($task_id, $user_id, $content);
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
        try {
            $task = $this->taskModel->getTask($id);
            if (!$task) {
                require_once __DIR__ . '/../Views/errors/task_not_found.php';
                return;
            }

            $project = $this->projectModel->getProjectByTaskId($id);
            $commentModel = $this->commentModel;
            $uploadModel = $this->uploadModel;
            
            // Get task hierarchy and child tasks
            $taskHierarchy = $this->taskModel->getTaskHierarchy($id);
            $childTasks = $this->taskModel->getChildTasks($id);

            require_once __DIR__ . '/../Views/tasks/view.php';
        } catch (Exception $e) {
            $this->logger->error('Error viewing task', ['error' => $e->getMessage()]);
            require_once __DIR__ . '/../Views/errors/task_error.php';
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




}