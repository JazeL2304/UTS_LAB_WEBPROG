<?php
session_start();
include 'database.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil informasi pengguna dari database
$conn = getDatabaseConnection();
$user_id = $_SESSION['user_id'];

// Ambil data pengguna
$user_query = $conn->query("SELECT username, email, photo FROM users WHERE id = $user_id");

if ($user_query) {
    $user = $user_query->fetch_assoc();
    if ($user) {
        $username = $user['username'];
        $email = $user['email'];
        $photo = $user['photo'] ? $user['photo'] : 'default_profile.png';
    } else {
        $username = "Unknown User";
        $email = "Unknown Email";
        $photo = 'default_profile.png';
    }
} else {
    $username = "Unknown User";
    $email = "Unknown Email";
    $photo = 'default_profile.png';
}


// Ambil daftar todo list
$result = $conn->query("SELECT * FROM todo_lists WHERE user_id = $user_id ORDER BY completed DESC, id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
    --primary-gradient: linear-gradient(135deg, #0099CC, #66CCFF);
    --secondary-color: #ffffff;
    --text-color: #333333;
    --card-shadow: 0 8px 32px rgba(0,0,0,0.1);
}

/* Keyframes Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
    100% { transform: translateY(0px); }
}

@keyframes logoFloat {
    0% { transform: translateY(0) rotate(0deg); }
    25% { transform: translateY(-5px) rotate(2deg); }
    50% { transform: translateY(0) rotate(0deg); }
    75% { transform: translateY(5px) rotate(-2deg); }
    100% { transform: translateY(0) rotate(0deg); }
}

@keyframes profilePulse {
    0% {
        border-color: #66CCFF;
        box-shadow: 0 0 0 0 rgba(102,204,255,0.4);
    }
    70% {
        border-color: #0099CC;
        box-shadow: 0 0 0 5px rgba(102,204,255,0);
    }
    100% {
        border-color: #66CCFF;
        box-shadow: 0 0 0 0 rgba(102,204,255,0);
    }
}

@keyframes buttonPop {
    0% { transform: scale(1); }
    50% { transform: scale(0.95); }
    100% { transform: scale(1); }
}

@keyframes moveWave1 {
    0% { transform: translateX(0); }
    50% { transform: translateX(-25%); }
    100% { transform: translateX(-50%); }
}

@keyframes moveWave2 {
    0% { transform: translateX(0); }
    50% { transform: translateX(-15%); }
    100% { transform: translateX(-30%); }
}

/* Base Styles */
body {
    background: var(--primary-gradient);
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
    position: relative;
    overflow-x: hidden;
}

h1, h2, h3, h4, h5, .navbar-brand {
    font-family: 'Montserrat', sans-serif;
    font-weight: 700;
}

/* Navbar Styles */
.navbar {
    font-family: 'Poppins', sans-serif;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    animation: fadeIn 0.5s ease-out;
    padding: 0.8rem 1rem;
}

.navbar-text {
    font-family: 'Poppins', sans-serif;
    font-size: 1.1rem !important;
    font-weight: 500;
    color: #000000;
}

/* Logo Styles */
.taskdo-logo {
    height: 85px;
    transition: all 0.5s ease;
    animation: logoFloat 6s ease-in-out infinite;
    z-index: 1000;
    margin-top: 0;
}

.taskdo-logo:hover {
    transform: scale(1.1) rotate(5deg);
    filter: drop-shadow(0 0 15px rgba(102,204,255,0.6));
}

/* Profile Styles */
.profile-wrapper {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 20px;
    transition: all 0.3s ease;
}

.profile-wrapper:hover {
    background: rgba(102, 204, 255, 0.1);
}

.profile-img {
    width: 35px;
    height: 35px;
    object-fit: cover;
    border: 2px solid #66CCFF;
    animation: profilePulse 3s infinite;
    transition: transform 0.3s ease;
}

.profile-img:hover {
    transform: scale(1.1);
}

/* Time Display */
#currentTime {
    font-family: 'Poppins', sans-serif;
    font-size: 0.9rem;
    color: #000000;
    white-space: nowrap;
}

/* Main Container */
.main-container {
    animation: fadeIn 0.8s ease-out;
    padding: 2rem;
    padding-bottom: 80px;
}

/* Card Styles */
.todo-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    border: none;
    box-shadow: var(--card-shadow);
    animation: fadeIn 0.8s ease-out;
    overflow: hidden;
}

