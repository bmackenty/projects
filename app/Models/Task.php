<?php

class Task {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllTasksByProjectId($project_id) {
        $stmt = $this->pdo->prepare('
            SELECT t.* 
            FROM tasks t
            JOIN task_to_project_relations r ON t.id = r.task_id
            WHERE r.project_id = ?
            ORDER BY t.last_updated DESC
        ');
        $stmt->execute([$project_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTask($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createTask($name, $description, $status, $time) {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO tasks (name, description, status, time, last_updated) 
                VALUES (?, ?, ?, ?, NOW())
            ');
            $stmt->execute([$name, $description, $status, $time]);
            $task_id = $this->pdo->lastInsertId();
            $this->pdo->commit();
            return $task_id;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function updateTask($id, $name, $description, $status, $time) {
        $stmt = $this->pdo->prepare('
            UPDATE tasks 
            SET name = ?, description = ?, status = ?, time = ?, last_updated = NOW() 
            WHERE id = ?
        ');
        return $stmt->execute([$name, $description, $status, $time, $id]);
    }

    public function assignTaskToProject($task_id, $project_id) {
        $stmt = $this->pdo->prepare('
            INSERT INTO task_to_project_relations (task_id, project_id) 
            VALUES (?, ?)
        ');
        return $stmt->execute([$task_id, $project_id]);
    }
}