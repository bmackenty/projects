<?php

class Comment {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getCommentsByTaskId($task_id) {
        $stmt = $this->pdo->prepare('
            SELECT c.*, u.email as user_email 
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.task_id = ? 
            ORDER BY c.created_at DESC
        ');
        $stmt->execute([$task_id]);
        return $stmt->fetchAll();
    }

    public function createComment($task_id, $user_id, $content) {
        $stmt = $this->pdo->prepare('
            INSERT INTO comments (task_id, user_id, content) 
            VALUES (?, ?, ?)
        ');
        return $stmt->execute([$task_id, $user_id, $content]);
    }
}