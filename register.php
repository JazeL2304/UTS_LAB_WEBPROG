<?php
session_start();
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Get the plain text password

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
 
    $conn = getDatabaseConnection();
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $email_exists = false;
        $username_exists = false;

        $stmt2 = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt2->bind_param("s", $email);
        $stmt2->execute();
        $stmt2->store_result();
        if ($stmt2->num_rows > 0) {
            $email_exists = true;
        }

        $stmt3 = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt3->bind_param("s", $username);
        $stmt3->execute();
        $stmt3->store_result();
        
        if ($stmt3->num_rows > 0) {
            $username_exists = true;
        }

        if ($email_exists && $username_exists) {
            $error_message = "Username dan email ini sudah terdaftar. Silahkan gunakan username dan email lain.";
        } elseif ($email_exists) {
            $error_message = "Email ini sudah terdaftar. Silahkan gunakan email lain.";
        } elseif ($username_exists) {
            $error_message = "Username ini sudah terdaftar. Silahkan gunakan username lain.";
        }

    } else {
        // Use the hashed password when inserting into the database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password); // Bind hashed password
        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            $error_message = "Registrasi gagal. Silakan coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="register.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #0099CC, #66CCFF);
            --secondary-color: #ffffff;
            --text-color: #333333;
            --card-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        body {
            background: var(--primary-gradient);
            font-family: 'Helvetica', sans-serif;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            animation: fadeIn 0.5s ease-out;
            padding: 0.25rem 1rem; /* Lebih kecil dari sebelumnya */
        }

        .navbar-brand img {
            height: 80px;
            transition: transform 0.3s ease;
            animation: float 6s ease-in-out infinite;
        }

        .navbar-brand img:hover {
            transform: scale(1.1) rotate(5deg);
        }

        .register-container {
            min-height: calc(100vh - 100px);
            padding: 4rem 0;
            position: relative;
            margin-top: 1rem; /* Tambah margin top */
        }

        .register-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            border: none;
            box-shadow: var(--card-shadow);
            max-width: 430px;
            width: 90%;
            animation: fadeIn 0.8s ease-out;
            position: relative;
            overflow: hidden;
        }

        .register-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }

        /* Update padding content dalam card */
        .card-body {
             padding: 2rem 2.5rem !important; /* Sesuaikan padding dalam card */
        }

        @keyframes shine {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }

        .form-control {
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

        .input-group {
            position: relative;
        }

        .input-group-text {
            background: transparent;
            border: none;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            cursor: pointer;
            color: #666;
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102,204,255,0.4);
        }

        .btn-login {
            background: transparent;
            border: 2px solid #66CCFF;
            color: #333;
            border-radius: 20px;
            padding: 8px 20px;
            transition: all 0.3s ease;
            white-space: nowrap;
            display: inline-block;
        }

        .btn-login:hover {
            background: var(--primary-gradient);
            color: white;
            border-color: transparent;
        }

        .error-message {
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .decorative-bg {
            position: fixed;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
        }

        .circle-1 {
            width: 300px;
            height: 300px;
            top: -150px;
            right: -150px;
            animation: float 8s infinite;
        }

        .circle-2 {
            width: 200px;
            height: 200px;
            bottom: -100px;
            left: -100px;
            animation: float 6s infinite reverse;
        }

        .password-strength {
            height: 5px;
            border-radius: 2.5px;
            margin-top: 5px;
            transition: all 0.3s ease;
        }

 /* Update style untuk navbar */
.navbar {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    animation: fadeIn 0.5s ease-out;
    padding: 0.25rem 1rem; /* Sesuaikan padding */
}

/* Update style untuk logo */
.taskdo-logo {
    height: 100px; /* Sesuaikan ukuran logo */
    transition: transform 0.3s ease;
    animation: float 6s ease-in-out infinite;
    z-index: 1000;
    margin: 0 auto;
    display: block;
}

/* Update style untuk container navbar */
.container {
    padding: 0.5rem; /* Sesuaikan padding container */
}

/* Update style untuk tombol login */
.btn-login {
    background: transparent;
    border: 2px solid #66CCFF;
    color: #333;
    border-radius: 20px;
    padding: 8px 20px;
    transition: all 0.3s ease;
}

.btn-login:hover {
    background: var(--primary-gradient);
    color: white;
    border-color: transparent;
}


/* Style untuk responsive */
@media (max-width: 768px) {
    .navbar {
        min-height: 70px; /* Ukuran untuk mobile */
        padding: 0.5rem;
    }
    
    .taskdo-logo {
        max-height: 80px;
    }
    
    .navbar .row {
        align-items: center;
    }

    .col-3 {
        padding: 0;
    }

    .navbar .container-fluid {
        justify-content: space-between;
    }

    .navbar .col-4 {
        padding: 0;
        flex: none;
        width: auto;
    }

    .navbar .text-end {
        text-align: right !important;
    }

    .btn-login {
        padding: 6px 15px;
        font-size: 0.9rem;
    }

    .container {
        min-height: 70px;
    }
}

@media (max-width: 576px) {
    .taskdo-logo {
        height: 60px;
    }

    .navbar {
        padding: 0.25rem 0.5rem;
    }

    .btn-login {
        padding: 4px 12px;
        font-size: 0.85rem;
    }

    .navbar .container-fluid {
        padding-left: 5px;
        padding-right: 5px;
    }
}

/* Animasi dan hover effect tetap sama */
@keyframes float {
    0% {
        transform: translateY(0) rotate(0deg);
    }
    50% {
        transform: translateY(-5px) rotate(2deg);
    }
    100% {
        transform: translateY(0) rotate(0deg);
    }
}

.taskdo-logo:hover {
    transform: scale(1.1) rotate(5deg);
    filter: drop-shadow(0 0 10px rgba(102,204,255,0.5));
}

/* Update padding content dalam card */
.card-body {
    padding: 2rem 2.5rem !important; /* Sesuaikan padding dalam card */
}

/* Update style form control */
.form-control {
    border-radius: 8px; /* Kurangi border radius */
    padding: 10px; /* Kurangi padding */
    font-size: 0.95rem; /* Kurangi ukuran font */
}

/* Update style button */
.btn-primary {
    padding: 10px; /* Kurangi padding button */
    font-size: 0.95rem; /* Kurangi ukuran font button */
}

/* Update style heading */
.card-body h2 {
    font-size: 1.75rem; /* Kurangi ukuran heading */
    margin-bottom: 1.5rem;
}

/* Update style label */
.form-label {
    font-size: 0.9rem; /* Kurangi ukuran label */
}

/* Responsive styles */
@media (max-width: 768px) {
    .register-container {
        padding: 2rem 0;
    }
    
    .register-card {
        max-width: 90%;
    }
    
    .card-body {
        padding: 1.5rem !important;
    }
}

/* Footer Styles with Waves */
.footer {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(8px);
    padding: 15px 0;
    position: relative;
    width: 100%;
    overflow: hidden;
    margin-top: auto;
}

/* Pastikan body menggunakan flexbox untuk layout */
body {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.register-container {
    flex: 1;
    padding: 2rem 0;
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

.copyright {
    margin: 0;
    color: #333;
    font-size: 0.9rem;
    position: relative;
    z-index: 2;
    font-family: 'Poppins', sans-serif;
}

/* Responsive adjustments for footer */
@media (max-width: 768px) {
    .footer {
        padding: 12px 0;
    }
    
    .copyright {
        font-size: 0.8rem;
    }
    
    .waves {
        height: 10px;
    }
    
    .wave {
        top: -8px;
    }
}

@media (max-width: 576px) {
    .footer {
        padding: 10px 0;
    }
    
    .copyright {
        font-size: 0.75rem;
    }
    
    .register-container {
        padding-bottom: 2rem;
    }
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
        <div class="row w-100 align-items-center">
            <div class="col-3">
                <!-- Empty space on the left -->
            </div>
            <div class="col-6 text-center">
                <img src="FOTO/taskdo.png" alt="TaskDo Logo" class="taskdo-logo">
            </div>
            <div class="col-3 text-end">
                <ul class="navbar-nav justify-content-end">
                    <li class="nav-item">
                        <a class="nav-link btn btn-login" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

    <!-- Register Container -->
    <div class="container d-flex justify-content-center align-items-center register-container">
        <div class="card register-card">
            <div class="card-body p-4 p-md-5">
                <h2 class="text-center mb-4">Register</h2>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger error-message" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= $error_message ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="needs-validation" novalidate>
                    <div class="mb-4">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="username" name="username" required 
                                placeholder="Choose your username">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label">Email address</label>
                        <div class="input-group">
                            <input type="email" class="form-control" id="email" name="email" required 
                                placeholder="Enter your email">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required 
                                placeholder="Create a strong password">
                            <span class="input-group-text" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                        <div class="password-strength" id="passwordStrength"></div>
                        <small class="text-muted" id="passwordFeedback"></small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-user-plus me-2"></i>Register
                    </button>
                </form>

                <p class="text-center mt-4 mb-0">
                    Already have an account? 
                    <a href="login.php" class="text-decoration-none">Login here</a>
                </p>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Password Visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Password Strength Checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);
            updatePasswordStrengthUI(strength);
        });

        function checkPasswordStrength(password) {
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            return strength;
        }

        function updatePasswordStrengthUI(strength) {
            const strengthBar = document.getElementById('passwordStrength');
            const feedback = document.getElementById('passwordFeedback');
            
            const colors = ['#ff4444', '#ffbb33', '#00C851', '#33b5e5', '#2BBBAD'];
            const messages = [
                'Very weak',
                'Weak',
                'Fair',
                'Good',
                'Strong'
            ];

            strengthBar.style.width = `${(strength / 5) * 100}%`;
            strengthBar.style.backgroundColor = colors[strength - 1];
            feedback.textContent = messages[strength - 1];
            feedback.style.color = colors[strength - 1];
        }

        // Form Validation
        (function () {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()

        // Input Animation
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });
    </script>
</body>
</html>