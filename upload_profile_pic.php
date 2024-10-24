    <?php
    session_start();
    include 'database.php';

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profilePic'])) {
        $user_id = $_SESSION['user_id'];
        $file = $_FILES['profilePic'];

        // Pastikan file diunggah dengan benar
        if ($file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'user_uploads/' . $user_id . '/'; // Folder berdasarkan ID pengguna
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true); // Buat folder jika tidak ada
            }
            $fileName = uniqid() . '-' . basename($file['name']);
            $uploadFile = $uploadDir . $fileName;

            // Memindahkan file ke direktori upload
            if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                // Update database dengan path baru
                $conn = getDatabaseConnection();
                $stmt = $conn->prepare("UPDATE users SET photo = ? WHERE id = ?");
                $stmt->bind_param("si", $uploadFile, $user_id);

                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Profile picture updated successfully.', 'photo' => $uploadFile]);
                    exit();
                } else {
                    echo json_encode(['success' => false, 'message' => 'Database update failed.']);
                    exit();
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file.']);
                exit();
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'File upload error.']);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        exit();
    }
