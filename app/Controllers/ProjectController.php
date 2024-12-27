<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/Project.php';
require_once __DIR__ . '/../Services/Logger.php';
require_once __DIR__ . '/../Models/Task.php';
require_once __DIR__ . '/../Models/Comment.php';
require_once __DIR__ . '/../Models/Upload.php';

class ProjectController {
    private $projectModel;
    private $taskModel;  
    private $commentModel;
    private $logger;

    public function __construct() {
        global $pdo;
        $this->projectModel = new Project($pdo);
        $this->taskModel = new Task($pdo); 
        $this->commentModel = new Comment($pdo);
        $this->logger = new Logger();
    }

    public function index() {
        // Check if user is admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /login');
            exit;
        }
    
        $projects = $this->projectModel->getAllProjects();
        $projectTasks = [];
        foreach ($projects as $project) {
            $projectTasks[$project['id']] = $this->taskModel->getAllTasksByProjectId($project['id']);
        }
        require_once __DIR__ . '/../Views/projects/index.php';
    }
    

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            try {
                $this->projectModel->createProject($name, $description);
                $this->logger->info('Project created', ['name' => $name]);
                $_SESSION['success'] = 'Project created successfully';
                header('Location: /projects');
                exit;
            } catch (PDOException $e) {
                $this->logger->error('Error creating project', ['error' => $e->getMessage()]);
                $_SESSION['error'] = 'Error updating project: ' . $e->getMessage();
            }
        }
        require_once __DIR__ . '/../Views/projects/create.php';
    }

    public function view($id) {
        $project = $this->projectModel->getProject($id);
        $tasks = $this->taskModel->getAllTasksByProjectId($id);
        $commentModel = $this->commentModel;
        
        // Make Upload model available to the view
        global $pdo;
        $uploadModel = new Upload($pdo);
        
        require_once __DIR__ . '/../Views/projects/view.php';
    }


    public function edit($id) {
        $project = $this->projectModel->getProject($id);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            try {
                $this->projectModel->updateProject($id, $name, $description);
                $this->logger->info('Project updated', ['id' => $id]);
                $_SESSION['success'] = 'Project updated successfully';
                header('Location: /projects');
                exit;
            } catch (PDOException $e) {
                $this->logger->error('Error updating project', ['error' => $e->getMessage()]);
                $_SESSION['error'] = 'Error updating project: ' . $e->getMessage();
            }
        }
        require_once __DIR__ . '/../Views/projects/edit.php';
    }

    public function dashboard() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    
        $projects = $this->projectModel->getAllProjects();
        $projectTasks = [];
        foreach ($projects as $project) {
            $projectTasks[$project['id']] = $this->taskModel->getAllTasksByProjectId($project['id']);
        }
        $commentModel = $this->commentModel;  // Make commentModel available to the view

        require_once __DIR__ . '/../Views/dashboard.php';
    }



}