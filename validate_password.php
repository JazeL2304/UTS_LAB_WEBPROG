<?php
session_start();
include 'database.php';

// Cek autentikasi
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Not authenticated']));
}

// Cek input password
if (!isset($_POST['password'])) {
    die(json_encode(['success' => false, 'message' => 'No password provided']));
}

$conn = getDatabaseConnection();
$user_id = $_SESSION['user_id'];
$input_password = $_POST['password'];

// Debug: Log password yang diinput
error_log("Input password received: " . substr($input_password, 0, 3) . '***');

// Ambil password dari database menggunakan prepared statement
$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    $stored_password = trim($user['password']); // Hapus whitespace yang mungkin ada
    
    // Debug informasi (jangan tampilkan password lengkap di log)
    error_log("Stored password hash length: " . strlen($stored_password));
    
    // Cek apakah password yang tersimpan sudah dalam bentuk hash
    $is_hashed = strlen($stored_password) > 40 && strpos($stored_password, '$2y$') === 0;
    
    if ($is_hashed) {
        // Jika password sudah di-hash, gunakan password_verify
        if (password_verify($input_password, $stored_password)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Incorrect password'
            ]);
        }
    } else {
        // Jika password belum di-hash (compatibility mode)
        if ($input_password === $stored_password) {
            // Optional: Update password ke bentuk hash untuk keamanan
            $hashed_password = password_hash($input_password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_stmt->bind_param("si", $hashed_password, $user_id);
            $update_stmt->execute();
            $update_stmt->close();
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Incorrect password',
                'debug' => [
                    'input_length' => strlen($input_password),
                    'stored_length' => strlen($stored_password),
                    'is_hashed' => false
                ]
            ]);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
}

$stmt->close();
$conn->close();
?>