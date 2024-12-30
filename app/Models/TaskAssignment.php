<?php

class TaskAssignment {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function assignTask($task_id, $user_id) {
        try {
            // First verify the task and user exist
            $checkTask = $this->pdo->prepare('SELECT id FROM tasks WHERE id = ?');
            $checkTask->execute([$task_id]);
            if (!$checkTask->fetch()) {
                error_log("Task assignment failed: Task ID $task_id does not exist");
                return false;
            }

            $checkUser = $this->pdo->prepare('SELECT id FROM users WHERE id = ?');
            $checkUser->execute([$user_id]);
            if (!$checkUser->fetch()) {
                error_log("Task assignment failed: User ID $user_id does not exist");
                return false;
            }

            // Check if assignment already exists
            $checkAssignment = $this->pdo->prepare('SELECT id FROM task_assignments WHERE task_id = ?');
            $checkAssignment->execute([$task_id]);
            $existingAssignment = $checkAssignment->fetch();

            if ($existingAssignment) {
                // Update existing assignment
                $stmt = $this->pdo->prepare('UPDATE task_assignments SET user_id = ? WHERE task_id = ?');
                $result = $stmt->execute([$user_id, $task_id]);
            } else {
                // Create new assignment
                $stmt = $this->pdo->prepare('INSERT INTO task_assignments (task_id, user_id) VALUES (?, ?)');
                $result = $stmt->execute([$task_id, $user_id]);
            }

            if (!$result) {
                $error = $stmt->errorInfo();
                error_log("Task assignment failed: " . print_r($error, true));
                return false;
            }

            return true;
        } catch (PDOException $e) {
            error_log("Task assignment exception: " . $e->getMessage());
            throw $e;
        }
    }

    public function unassignTask($task_id) {
        $stmt = $this->pdo->prepare('DELETE FROM task_assignments WHERE task_id = ?');
        return $stmt->execute([$task_id]);
    }

    public function getTaskAssignment($task_id) {
        $stmt = $this->pdo->prepare('
            SELECT ta.*, u.name as user_name, u.email as user_email 
            FROM task_assignments ta
            JOIN users u ON ta.user_id = u.id
            WHERE ta.task_id = ?
        ');
        $stmt->execute([$task_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserAssignedTasks($user_id) {
        $stmt = $this->pdo->prepare('
            SELECT t.*, p.id as project_id, p.name as project_name
            FROM tasks t
            JOIN task_assignments ta ON t.id = ta.task_id
            JOIN task_to_project_relations tpr ON t.id = tpr.task_id
            JOIN projects p ON tpr.project_id = p.id
            WHERE ta.user_id = ?
            ORDER BY t.due_date ASC, t.last_updated DESC
        ');
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 