/* Form Elements */
.form-control {
    font-family: 'Inter', sans-serif;
    font-weight: 400;
    border-radius: 10px;
    padding: 12px;
    border: 2px solid #eef2f7;
    transition: all 0.3s ease;
    background: rgba(255,255,255,0.9);
}

.form-control:focus {
    box-shadow: 0 0 0 3px rgba(102,204,255,0.25);
    border-color: #66CCFF;
    transform: translateY(-2px);
}

/* Button Styles */
.btn {
    font-family: 'Poppins', sans-serif;
    font-weight: 500;
    letter-spacing: 0.5px;
}

.btn-primary {
    background: var(--primary-gradient);
    border: none;
    border-radius: 10px;
    padding: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102,204,255,0.4);
}

.btn-action {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Edit & Delete Buttons */
.btn-edit {
    background: linear-gradient(135deg, #FFB75E, #ED8F03);
    color: white;
}

.btn-edit:hover {
    background: linear-gradient(135deg, #ED8F03, #FFB75E);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(237, 143, 3, 0.3);
    color: white;
}

.btn-delete {
    background: linear-gradient(135deg, #FF6B6B, #FF4949);
    color: white;
}

.btn-delete:hover {
    background: linear-gradient(135deg, #FF4949, #FF6B6B);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(255, 73, 73, 0.3);
    color: white;
}

/* List Styles */
.list-group-item {
    font-family: 'Inter', sans-serif;
    border: 1px solid rgba(0,0,0,0.1);
    margin-bottom: 0.5rem;
    border-radius: 10px !important;
    transition: all 0.3s ease;
}

.list-group-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.list-title {
    transition: all 0.3s ease;
}

/* Footer Styles */
.footer {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(8px);
    padding: 15px 0;
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    overflow: hidden;
    z-index: 1000;
}

.waves {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 15px;
    margin-bottom: -7px;
    min-height: 15px;
}

.wave {
    position: absolute;
    top: -10px;
    left: 0;
    width: 200%;
    height: 100%;
    background: linear-gradient(90deg, #0099CC, #66CCFF);
    opacity: 0.3;
}

#wave1 {
    z-index: 1;
    opacity: 0.5;
    animation: moveWave1 3s linear infinite;
}

#wave2 {
    z-index: 0;
    opacity: 0.3;
    animation: moveWave2 5s linear infinite;
}

.copyright {
    margin: 0;
    color: #333;
    font-size: 0.9rem;
    position: relative;
    z-index: 2;
    font-family: 'Poppins', sans-serif;
}

/* Responsive Styles */
@media (max-width: 1200px) {
    .container {
        max-width: 95%;
    }
}

@media (max-width: 992px) {
    .taskdo-logo {
        height: 60px;
    }

    .navbar-text {
        font-size: 1rem !important;
    }

    #currentTime {
        font-size: 1rem;
    }

    .main-container {
        padding: 1.5rem;
    }
}

@media (max-width: 768px) {
    .navbar {
        padding: 0.4rem 0.8rem;
    }

    .navbar .container-fluid {
        flex-direction: column;
        align-items: center;
        padding: 0.5rem;
    }

    .profile-img {
        width: 30px;
        height: 30px;
    }

    .taskdo-logo {
        height: 55px;
    }

    #currentTime {
        font-size: 0.8rem;
    }

    .input-group {
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        gap: 0.5rem;
    }

    .btn-action {
        padding: 0.4rem 0.8rem;
        font-size: 0.75rem;
    }

    .footer {
        padding: 12px 0;
    }
    
    .copyright {
        font-size: 0.8rem;
    }
    
    .waves {
        height: 10px;
    }
}

@media (max-width: 576px) {
    .navbar {
        padding: 0.3rem 0.5rem;
    }

    .profile-img {
        width: 28px;
        height: 28px;
    }

    .taskdo-logo {
        height: 30px;
    }

    .main-container {
        padding-bottom: 60px;
    }
    
    .footer {
        padding: 10px 0;
    }
    
    .copyright {
        font-size: 0.75rem;
    }

    .btn-primary span {
        display: none;
    }

    .btn-primary i {
        margin: 0;
    }
}

/* iOS Fix */
@supports (-webkit-touch-callout: none) {
    .input-group .form-control {
        min-width: 0;
        width: 100% !important;
    }
}

/* Checklist styling */
.form-check-input {
    cursor: pointer;
    border-radius: 4px;
    transition: all 0.3s ease;
    width: 20px;
    height: 20px;
}

.form-check-input:checked {
    background-color: #66CCFF;
    border-color: #66CCFF;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M6 10l3 3l6-6'/%3e%3c/svg%3e");
}

.form-check-input:focus {
    border-color: #66CCFF;
    box-shadow: 0 0 0 0.25rem rgba(102, 204, 255, 0.25);
}

.form-check-input:hover {
    border-color: #66CCFF;
}

</style>
</head>
<body>
    <!-- Decorative Background -->
    <div class="decorative-bg">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
    </div>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between w-100">
            <!-- Profile Section -->
            <div class="d-flex align-items-center">
                <div class="profile-wrapper" style="cursor: pointer;" id="userProfile">
                    <img src="<?= htmlspecialchars($photo) ?>?<?= time() ?>" 
                         alt="Profile Picture" 
                         class="rounded-circle profile-img me-2">
                    <span class="navbar-text d-none d-md-inline">Welcome, <?= htmlspecialchars($username) ?></span>
                </div>
            </div>

            <!-- Logo Section -->
            <div class="logo-wrapper text-center">
                <img src="FOTO/taskdo.png" alt="TaskDo Logo" class="taskdo-logo">
            </div>

            <!-- Clock Section -->
            <div class="clock-wrapper">
                <span id="currentTime" class="navbar-text"></span>
            </div>
        </div>
    </div>
</nav>

    <div class="container main-container">
        <h1 class="text-center mb-4 text-white">MY TODO LIST</h1>

       <!-- Form to add new to-do list -->
<div class="todo-card mb-4">
    <div class="card-body">
        <form id="addListForm" class="mb-4">
            <div class="input-group">
                <input type="text" 
                       class="form-control" 
                       name="title" 
                       id="listTitle" 
                       placeholder="What needs to be done?" 
                       required
                       autocomplete="off">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-plus"></i>
                    <span class="btn-text">Add Task</span>
                </button>
            </div>
        </form>
    </div>
</div>


<!-- Search Box -->
<div class="todo-card mb-4">
    <div class="card-body">
        <div class="search-container">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-search search-icon"></i>
                </span>
                <input type="text" 
                       class="form-control" 
                       id="searchTask" 
                       placeholder="Search tasks..."
                       autocomplete="off">
                <button class="btn btn-outline-secondary" 
                        type="button" 
                        id="clearSearch" 
                        style="display: none;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Todo List Card -->
<div class="todo-card">
    <div class="card-body">
    <ul class="list-group" id="todoList">
    <?php while ($row = $result->fetch_assoc()): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center" id="list-<?= $row['id'] ?>">
            <div class="d-flex align-items-center">
                <input type="checkbox" 
                       class="form-check-input me-3" 
                       data-id="<?= $row['id'] ?>" 
                       <?= $row['completed'] ? 'checked' : '' ?>>
                <span class="list-title" 
                      id="title-<?= $row['id'] ?>" 
                      style="<?= $row['completed'] ? 'text-decoration: line-through;' : '' ?>">
                    <?= htmlspecialchars($row['title']) ?>
                </span>
            </div>
            <div>
                <button class="btn-action btn-edit me-2" 
                        data-id="<?= $row['id'] ?>" 
                        data-title="<?= htmlspecialchars($row['title']) ?>" 
                        data-bs-toggle="modal" 
                        data-bs-target="#editModal">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </button>
                <button class="btn-action btn-delete" 
                        data-id="<?= $row['id'] ?>" 
                        data-bs-toggle="modal" 
                        data-bs-target="#deleteModal">
                    <i class="fas fa-trash"></i>
                    <span>Delete</span>
                </button>
            </div>
        </li>
    <?php endwhile; ?>
</ul>
    </div>
</div>

    <!-- Modal for editing to-do list -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">
                    Edit Task: <span id="currentTaskTitle"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <div class="mb-3">
                        <label for="editTitle" class="form-label">Task Title</label>
                        <input type="text" class="form-control" id="editTitle" name="editTitle" required>
                    </div>
                    <input type="hidden" id="editListId" name="editListId">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

    <!-- Modal for deleting to-do list -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this TO DO LIST?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for profile -->
    <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profileModalLabel">User Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="<?= htmlspecialchars($photo) ?>" alt="Profile Picture" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px;">
                    <h1><?= htmlspecialchars($username) ?></h1>
                    <a href="edit_profile.php" class="btn btn-primary mt-3">Edit Profile</a>
                    <a href="logout.php" class="btn btn-danger mt-3">Logout</a>
                </div>
            </div>
        </div>
    </div>

<!-- Modal for password confirmation before editing profile -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordModalLabel">Confirm Your Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="passwordForm">
                    <div class="mb-3">
                        <label for="password" class="form-label">Enter Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </form>
                <p id="passwordError" class="text-danger mt-2" style="display: none;">Incorrect password. Please try again.</p>
            </div>
        </div>
    </div>
</div>

<!-- Footer Design dengan Waves -->
<footer class="footer">
    <div class="waves">
        <div class="wave" id="wave1"></div>
        <div class="wave" id="wave2"></div>
    </div>
    <div class="container text-center">
        <p class="copyright">
            TaskDo &copy; <?= date('Y') ?> | All Rights Reserved
        </p>
    </div>
</footer>

<script>
    // Handle when the "Edit Profile" button is clicked
    $('a[href="edit_profile.php"]').click(function(e) {
        e.preventDefault(); // Prevent direct link
        $('#passwordModal').modal('show'); // Show the password modal
    });

    // Handle password form submission
$('#passwordForm').submit(function(e) {
    e.preventDefault();

    const password = $('#password').val().trim();

    console.log('Sending password:', password); // Debug log

      // Periksa password saat ini
      $.get('check_current_password.php', function(currentPassword) {
        console.log('Current password in database:', currentPassword);
    });

    $.ajax({
        url: 'validate_password.php',
        method: 'POST',
        data: { password: password },
        success: function(response) {
            console.log('Raw Server response:', response); // Debug log
            try {
                const result = JSON.parse(response);

                if (result.success) {
                    window.location.href = 'edit_profile.php';
                } else {
                    $('#passwordError').text(result.message);
                    $('#passwordError').show();
                    
                    if (result.debug) {
                        console.log('Debug info:', result.debug);
                    }
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                alert("Error processing server response");
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax error:', error);
            alert("Error occurred during password validation");
        }
    });
});

// Tambahkan event untuk menyembunyikan pesan error saat input berubah
$('#password').on('input', function() {
    $('#passwordError').hide();
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let deleteListId;

    // Update real-time clock
    function updateClock() {
        const now = new Date();
        const options = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
        const timeString = now.toLocaleTimeString([], options);
        document.getElementById('currentTime').textContent = timeString;
    }

    // Call updateClock every second
    setInterval(updateClock, 1000);
    updateClock(); // Initial call

    // Handle profile modal display
    $('#userProfile').click(function() {
        $('#profileModal').modal('show'); // Tampilkan modal profil
    });

    // Handle profile picture upload
    $('#uploadProfilePicForm').submit(function(e) {
    e.preventDefault(); // Prevent normal form submission
    const formData = new FormData(this);

    $.ajax({
        url: 'upload_profile_pic.php',
        method: 'POST',
        data: formData,
        processData: false, // Important for file upload
        contentType: false, // Important for file upload
        success: function(response) {
            const result = JSON.parse(response);
            if (result.success) {
                // Update the profile picture in the modal and dashboard
               // Update the profile picture in the modal and dashboard
                $('img[alt="Profile Picture"]').attr('src', result.photo + '?' + new Date().getTime()); // Update in modal
                $('#userProfile img').attr('src', result.photo + '?' + new Date().getTime()); // Update in dashboard

                $('#uploadMessage').text(result.message).removeClass('text-danger').addClass('text-success');
            } else {
                $('#uploadMessage').text(result.message).removeClass('text-success').addClass('text-danger');
            }
        },
        error: function() {
            $('#uploadMessage').text("Error occurred while uploading the file.").removeClass('text-success').addClass('text-danger');
        }
    });
});

    // Handle edit list action
    $(document).on('click', '.editList', function() {
        const listId = $(this).data('id');
        const currentTitle = $(this).data('title');
        $('#editTitle').val(currentTitle);
        $('#editListId').val(listId);
    });

    // Handle confirm delete
    $(document).on('click', '.deleteList', function() {
        deleteListId = $(this).data('id'); // Simpan ID yang akan dihapus
    });

    // Handle confirm delete action
    $('#confirmDelete').click(function() {
        $.ajax({
            url: 'delete_list.php',
            method: 'POST',
            data: { list_id: deleteListId },
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    $('#list-' + deleteListId).remove();  // Remove the deleted list from the DOM
                    $('#deleteModal').modal('hide');  // Hide the modal
                } else {
                    alert("Failed to delete the list. Please try again.");
                }
            },
            error: function() {
                alert("Error occurred while deleting the list.");
            }
        });
    });

// Update bagian handle edit list action
$(document).on('click', '.btn-edit', function() {
    const listId = $(this).data('id');
    const currentTitle = $(this).data('title');
    $('#editTitle').val(currentTitle);
    $('#editListId').val(listId);
    $('#currentTaskTitle').text(currentTitle); // Menambahkan judul task yang sedang diedit
    
    // Tambahkan animasi fade in untuk modal content
    $('.modal-content').css('opacity', 0);
    $('#editModal').on('shown.bs.modal', function() {
        $('.modal-content').animate({opacity: 1}, 300);
    });
});

// Style tambahan untuk modal
const modalStyles = `
    .modal-content {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .modal-header {
        background: var(--primary-gradient);
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 1rem 1.5rem;
    }

    #currentTaskTitle {
        font-style: italic;
        font-weight: normal;
        opacity: 0.9;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .btn-close {
        background-color: white;
        opacity: 0.8;
    }

    .btn-close:hover {
        opacity: 1;
    }
`;

// Tambahkan style ke document
if (!document.getElementById('modalStyles')) {
    const styleSheet = document.createElement("style");
    styleSheet.id = 'modalStyles';
    styleSheet.textContent = modalStyles;
    document.head.appendChild(styleSheet);
}

// Handle delete button click with event delegation
$(document).on('click', '.btn-delete', function() {
    deleteListId = $(this).data('id');
    $('#deleteModal').modal('show');
});

// Handle confirm delete action
$('#confirmDelete').click(function() {
    if (!deleteListId) return;

    $.ajax({
        url: 'delete_list.php',
        method: 'POST',
        data: { list_id: deleteListId },
        success: function(response) {
            try {
                const result = JSON.parse(response);
                if (result.success) {
                    // Animate and remove the deleted item
                    $(`#list-${deleteListId}`).slideUp(300, function() {
                        $(this).remove();
                    });
                    $('#deleteModal').modal('hide');
                    showNotification('Task deleted successfully!', 'success');
                } else {
                    showNotification('Failed to delete the task.', 'danger');
                }
            } catch (e) {
                showNotification('Error processing the response.', 'danger');
            }
        },
        error: function() {
            showNotification('Error occurred while deleting the task.', 'danger');
        }
    });
});

// Fungsi untuk update ID todo list
function updateTodoListIds() {
    $.ajax({
        url: 'update_ids.php',
        method: 'POST',
        success: function(response) {
            try {
                const result = JSON.parse(response);
                if (result.success) {
                    // Update ID di tampilan
                    $('#todoList .list-group-item').each(function(index) {
                        const newId = index + 1;
                        const $item = $(this);
                        
                        // Update ID elemen
                        $item.attr('id', `list-${newId}`);
                        
                        // Update ID pada checkbox
                        $item.find('.form-check-input').attr('data-id', newId);
                        
                        // Update ID pada span title
                        $item.find('.list-title').attr('id', `title-${newId}`);
                        
                        // Update ID pada tombol edit
                        $item.find('.btn-edit')
                            .attr('data-id', newId);
                        
                        // Update ID pada tombol delete
                        $item.find('.btn-delete')
                            .attr('data-id', newId);
                    });
                }
            } catch (e) {
                console.error('Error updating IDs:', e);
            }
        },
        error: function() {
            console.error('Failed to update IDs');
        }
    });
}


// Handle edit form submission with real-time update
$('#editForm').submit(function(e) {
    e.preventDefault();
    const listId = $('#editListId').val();
    const newTitle = $('#editTitle').val().trim();

    if (!newTitle) {
        showNotification('Please enter a task title.', 'warning');
        return;
    }

    $.ajax({
        url: 'edit_list.php',
        method: 'POST',
        data: { list_id: listId, title: newTitle },
        success: function(response) {
            try {
                const result = JSON.parse(response);
                if (result.success) {
                    // Segera update tampilan tanpa refresh
                    const $listItem = $(`#list-${listId}`);
                    const $titleElement = $(`#title-${listId}`);
                    const $editButton = $listItem.find('.btn-edit');

                    // Animate the title update
                    $titleElement.fadeOut(200, function() {
                        $(this).text(newTitle).fadeIn(200);
                    });

                    // Update data-title pada tombol edit
                    $editButton.attr('data-title', newTitle);

                    // Update current task title di modal jika masih terbuka
                    $('#currentTaskTitle').text(newTitle);

                    // Tutup modal dengan animasi
                    $('#editModal').modal('hide');

                    // Tampilkan notifikasi sukses
                    showNotification('Task updated successfully!', 'success');
                } else {
                    showNotification('Failed to update task.', 'danger');
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                showNotification('Error processing the response.', 'danger');
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax error:', error);
            showNotification('Error occurred while updating the task.', 'danger');
        }
    });
});

// Tambahkan event listener untuk modal edit
$('#editModal').on('shown.bs.modal', function(e) {
    const button = $(e.relatedTarget);
    const currentTitle = button.data('title');
    
    // Set nilai input dan judul modal
    $('#editTitle').val(currentTitle);
    $('#currentTaskTitle').text(currentTitle);
    
    // Focus pada input field
    $('#editTitle').focus();
});

// Tambahkan animasi saat mengupdate task
function updateTaskWithAnimation(listId, newTitle) {
    const $listItem = $(`#list-${listId}`);
    
    // Tambahkan class untuk animasi
    $listItem.addClass('updating');
    
    // Update konten dengan animasi
    $listItem.find('.list-title').fadeOut(200, function() {
        $(this).text(newTitle).fadeIn(200);
        
        // Hapus class animasi
        setTimeout(() => {
            $listItem.removeClass('updating');
        }, 300);
    });
}

// CSS untuk animasi update
const updateAnimationStyles = `
    .list-group-item.updating {
        background-color: rgba(102, 204, 255, 0.1);
        transition: background-color 0.3s ease;
    }

    .modal.fade .modal-content {
        transform: scale(0.95);
        transition: all 0.3s ease;
    }

    .modal.show .modal-content {
        transform: scale(1);
    }

    .list-title {
        transition: all 0.3s ease;
    }
`;

// Tambahkan styles ke document
if (!document.getElementById('updateAnimationStyles')) {
    const styleSheet = document.createElement("style");
    styleSheet.id = 'updateAnimationStyles';
    styleSheet.textContent = updateAnimationStyles;
    document.head.appendChild(styleSheet);
}

// Update bagian Handle add list form submission
$('#addListForm').submit(function(e) {
    e.preventDefault();
    const title = $('#listTitle').val();
    
    if (!title.trim()) {
        showNotification('Please enter a task title.', 'warning');
        return;
    }

    $.ajax({
        url: 'add_list.php',
        method: 'POST',
        data: { title: title },
        success: function(response) {
            try {
                const newList = JSON.parse(response);
                if (newList.success) {
                    const newListItem = `
                        <li class="list-group-item d-flex justify-content-between align-items-center" id="list-${newList.id}">
                            <div class="d-flex align-items-center">
                                <input type="checkbox" class="form-check-input me-3" data-id="${newList.id}">
                                <span class="list-title" id="title-${newList.id}">${newList.title}</span>
                            </div>
                            <div>
                                <button class="btn-action btn-edit me-2" data-id="${newList.id}" 
                                    data-title="${newList.title}" data-bs-toggle="modal" data-bs-target="#editModal">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit</span>
                                </button>
                                <button class="btn-action btn-delete" data-id="${newList.id}" 
                                    data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="fas fa-trash"></i>
                                    <span>Delete</span>
                                </button>
                            </div>
                        </li>
                    `;

                    // Cari item terakhir yang di-checklist
                    const $checkedItems = $('#todoList .list-group-item').filter(function() {
                        return $(this).find('.form-check-input').is(':checked');
                    });

                    if ($checkedItems.length > 0) {
                        // Jika ada item yang di-checklist, tambahkan setelah item terakhir yang di-checklist
                        $checkedItems.last().after($(newListItem).hide().slideDown(300));
                    } else {
                        // Jika tidak ada yang di-checklist, tambahkan di awal list
                        $('#todoList').prepend($(newListItem).hide().slideDown(300));
                    }
                    
                    $('#listTitle').val('');
                    
                    // Update ID setelah menambah item baru
                    updateTodoListIds();
                    
                    // Reinitialize checkbox event handler
                    initializeCheckboxHandlers();

                    showNotification('Task added successfully!', 'success');
                } else {
                    showNotification('Failed to add task: ' + newList.message, 'danger');
                }
            } catch (e) {
                showNotification('Error processing the response.', 'danger');
            }
        },
        error: function() {
            showNotification('Error occurred while adding the task.', 'danger');
        }
    });
});

// Fungsi untuk menginisialisasi event handler checkbox
function initializeCheckboxHandlers() {
    $('.form-check-input').off('change').on('change', function() {
        const listId = $(this).data('id');
        const isChecked = $(this).is(':checked');
        const $titleElement = $(`#title-${listId}`);
        const $checkbox = $(this);
        const $listItem = $checkbox.closest('.list-group-item');

        // Update UI segera
        if (isChecked) {
            $titleElement.css('text-decoration', 'line-through');
            // Animate dan pindahkan ke atas
            $listItem.fadeOut(300, function() {
                $(this).prependTo('#todoList').fadeIn(300);
            });
        } else {
            $titleElement.css('text-decoration', 'none');
            // Animate dan pindahkan ke bawah
            $listItem.fadeOut(300, function() {
                const $uncheckedItems = $('#todoList .list-group-item').filter(function() {
                    return !$(this).find('.form-check-input').is(':checked');
                });
                
                if ($uncheckedItems.length > 0) {
                    $(this).insertBefore($uncheckedItems.first()).fadeIn(300);
                } else {
                    $(this).appendTo('#todoList').fadeIn(300);
                }
            });
        }

        // Kirim ke server
        $.ajax({
            url: 'update_status.php',
            method: 'POST',
            data: { 
                list_id: listId, 
                completed: isChecked ? 1 : 0 
            },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (!result.success) {
                        // Kembalikan status jika gagal
                        $checkbox.prop('checked', !isChecked);
                        $titleElement.css('text-decoration', isChecked ? 'none' : 'line-through');
                        showNotification('Failed to update status.', 'warning');
                    }
                } catch (e) {
                    // Handle error parsing
                    $checkbox.prop('checked', !isChecked);
                    $titleElement.css('text-decoration', isChecked ? 'none' : 'line-through');
                }
            },
            error: function() {
                // Handle network error
                $checkbox.prop('checked', !isChecked);
                $titleElement.css('text-decoration', isChecked ? 'none' : 'line-through');
                showNotification('Connection error. Please try again.', 'danger');
            }
        });
    });
}

// Panggil fungsi inisialisasi saat dokumen siap
$(document).ready(function() {
    initializeCheckboxHandlers();
});

// Utility function untuk menampilkan notifikasi
function showNotification(message, type) {
    // Check if notification container exists, if not create it
    if (!$('#notification-container').length) {
        $('body').append('<div id="notification-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>');
    }

    // Create notification element
    const notification = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert" 
             style="min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    // Add notification to container with animation
    const $notification = $(notification).appendTo('#notification-container');
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        $notification.fadeOut(300, function() {
            $(this).remove();
        });
    }, 3000);
}

// Handle checkbox status updates
// Handle checkbox status updates (ubah kode yang ada)
$(document).on('change', '.form-check-input', function() {
    const listId = $(this).data('id');
    const isChecked = $(this).is(':checked');
    const $titleElement = $(`#title-${listId}`);
    const $listItem = $(this).closest('.list-group-item');

    // Update UI
    if (isChecked) {
        $titleElement.css('text-decoration', 'line-through');
        $listItem.fadeOut(300, function() {
            $(this).prependTo('#todoList').fadeIn(300);
        });
    } else {
        $titleElement.css('text-decoration', 'none');
        $listItem.fadeOut(300, function() {
            $(this).appendTo('#todoList').fadeIn(300);
        });
    }

    // Kirim ke server
    $.ajax({
        url: 'update_status.php',
        method: 'POST',
        data: { list_id: listId, completed: isChecked ? 1 : 0 },
        success: function(response) {
            try {
                const result = JSON.parse(response);
                if (!result.success) {
                    // Kembalikan state jika gagal
                    $(this).prop('checked', !isChecked);
                    $titleElement.css('text-decoration', isChecked ? 'none' : 'line-through');
                    showNotification('Failed to update status', 'danger');
                }
            } catch (e) {
                console.error('Error:', e);
                showNotification('Error updating status', 'danger');
            }
        },
        error: function() {
            showNotification('Connection error', 'danger');
        }
    });
});

// Update bagian add new task (pada bagian success callback)
if (newList.success) {
    const newListItem = `
        <li class="list-group-item d-flex justify-content-between align-items-center" id="list-${newList.id}">
            <div class="d-flex align-items-center">
                <input type="checkbox" class="form-check-input me-3" data-id="${newList.id}">
                <span class="list-title" id="title-${newList.id}">${newList.title}</span>
            </div>
            <div>
                <button class="btn-action btn-edit me-2" data-id="${newList.id}" 
                    data-title="${newList.title}" data-bs-toggle="modal" data-bs-target="#editModal">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </button>
                <button class="btn-action btn-delete" data-id="${newList.id}" 
                    data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash"></i>
                    <span>Delete</span>
                </button>
            </div>
        </li>`;

    $(newListItem)
        .prependTo('#todoList')
        .hide()
        .slideDown(300);
    
    $('#listTitle').val('');
    showNotification('Task added successfully!', 'success');
}

// Tambahkan CSS untuk animasi yang lebih smooth
</script>

<!-- Add this JavaScript at the end of your existing script section -->
<script>
// Fungsi pencarian yang ditingkatkan dengan pemindahan posisi
// Fungsi pencarian yang sudah diperbaiki
function searchTasks() {
    const searchTerm = $('#searchTask').val().toLowerCase().trim();
    const $todoList = $('#todoList');
    const $items = $todoList.find('.list-group-item').detach(); // Lepaskan semua item
    const sortedItems = [];

    // Urutkan item berdasarkan hasil pencarian
    $items.each(function() {
        const $item = $(this);
        const taskTitle = $item.find('.list-title').text().toLowerCase();
        const isMatch = taskTitle.includes(searchTerm);
        
        // Reset highlight
        const $titleElement = $item.find('.list-title');
        const originalText = $titleElement.text();

        // Jika ada kata yang cocok
        if (isMatch) {
            // Tambahkan highlight jika ada search term
            if (searchTerm) {
                const regex = new RegExp(`(${escapeRegExp(searchTerm)})`, 'gi');
                const highlightedText = originalText.replace(regex, '<mark>$1</mark>');
                $titleElement.html(highlightedText);
            } else {
                $titleElement.html(originalText);
            }
            $item.show();
            // Tambahkan ke awal array untuk ditampilkan di atas
            sortedItems.unshift($item);
        } else {
            $titleElement.html(originalText);
            $item.hide();
            // Tambahkan ke akhir array
            sortedItems.push($item);
        }
    });

    // Terapkan urutan baru
    $todoList.append(sortedItems);

    // Tampilkan pesan jika tidak ada hasil
    updateNoResultsMessage(searchTerm, sortedItems);

    // Update tombol clear
    updateClearButton(searchTerm);
}

// Fungsi untuk escape karakter khusus dalam RegExp
function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

// Fungsi untuk update pesan tidak ada hasil
function updateNoResultsMessage(searchTerm, sortedItems) {
    const $noResults = $('#noResults');
    const hasVisibleItems = sortedItems.some($item => $item.is(':visible'));

    if (!hasVisibleItems && searchTerm) {
        if (!$noResults.length) {
            const noResultsHtml = `
                <div id="noResults" class="alert alert-info text-center my-3">
                    <i class="fas fa-search me-2"></i>
                    <span>No tasks found matching "${searchTerm}"</span>
                </div>`;
            $('#todoList').after(noResultsHtml);
        } else {
            $noResults.show();
        }
    } else {
        $noResults?.remove();
    }
}

// Fungsi untuk update tombol clear
function updateClearButton(searchTerm) {
    const $clearButton = $('#clearSearch');
    if (searchTerm) {
        $clearButton.fadeIn(300);
    } else {
        $clearButton.fadeOut(300);
    }
}

// Event listener untuk input pencarian dengan debounce
$('#searchTask').on('input', debounce(function() {
    searchTasks();
}, 300));

// Event listener untuk tombol clear
$(document).on('click', '#clearSearch', function() {
    $('#searchTask').val('');
    searchTasks();
    $(this).hide();
});

// Fungsi debounce untuk optimasi performa
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

// CSS untuk tampilan
const searchStyles = `
    mark {
        background: rgba(102, 204, 255, 0.3);
        padding: 0.2em;
        border-radius: 3px;
        transition: background-color 0.3s ease;
    }

    mark:hover {
        background: rgba(102, 204, 255, 0.5);
    }

    .list-group-item {
        transition: all 0.3s ease;
    }

    #searchTask {
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    #searchTask:focus {
        border-color: #66CCFF;
        box-shadow: 0 0 0 0.2rem rgba(102, 204, 255, 0.25);
    }

    .search-container {
        position: relative;
    }

    .search-icon {
        color: #66CCFF;
    }

    #clearSearch {
        transition: all 0.3s ease;
    }

    #clearSearch:hover {
        background-color: #66CCFF;
        color: white;
    }

    .alert-info {
        background: rgba(102, 204, 255, 0.1);
        border: 1px solid rgba(102, 204, 255, 0.2);
        color: #0099CC;
    }
`;

// Tambahkan style ke document
if (!document.getElementById('searchStyles')) {
    const styleSheet = document.createElement("style");
    styleSheet.id = 'searchStyles';
    styleSheet.textContent = searchStyles;
    document.head.appendChild(styleSheet);
}
</script>

</body>
</html>