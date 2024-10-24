<?php
session_start();
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $conn = getDatabaseConnection();
    
    // Fetch user data including the hashed password
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Use password_verify to check the entered password against the hashed password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error_message = "Email atau password salah";
        }
    } else {
        $error_message = "Email tidak ditemukan";
    }

    $stmt->close();
    $conn->close();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="login.css">
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
            padding: 0.25rem 1rem; /* Kurangi padding untuk navbar */
        }
        
        .navbar-brand img {
            height: 80px;
            transition: transform 0.3s ease;
            animation: float 6s ease-in-out infinite;
        }

        .navbar-brand img:hover {
            transform: scale(1.1) rotate(5deg);
        }

        .login-container {
            min-height: calc(100vh - 100px);
            padding: 2rem 0;
            position: relative;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            border: none;
            box-shadow: var(--card-shadow);
            max-width: 450px;
            width: 90%;
            animation: fadeIn 0.8s ease-out;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
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

        .btn-register {
            background: transparent;
            border: 2px solid #66CCFF;
            color: #333;
            border-radius: 20px;
            padding: 8px 20px;
            transition: all 0.3s ease;
            white-space: nowrap;
            display: inline-block;
        }

        .btn-register:hover {
            background: var(--primary-gradient);
            color: white;
            border-color: transparent;/* Change border color on hover */
            box-shadow: 0 4px 10px rgba(0, 153, 204, 0.5); /* Add shadow effect */
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
            left: -150px;
            animation: float 8s infinite;
        }

        .circle-2 {
            width: 200px;
            height: 200px;
            bottom: -100px;
            right: -100px;
            animation: float 6s infinite reverse;
        }

        .taskdo-logo {
            height: 100px;
            transition: transform 0.3s ease;
            animation: float 6s ease-in-out infinite;
            z-index: 1000;
            margin: 0 auto;
            display: block;
        }

        /* Update responsive styles */
@media (max-width: 768px) {
    .navbar {
        padding: 0.5rem;
    }
    
    .taskdo-logo {
        height: 80px;
    }
    
    .navbar .row {
        align-items: center;
    }
    
    .col-3 {
        padding: 0;
    }

    .btn-register {
        padding: 6px 15px;
        font-size: 0.9rem;
    }
}

@media (max-width: 576px) {
    .taskdo-logo {
        height: 60px;
    }
    
    .navbar {
        padding: 0.25rem 0.5rem;
    }
    
    .btn-register {
        padding: 4px 12px;
        font-size: 0.85rem;
    }

    .navbar .container-fluid {
        padding-left: 5px;
        padding-right: 5px;
    }
}

/* Animation untuk logo */
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

/* Pastikan tombol register selalu terlihat */
.navbar-nav {
    margin: 0;
    padding: 0;
}

.nav-item {
    margin: 0;
}

/* Tambahan untuk responsivitas yang lebih baik */
@media (max-width: 992px) {
    .navbar .row > div {
        padding: 0.5rem;
    }
    
    .navbar-nav {
        flex-direction: row;
    }
}

/* Fix untuk container pada mobile */
@media (max-width: 576px) {
    .container-fluid {
        padding: 0 0.5rem;
    }
    
    .row {
        margin: 0;
    }
}

.footer {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(8px);
    padding: 15px 0;
    position: fixed;
    bottom: 0;
    width: 100%;
    overflow: hidden;
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
    top: 0;
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
}
</style>
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
                        <a class="nav-link btn btn-register" href="register.php">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>



    <!-- Login Container -->
    <div class="container d-flex justify-content-center align-items-center login-container">
        <div class="card login-card">
            <div class="card-body p-4 p-md-5">
                <h2 class="text-center mb-4">Welcome Back!</h2>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger error-message" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= $error_message ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="needs-validation" novalidate>
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
                                placeholder="Enter your password">
                            <span class="input-group-text" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>
                </form>

                <p class="text-center mt-4 mb-0">
                    Don't have an account? 
                    <a href="register.php" class="text-decoration-none">Register here</a>
                </p>
            </div>
        </div>
        <!-- Footer Design 2 -->
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
    </div>

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