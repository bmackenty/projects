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
            ORDER BY 
                CASE 
                    WHEN t.status = "completed" THEN 1 
                    ELSE 0 
                END,
                t.last_updated DESC
        ');
        $stmt->execute([$project_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function getTask($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createTask($name, $description, $status, $time, $parent_task_id = null, $due_date = null) {
        $stmt = $this->pdo->prepare('
            INSERT INTO tasks (name, description, status, time, parent_task_id, due_date)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        
        $stmt->execute([$name, $description, $status, $time, $parent_task_id, $due_date]);
        return $this->pdo->lastInsertId();
    }

    public function updateTask($id, $name, $description, $status, $time, $parent_task_id = null, $due_date = null) {
        $stmt = $this->pdo->prepare('
            UPDATE tasks 
            SET name = ?, description = ?, status = ?, time = ?, parent_task_id = ?, due_date = ?, last_updated = NOW() 
            WHERE id = ?
        ');
        return $stmt->execute([$name, $description, $status, $time, $parent_task_id, $due_date, $id]);
    }

    public function assignTaskToProject($task_id, $project_id) {
        $stmt = $this->pdo->prepare('
            INSERT INTO task_to_project_relations (task_id, project_id) 
            VALUES (?, ?)
        ');
        return $stmt->execute([$task_id, $project_id]);
    }

    public function getChildTasks($task_id) {
        $stmt = $this->pdo->prepare('
            SELECT t.* 
            FROM tasks t
            WHERE t.parent_task_id = ?
            ORDER BY t.last_updated DESC
        ');
        $stmt->execute([$task_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getParentTask($task_id) {
        $stmt = $this->pdo->prepare('
            SELECT p.* 
            FROM tasks t
            JOIN tasks p ON t.parent_task_id = p.id
            WHERE t.id = ?
        ');
        $stmt->execute([$task_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllParentTasks($project_id) {
        $stmt = $this->pdo->prepare('
            SELECT t.* 
            FROM tasks t
            JOIN task_to_project_relations r ON t.id = r.task_id
            WHERE r.project_id = ? AND t.parent_task_id IS NULL
            ORDER BY t.last_updated DESC
        ');
        $stmt->execute([$project_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTaskHierarchy($task_id) {
        $hierarchy = [];
        $current = $this->getTask($task_id);
        
        while ($current) {
            array_unshift($hierarchy, $current);
            $current = $current['parent_task_id'] ? $this->getTask($current['parent_task_id']) : null;
        }
        
        return $hierarchy;
    }

    public function deleteTask($id) {
        try {
            $this->pdo->beginTransaction();

            // Delete task assignments first
            $stmt = $this->pdo->prepare('DELETE FROM task_assignments WHERE task_id = ?');
            $stmt->execute([$id]);

            // Delete task comments
            $stmt = $this->pdo->prepare('DELETE FROM comments WHERE task_id = ?');
            $stmt->execute([$id]);

            // Delete task uploads
            $stmt = $this->pdo->prepare('DELETE FROM task_uploads WHERE task_id = ?');
            $stmt->execute([$id]);

            // Delete task project relations
            $stmt = $this->pdo->prepare('DELETE FROM task_to_project_relations WHERE task_id = ?');
            $stmt->execute([$id]);

            // Delete the task itself
            $stmt = $this->pdo->prepare('DELETE FROM tasks WHERE id = ?');
            $stmt->execute([$id]);

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}