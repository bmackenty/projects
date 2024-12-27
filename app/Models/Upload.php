<?php

class Upload {
    private $pdo;
    private $allowedTypes = ['application/pdf', 'image/png', 'image/jpeg', 'image/jpg'];
    private $maxFileSize = 5242880; // 5MB

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getUploadsByTaskId($task_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM task_uploads WHERE task_id = ? ORDER BY uploaded_at DESC');
        $stmt->execute([$task_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addUpload($task_id, $file) {
        if (!$this->validateFile($file)) {
            throw new Exception('Invalid file type or size');
        }

        $filename = $this->generateUniqueFilename($file['name']);
        $uploadPath = $this->getUploadPath($filename);

        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new Exception('Failed to move uploaded file');
        }

        $stmt = $this->pdo->prepare('
            INSERT INTO task_uploads (task_id, filename, original_filename, file_type, file_size) 
            VALUES (?, ?, ?, ?, ?)
        ');

        return $stmt->execute([
            $task_id,
            $filename,
            $file['name'],
            $file['type'],
            $file['size']
        ]);
    }

    private function validateFile($file) {
        return in_array($file['type'], $this->allowedTypes) && 
               $file['size'] <= $this->maxFileSize && 
               $file['error'] === UPLOAD_ERR_OK;
    }

    private function generateUniqueFilename($originalName) {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        return uniqid() . '_' . time() . '.' . $extension;
    }

    private function getUploadPath($filename) {
        $uploadDir = __DIR__ . '/../../public/uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        return $uploadDir . $filename;
    }

    public function fileExists($filename) {
        $path = $this->getUploadPath($filename);
        return file_exists($path) && is_readable($path);
    }

    public function getFilePath($filename) {
        if (!$this->fileExists($filename)) {
            throw new Exception('File not found or not accessible');
        }
        return $this->getUploadPath($filename);
    }

    public function hasUploads($task_id) {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM task_uploads WHERE task_id = ?');
        $stmt->execute([$task_id]);
        return $stmt->fetchColumn() > 0;
    }

    public function viewFile($filename) {
        try {
            $filePath = $this->getFilePath($filename);
            // Serve the file...
            header('Content-Type: ' . mime_content_type($filePath));
            readfile($filePath);
        } catch (Exception $e) {
            // Handle the error appropriately
            header('HTTP/1.0 404 Not Found');
            echo 'File not available';
        }
    }

    public function renameFile($upload_id, $new_filename) {
        try {
            // Get current upload info
            $stmt = $this->pdo->prepare('SELECT filename, original_filename FROM task_uploads WHERE id = ?');
            $stmt->execute([$upload_id]);
            $upload = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$upload) {
                throw new Exception('Upload not found');
            }

            // Update the original_filename in database
            $stmt = $this->pdo->prepare('UPDATE task_uploads SET original_filename = ? WHERE id = ?');
            return $stmt->execute([$new_filename, $upload_id]);
        } catch (Exception $e) {
            throw new Exception('Failed to rename file: ' . $e->getMessage());
        }
    }
}