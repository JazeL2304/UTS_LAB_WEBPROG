<?php
session_start();
include 'database.php';

$conn = getDatabaseConnection();
$user_id = $_SESSION['user_id'];

// Ambil password dari database
$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    echo $user['password']; // Tampilkan password saat ini
} else {
    echo "User not found";
}

$stmt->close();
$conn->close();
?>