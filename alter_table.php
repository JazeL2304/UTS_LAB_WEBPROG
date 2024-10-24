<?php
include 'database.php';

$conn = getDatabaseConnection();

$sql = "ALTER TABLE todo_lists ADD COLUMN IF NOT EXISTS completed TINYINT(1) DEFAULT 0";

if ($conn->query($sql) === TRUE) {
    echo "Table modified successfully";
} else {
    echo "Error modifying table: " . $conn->error;
}