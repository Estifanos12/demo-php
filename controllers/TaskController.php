<?php

require_once __DIR__ . '/../models/Task.php';

class TaskController {
    private $taskModel;
    
    public function __construct() {
        $this->taskModel = new Task();
    }
    
    public function index() {
        $status = $_GET['status'] ?? null;
        
        try {
            $tasks = $this->taskModel->getAll($status);
            $this->sendResponse(200, $tasks);
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'Internal server error']);
        }
    }
    
    public function show($id) {
        try {
            $task = $this->taskModel->getById($id);
            
            if (!$task) {
                $this->sendResponse(404, ['error' => 'Task not found']);
                return;
            }
            
            $this->sendResponse(200, $task);
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'Internal server error']);
        }
    }
    
    public function store() {
        $input = $this->getJsonInput();
        
        if (!$input) {
            $this->sendResponse(400, ['error' => 'Invalid JSON input']);
            return;
        }
        
        $errors = $this->taskModel->validateData($input);
        
        if (!empty($errors)) {
            $this->sendResponse(400, ['error' => 'Validation failed', 'details' => $errors]);
            return;
        }
        
        try {
            $id = $this->taskModel->create($input);
            $task = $this->taskModel->getById($id);
            $this->sendResponse(201, $task);
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'Internal server error']);
        }
    }
    
    public function update($id) {
        $input = $this->getJsonInput();
        
        if (!$input) {
            $this->sendResponse(400, ['error' => 'Invalid JSON input']);
            return;
        }
        
        $errors = $this->taskModel->validateData($input);
        
        if (!empty($errors)) {
            $this->sendResponse(400, ['error' => 'Validation failed', 'details' => $errors]);
            return;
        }
        
        try {
            $success = $this->taskModel->update($id, $input);
            
            if (!$success) {
                $this->sendResponse(404, ['error' => 'Task not found']);
                return;
            }
            
            $task = $this->taskModel->getById($id);
            $this->sendResponse(200, $task);
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'Internal server error']);
        }
    }
    
    public function delete($id) {
        try {
            $success = $this->taskModel->delete($id);
            
            if (!$success) {
                $this->sendResponse(404, ['error' => 'Task not found']);
                return;
            }
            
            $this->sendResponse(200, ['message' => 'Task deleted successfully']);
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'Internal server error']);
        }
    }
    
    private function getJsonInput() {
        $input = file_get_contents('php://input');
        return json_decode($input, true);
    }
    
    private function sendResponse($statusCode, $data) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }
} 