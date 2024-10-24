<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Not authenticated']));
}

$conn = getDatabaseConnection();
$user_id = $_SESSION['user_id'];

// Ambil semua todo list untuk user ini, urutkan berdasarkan ID
$result = $conn->query("SELECT id FROM todo_lists WHERE user_id = $user_id ORDER BY id ASC");
$ids = [];
while ($row = $result->fetch_assoc()) {
    $ids[] = $row['id'];
}

// Update ID secara berurutan
$success = true;
$conn->begin_transaction();

try {
    // Buat temporary table
    $conn->query("CREATE TEMPORARY TABLE temp_ids (old_id INT, new_id INT)");
    
    // Isi temporary table dengan mapping ID lama ke ID baru
    foreach ($ids as $index => $old_id) {
        $new_id = $index + 1;
        $conn->query("INSERT INTO temp_ids (old_id, new_id) VALUES ($old_id, $new_id)");
    }
    
    // Update ID di tabel utama menggunakan temporary table
    $conn->query("UPDATE todo_lists t1
                 JOIN temp_ids t2 ON t1.id = t2.old_id
                 SET t1.id = t2.new_id");
    
    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>