<?php

require_once __DIR__ . '/../database/init.php';

class Task {
    private $pdo;
    
    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }
    
    public function getAll($status = null) {
        $sql = "SELECT * FROM tasks";
        $params = [];
        
        if ($status) {
            $sql .= " WHERE status = :status";
            $params[':status'] = $status;
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM tasks WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $sql = "
            INSERT INTO tasks (title, description, status) 
            VALUES (:title, :description, :status)
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'] ?? '',
            ':status' => $data['status'] ?? 'pending'
        ]);
        
        return $this->pdo->lastInsertId();
    }
    
    public function update($id, $data) {
        $sql = "
            UPDATE tasks 
            SET title = :title, 
                description = :description, 
                status = :status,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            ':id' => $id,
            ':title' => $data['title'],
            ':description' => $data['description'] ?? '',
            ':status' => $data['status'] ?? 'pending'
        ]);
        
        return $result && $stmt->rowCount() > 0;
    }
    
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        return $stmt->rowCount() > 0;
    }
    
    public function validateStatus($status) {
        $validStatuses = ['pending', 'in-progress', 'completed'];
        return in_array($status, $validStatuses);
    }
    
    public function validateData($data) {
        $errors = [];
        
        if (empty($data['title'])) {
            $errors[] = 'Title is required';
        }
        
        if (isset($data['status']) && !$this->validateStatus($data['status'])) {
            $errors[] = 'Status must be one of: pending, in-progress, completed';
        }
        
        return $errors;
    }
} 