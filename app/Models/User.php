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

    public function create($name, $email, $password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, 'user', NOW())");
        $stmt->execute([$name, $email, $hashed_password]);
        
        return $this->pdo->lastInsertId();
    }

    public function storeRememberToken($user_id, $token, $expiry) {
        $stmt = $this->pdo->prepare("
            INSERT INTO remember_tokens (user_id, token, expires_at) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE token = ?, expires_at = ?
        ");
        return $stmt->execute([$user_id, $token, $expiry, $token, $expiry]);
    }

    public function findByRememberToken($token) {
        $stmt = $this->pdo->prepare("
            SELECT u.* 
            FROM users u 
            JOIN remember_tokens rt ON u.id = rt.user_id 
            WHERE rt.token = ? AND rt.expires_at > NOW()
        ");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteRememberToken($token) {
        $stmt = $this->pdo->prepare("DELETE FROM remember_tokens WHERE token = ?");
        return $stmt->execute([$token]);
    }

    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile($id, $name, $password = null) {
        if ($password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("UPDATE users SET name = ?, password = ? WHERE id = ?");
            return $stmt->execute([$name, $hashed_password, $id]);
        } else {
            $stmt = $this->pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
            return $stmt->execute([$name, $id]);
        }
    }

    public function verifyPassword($id, $password) {
        $stmt = $this->pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user && password_verify($password, $user['password']);
    }

    public function getUser($id) {
        $stmt = $this->pdo->prepare('SELECT id, name, email, role, created_at FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllUsers() {
        $stmt = $this->pdo->query('SELECT id, name, email, role FROM users ORDER BY name');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}