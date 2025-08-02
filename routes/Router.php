<?php

require_once __DIR__ . '/../controllers/TaskController.php';

class Router {
    private $routes = [];
    
    public function addRoute($method, $pattern, $handler) {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler
        ];
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove trailing slash
        $uri = rtrim($uri, '/');
        
        // If URI is empty, set to root
        if (empty($uri)) {
            $uri = '/';
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPattern($route['pattern'], $uri)) {
                $params = $this->extractParams($route['pattern'], $uri);
                return $this->executeHandler($route['handler'], $params);
            }
        }
        
        // No route found
        $this->sendNotFound();
    }
    
    private function matchPattern($pattern, $uri) {
        // Convert pattern to regex
        $regex = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';
        
        return preg_match($regex, $uri);
    }
    
    private function extractParams($pattern, $uri) {
        $params = [];
        
        // Extract parameter names from pattern
        preg_match_all('/\{([^}]+)\}/', $pattern, $paramNames);
        
        // Convert pattern to regex for extraction
        $regex = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';
        
        // Extract parameter values
        preg_match($regex, $uri, $paramValues);
        
        // Combine names and values
        for ($i = 0; $i < count($paramNames[1]); $i++) {
            $params[$paramNames[1][$i]] = $paramValues[$i + 1];
        }
        
        return $params;
    }
    
    private function executeHandler($handler, $params) {
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }
        
        if (is_array($handler)) {
            $controller = new $handler[0]();
            $method = $handler[1];
            return call_user_func_array([$controller, $method], $params);
        }
        
        throw new Exception('Invalid handler');
    }
    
    private function sendNotFound() {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Route not found'], JSON_PRETTY_PRINT);
        exit;
    }
    
    public function setupRoutes() {
        $taskController = new TaskController();
        
        // GET /tasks - Get all tasks
        $this->addRoute('GET', '/tasks', [$taskController, 'index']);
        
        // GET /tasks/{id} - Get single task
        $this->addRoute('GET', '/tasks/{id}', [$taskController, 'show']);
        
        // POST /tasks - Create task
        $this->addRoute('POST', '/tasks', [$taskController, 'store']);
        
        // PUT /tasks/{id} - Update task
        $this->addRoute('PUT', '/tasks/{id}', [$taskController, 'update']);
        
        // DELETE /tasks/{id} - Delete task (bonus)
        $this->addRoute('DELETE', '/tasks/{id}', [$taskController, 'delete']);
        
        // GET / - API info
        $this->addRoute('GET', '/', function() {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode([
                'message' => 'Task Manager API',
                'version' => '1.0.0',
                'endpoints' => [
                    'GET /tasks' => 'Get all tasks',
                    'GET /tasks/{id}' => 'Get single task',
                    'POST /tasks' => 'Create new task',
                    'PUT /tasks/{id}' => 'Update task',
                    'DELETE /tasks/{id}' => 'Delete task'
                ]
            ], JSON_PRETTY_PRINT);
            exit;
        });
    }
} 