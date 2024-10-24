<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_id = $_POST['task_id'];

    $conn = new mysqli("localhost", "root", "", "todo_db");

    $stmt = $conn->prepare("SELECT is_completed FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($is_completed);
    $stmt->fetch();

    // Toggle the completion status
    $new_status = !$is_completed;
    $stmt = $conn->prepare("UPDATE tasks SET is_completed = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_status, $task_id);
    $stmt->execute();

    echo $new_status ? "Completed" : "Incomplete";  // Return the new status for updating the UI
}
