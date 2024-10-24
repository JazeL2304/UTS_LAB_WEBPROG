<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getDatabaseConnection();
    $list_id = $_POST['list_id'];
    $title = $_POST['title'];

    // Prepare update statement
    $stmt = $conn->prepare("UPDATE todo_lists SET title = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $title, $list_id, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update']);
    }
}


?>
