<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Not authenticated']));
}

if (!isset($_POST['list_id']) || !isset($_POST['completed'])) {
    die(json_encode(['success' => false, 'message' => 'Missing parameters']));
}

$conn = getDatabaseConnection();
$list_id = (int)$_POST['list_id'];
$completed = (int)$_POST['completed'];
$user_id = $_SESSION['user_id'];

// Update dengan pengecekan user_id
$stmt = $conn->prepare("UPDATE todo_lists SET completed = ? WHERE id = ? AND user_id = ?");
$stmt->bind_param("iii", $completed, $list_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update status']);
}

$stmt->close();
$conn->close();
?>