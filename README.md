# Task Manager API

A simple REST API for managing tasks built with vanilla PHP and SQLite.

## Features

- Create, read, update tasks
- Filter tasks by status
- SQLite database
- Docker support
- Clean MVC-like architecture

## API Endpoints

### Create Task
```
POST /tasks
Content-Type: application/json

{
    "title": "Complete project",
    "description": "Finish the task manager API",
    "status": "pending"
}
```

### Get All Tasks
```
GET /tasks
GET /tasks?status=completed
```

### Get Single Task
```
GET /tasks/{id}
```

### Update Task
```
PUT /tasks/{id}
Content-Type: application/json

{
    "title": "Updated title",
    "description": "Updated description",
    "status": "completed"
}
```

## Installation & Setup

### Option 1: Using Docker (Recommended)

1. Clone the repository:
```bash
git clone https://github.com/Estifanos12/demo-php
cd demo-php
```

2. Build and run with Docker:
```bash
docker build -t task-manager-api .
docker run -p 8080:80 task-manager-api
```

3. The API will be available at `http://localhost:8080`

### Option 2: Local Development

1. Ensure you have PHP 8.0+ and SQLite installed
2. Clone the repository
3. Initialize the database:
```bash
php database/init.php
```
4. Start the PHP development server:
```bash
php -S localhost:8080 -t public/
```

## Project Structure

```
├── database/
│   ├── init.php          # Database initialization
│   └── tasks.db          # SQLite database file
├── models/
│   └── Task.php          # Task model
├── controllers/
│   └── TaskController.php # Task controller
├── routes/
│   └── Router.php        # Simple router
├── public/
│   └── index.php         # Entry point
├── Dockerfile
├── docker-compose.yml
└── README.md
```

## Sample API Usage

### Create a new task
```bash
curl -X POST http://localhost:8080/tasks \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Learn PHP",
    "description": "Study PHP fundamentals",
    "status": "pending"
  }'
```

### Get all tasks
```bash
curl http://localhost:8080/tasks
```

### Get tasks by status
```bash
curl http://localhost:8080/tasks?status=completed
```

### Update a task
```bash
curl -X PUT http://localhost:8080/tasks/1 \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Learn PHP - Updated",
    "description": "Study PHP fundamentals and OOP",
    "status": "in-progress"
  }'
```

## Database Schema

The SQLite database contains a single `tasks` table:

```sql
CREATE TABLE tasks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status VARCHAR(50) DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

## Status Values

Valid task statuses:
- `pending`
- `in-progress`
- `completed`

## Error Handling

The API returns appropriate HTTP status codes:
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `404` - Not Found
- `500` - Internal Server Error

All responses are in JSON format with a consistent structure.
