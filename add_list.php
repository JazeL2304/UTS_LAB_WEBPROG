<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getDatabaseConnection();
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];

    // Insert new to-do list into database
    $stmt = $conn->prepare("INSERT INTO todo_lists (user_id, title) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $title);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id, 'title' => $title]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to insert']);
    }
}
?>
