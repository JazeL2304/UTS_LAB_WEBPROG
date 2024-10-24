<?php
session_start();
session_destroy(); // Menghentikan semua sesi
header("Location: login.php"); // Redirect ke halaman login
exit();
?>
