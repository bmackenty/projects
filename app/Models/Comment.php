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
            WHERE c.task_id = ? AND c.parent_id IS NULL
            ORDER BY c.created_at DESC
        ');
        $stmt->execute([$task_id]);
        $comments = $stmt->fetchAll();

        // Get replies for each comment
        foreach ($comments as &$comment) {
            $comment['replies'] = $this->getRepliesByCommentId($comment['id']);
        }

        return $comments;
    }

    public function getRepliesByCommentId($comment_id) {
        $stmt = $this->pdo->prepare('
            SELECT c.*, u.email as user_email 
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.parent_id = ?
            ORDER BY c.created_at ASC
        ');
        $stmt->execute([$comment_id]);
        return $stmt->fetchAll();
    }

    public function createComment($task_id, $user_id, $content, $parent_id = null) {
        $stmt = $this->pdo->prepare('
            INSERT INTO comments (task_id, user_id, content, parent_id) 
            VALUES (?, ?, ?, ?)
        ');
        return $stmt->execute([$task_id, $user_id, $content, $parent_id]);
    }
}