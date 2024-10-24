<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = getDatabaseConnection();
$user_id = $_SESSION['user_id'];
$error_message = '';
$success = true;

// Ambil informasi pengguna dari database
$user_query = $conn->query("SELECT username, email, photo FROM users WHERE id = $user_id");
if ($user_query) {
    $user = $user_query->fetch_assoc();
    if ($user) {
        $username = $user['username'];
        $email = $user['email'];
        $photo = $user['photo'] ? $user['photo'] : 'default_profile.png';
    } else {
        header("Location: dashboard.php");
        exit();
    }
} else {
    header("Location: dashboard.php");
    exit();
}

// Proses update profil
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update username jika ada
    if (!empty($_POST['new_username'])) {
        $new_username = $_POST['new_username'];
        
        // Cek apakah username sudah ada
        $check_username = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $check_username->bind_param("si", $new_username, $user_id);
        $check_username->execute();
        $result_username = $check_username->get_result();
        
        if ($result_username->num_rows > 0) {
            $error_message .= "Username already exists! ";
            $success = false;
        } else {
            $update_username = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
            $update_username->bind_param("si", $new_username, $user_id);
            $update_username->execute();
            $update_username->close();
        }
        $check_username->close();
    }

    // Update email jika ada
    if (!empty($_POST['new_email'])) {
        $new_email = $_POST['new_email'];
        
        // Cek apakah email sudah ada
        $check_email = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check_email->bind_param("si", $new_email, $user_id);
        $check_email->execute();
        $result_email = $check_email->get_result();
        
        if ($result_email->num_rows > 0) {
            $error_message .= "Email already exists! ";
            $success = false;
        } else {
            $update_email = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
            $update_email->bind_param("si", $new_email, $user_id);
            $update_email->execute();
            $update_email->close();
        }
        $check_email->close();
    }

    // Update password jika ada
    if (!empty($_POST['new_password'])) {
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $new_password, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    // Update foto profil jika ada upload baru
    if (!empty($_FILES['photo']['name'])) {
        $photo_name = $_FILES['photo']['name'];
        $photo_tmp = $_FILES['photo']['tmp_name'];
        $upload_dir = "user_uploads/$user_id/";

        // Buat direktori jika belum ada
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Pindahkan file yang diunggah ke direktori user
        if (move_uploaded_file($photo_tmp, $upload_dir . $photo_name)) {
            // Simpan jalur foto di database
            $photo_path = $upload_dir . $photo_name;
            $update_photo = $conn->prepare("UPDATE users SET photo = ? WHERE id = ?");
            $update_photo->bind_param("si", $photo_path, $user_id);
            $update_photo->execute();
            $update_photo->close();
        }
    }

    // Jika tidak ada error, redirect ke dashboard
    if ($success) {
        header("Location: dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .alert {
            margin-bottom: 20px;
        }
    </style>
    <script>
        function updateCurrentPasswordDisplay() {
            const newPasswordInput = document.getElementById('new_password');
            const currentPasswordInput = document.getElementById('current_password');

            // Mengubah current password input menjadi jumlah karakter dari new password
            currentPasswordInput.value = '*'.repeat(newPasswordInput.value.length);
        }

        document.addEventListener("DOMContentLoaded", function() {
            const newPasswordInput = document.getElementById('new_password');
            newPasswordInput.addEventListener('input', updateCurrentPasswordDisplay);

        // Auto hide alert after 3 seconds
        setTimeout(function() {
                const alert = document.querySelector('.alert');
                if(alert) {
                    alert.style.display = 'none';
                }
            }, 3000);
        });
    </script>
</head>
<body>

<div class="container mt-5">
    <h1>Edit Profile</h1>

    <?php if (!empty($error_message)): ?>
    <div class="alert alert-danger" role="alert">
        <?php echo $error_message; ?>
    </div>
    <?php endif; ?>

    <div class="mb-4 text-center">
        <img src="<?= htmlspecialchars($photo) ?>" alt="Profile Picture" class="img-fluid rounded-circle" style="width: 150px; height: 150px;">
    </div>

    <form method="POST" enctype="multipart/form-data">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Current Username</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($username) ?>" readonly>
            </div>
            <div class="col-md-6">
                <label for="new_username" class="form-label">New Username</label>
                <input type="text" class="form-control" id="new_username" name="new_username">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Current Email</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($email) ?>" readonly>
            </div>
            <div class="col-md-6">
                <label for="new_email" class="form-label">New Email Address</label>
                <input type="email" class="form-control" id="new_email" name="new_email">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Current Password</label>
                <input type="password" class="form-control" id="current_password" value="********" readonly>
                <small class="form-text text-muted">Leave the password field empty if you don't want to change it.</small>
            </div>
            <div class="col-md-6">
                <label for="new_password" class="form-label">New Password (Optional)</label>
                <input type="password" class="form-control" id="new_password" name="new_password">
            </div>
        </div>

        <div class="mb-3">
            <label for="photo" class="form-label">Upload New Profile Picture</label>
            <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
