<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/Project.php';
require_once __DIR__ . '/../Models/Task.php';
require_once __DIR__ . '/../Models/Comment.php';

class HomeController {
    private $projectModel;
    private $taskModel;
    private $commentModel;

    public function __construct() {
        global $pdo;
        $this->projectModel = new Project($pdo);
        $this->taskModel = new Task($pdo);
        $this->commentModel = new Comment($pdo);
    }

    public function index() {
        $projects = $this->projectModel->getAllProjects();
        $projectTasks = [];
        foreach ($projects as $project) {
            $projectTasks[$project['id']] = $this->taskModel->getAllTasksByProjectId($project['id']);
        }
        $commentModel = $this->commentModel;
        
        require_once __DIR__ . '/../Views/home.php';
    }
}