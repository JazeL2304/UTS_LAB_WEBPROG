<?php
header("Location: login.php");
exit;
session_start();

// Cek apakah pengguna sudah login
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Jika belum login, arahkan ke halaman login
header("Location: login.php");
exit();
?>
