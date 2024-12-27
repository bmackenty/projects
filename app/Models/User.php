<?php

class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT id, email, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($email, $password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare("INSERT INTO users (email, password, role, created_at) VALUES (?, ?, 'user', NOW())");
        $stmt->execute([$email, $hashed_password]);
        
        return $this->pdo->lastInsertId();
    }
}