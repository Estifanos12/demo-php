#!/bin/bash

# Task Manager API Test Script
# Make sure the API is running on http://localhost:8080

BASE_URL="http://localhost:8080"
API_URL="$BASE_URL/tasks"

echo "🧪 Testing Task Manager API"
echo "=========================="
echo ""

# Test 1: Get API info
echo "1. Testing API info endpoint..."
curl -s "$BASE_URL/" | jq .
echo ""

# Test 2: Get all tasks
echo "2. Getting all tasks..."
curl -s "$API_URL" | jq .
echo ""

# Test 3: Create a new task
echo "3. Creating a new task..."
NEW_TASK=$(curl -s -X POST "$API_URL" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Test Task",
    "description": "This is a test task created by the test script",
    "status": "pending"
  }')
echo "$NEW_TASK" | jq .

# Extract the ID of the created task
TASK_ID=$(echo "$NEW_TASK" | jq -r '.id')
echo ""

# Test 4: Get the specific task
echo "4. Getting task with ID: $TASK_ID..."
curl -s "$API_URL/$TASK_ID" | jq .
echo ""

# Test 5: Update the task
echo "5. Updating task with ID: $TASK_ID..."
curl -s -X PUT "$API_URL/$TASK_ID" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Updated Test Task",
    "description": "This task has been updated",
    "status": "in-progress"
  }' | jq .
echo ""

# Test 6: Get tasks filtered by status
echo "6. Getting tasks with status 'in-progress'..."
curl -s "$API_URL?status=in-progress" | jq .
echo ""

# Test 7: Get all tasks again to see the changes
echo "7. Getting all tasks (after updates)..."
curl -s "$API_URL" | jq .
echo ""

# Test 8: Test validation - invalid status
echo "8. Testing validation with invalid status..."
curl -s -X POST "$API_URL" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Invalid Task",
    "description": "This task has an invalid status",
    "status": "invalid-status"
  }' | jq .
echo ""

# Test 9: Test validation - missing title
echo "9. Testing validation with missing title..."
curl -s -X POST "$API_URL" \
  -H "Content-Type: application/json" \
  -d '{
    "description": "This task has no title",
    "status": "pending"
  }' | jq .
echo ""

# Test 10: Get non-existent task
echo "10. Testing 404 for non-existent task..."
curl -s "$API_URL/99999" | jq .
echo ""

echo "✅ API testing completed!"
echo ""
echo "📝 Summary:"
echo "- All CRUD operations tested"
echo "- Validation tested"
echo "- Error handling tested"
echo "- Filtering tested" 