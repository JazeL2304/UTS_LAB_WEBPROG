<?php
function getDatabaseConnection() {
    $servername = "localhost";
    $username = "root"; // Change if you have a different username
    $password = "230404";     // Change if you have a different password
    $dbname = "todo_db"; // Your database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>
