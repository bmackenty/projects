<?php

class Project {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllProjects() {
        $stmt = $this->pdo->query('SELECT * FROM projects ORDER BY last_updated DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProject($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM projects WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createProject($name, $description) {
        $stmt = $this->pdo->prepare('
            INSERT INTO projects (name, description, last_updated) 
            VALUES (?, ?, NOW())
        ');
        return $stmt->execute([$name, $description]);
    }

    public function updateProject($id, $name, $description) {
        $stmt = $this->pdo->prepare('
            UPDATE projects 
            SET name = ?, description = ?, last_updated = NOW() 
            WHERE id = ?
        ');
        return $stmt->execute([$name, $description, $id]);
    }

    public function getProjectByTaskId($task_id) {
        $stmt = $this->pdo->prepare('
            SELECT p.* 
            FROM projects p
            JOIN task_to_project_relations r ON p.id = r.project_id
            WHERE r.task_id = ?
        ');
        $stmt->execute([$task_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}