<?php

class Database {
    private $pdo;
    
    public function __construct() {
        $dbPath = __DIR__ . '/tasks.db';
        $this->pdo = new PDO("sqlite:$dbPath");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    public function init() {
        $sql = "
            CREATE TABLE IF NOT EXISTS tasks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                status VARCHAR(50) DEFAULT 'pending',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ";
        
        $this->pdo->exec($sql);
        
        // Insert some sample data
        $this->insertSampleData();
        
        echo "Database initialized successfully!\n";
        echo "Sample tasks have been created.\n";
    }
    
    private function insertSampleData() {
        $sampleTasks = [
            [
                'title' => 'Learn PHP Basics',
                'description' => 'Study PHP fundamentals and syntax',
                'status' => 'completed'
            ],
            [
                'title' => 'Build REST API',
                'description' => 'Create a simple REST API with vanilla PHP',
                'status' => 'in-progress'
            ],
            [
                'title' => 'Add Authentication',
                'description' => 'Implement JWT authentication for the API',
                'status' => 'pending'
            ]
        ];
        
        $stmt = $this->pdo->prepare("
            INSERT INTO tasks (title, description, status) 
            VALUES (:title, :description, :status)
        ");
        
        foreach ($sampleTasks as $task) {
            $stmt->execute($task);
        }
    }
    
    public function getConnection() {
        return $this->pdo;
    }
}

// Run initialization if this file is executed directly
if (php_sapi_name() === 'cli') {
    $db = new Database();
    $db->init();
} 