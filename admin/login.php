<?php
session_start();
require_once '../config/database.php';

// Flash message
$flash = '';
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

// Remember Me: check cookie
if (isset($_COOKIE['rememberme']) && !isset($_SESSION['user_id'])) {
    $user_id = $_COOKIE['rememberme'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['flash'] = 'Login berhasil!';
        header('Location: dashboard.php');
        exit();
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $remember = isset($_POST['remember']);

    if (empty($username) || empty($password) || empty($role)) {
        $error = "Semua field harus diisi.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if (!$user) {
            $error = "Username tidak ditemukan.";
        } elseif ($user['role'] !== $role) {
            $error = "Role tidak sesuai dengan akun.";
        } elseif (!password_verify($password, $user['password'])) {
            $error = "Password salah.";
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            // Log login
            $stmtLog = $pdo->prepare("INSERT INTO logins (user_id) VALUES (?)");
            $stmtLog->execute([$user['id']]);
            if ($remember) {
                setcookie('rememberme', $user['id'], time() + (86400 * 30), "/"); // 30 hari
            } else {
                setcookie('rememberme', '', time() - 3600, "/");
            }
            $_SESSION['flash'] = 'Login berhasil!';
            header('Location: dashboard.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MyCMS Rahmat | Login</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>
        body.login-page {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            min-height: 100vh;
        }
        .login-box {
            margin-top: 5vh;
        }
        .login-logo img, .login-logo .icon-circle {
            max-width: 90px;
            margin-bottom: 10px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .icon-circle {
            background: #fff;
            border-radius: 50%;
            width: 90px;
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.8rem;
            color: #2575fc;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
        }
        .card {
            border-radius: 18px;
            box-shadow: 0 4px 32px rgba(0,0,0,0.10);
        }
        .login-card-body {
            padding: 2.2rem 2rem 1.5rem 2rem;
        }
        .btn-primary, .btn-outline-secondary {
            transition: 0.2s;
        }
        .btn-primary:hover, .btn-outline-secondary:hover {
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 2px 12px rgba(37,117,252,0.15);
        }
        .dark-mode body.login-page {
            background: linear-gradient(135deg, #232526 0%, #414345 100%);
        }
        .dark-mode .card {
            background: #232526 !important;
            color: #fff;
        }
        .dark-mode .login-card-body {
            background: #232526 !important;
            color: #fff;
        }
        .dark-mode .form-control, .dark-mode .input-group-text {
            background: #333 !important;
            color: #fff !important;
        }
        .dark-mode .btn-primary { background: #444; border-color: #444; }
        .dark-mode .btn-outline-secondary { background: #232526; color: #fff; border-color: #fff; }
        .dark-mode .btn-outline-secondary:hover { background: #444; color: #fff; }
        @media (max-width: 576px) {
            .login-box { width: 95vw; margin: 1rem auto; }
            .login-card-body { padding: 1.2rem 0.7rem 1rem 0.7rem; }
        }
    </style>
</head>
<body class="hold-transition login-page">
<button class="btn btn-sm btn-secondary dark-toggle" id="toggleDark"><i class="fas fa-moon"></i> <span id="darkLabel">Dark</span></button>
<div class="login-box">
    <div class="login-logo">
        <!-- Logo bisa diganti dengan <img src="logo.png" alt="Logo"> -->
        <span class="icon-circle mb-2"><i class="fas fa-user-shield"></i></span>
        <span style="font-size:2rem;font-weight:bold;letter-spacing:1px;">MyCMS <span style="color:#007bff">Rahmat</span></span>
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Sign in to start your session</p>

            <?php if (!empty($flash)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $flash; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <form method="POST" id="loginForm" autocomplete="off">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="username" id="username" placeholder="Username" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <select name="role" id="role" class="form-control" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="admin">Admin</option>
                        <option value="editor">Editor</option>
                        <option value="penulis">Penulis</option>
                        <option value="viewer">Viewer</option>
                    </select>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-users"></span>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Remember Me</label>
                        </div>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block" id="loginBtn">
                            <span id="loginText">Sign In</span>
                            <span id="loginSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </form>
            <div class="text-center mt-2 mb-2">
                <a href="../index.php" class="btn btn-outline-secondary btn-block"><i class="fas fa-sign-out-alt"></i> Keluar</a>
            </div>
            <p class="mb-1 text-center">
                <a href="#" onclick="alert('Fitur lupa password belum tersedia.'); return false;">Lupa Password?</a>
            </p>
            <p class="mb-1 text-center mt-3">
                <a href="register.php">Belum punya akun? Register</a>
            </p>
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script>
// Client-side validation & loading spinner
$(function() {
    $('#loginForm').on('submit', function(e) {
        var username = $('#username').val().trim();
        var password = $('#password').val();
        var role = $('#role').val();
        if (username === '' || password === '' || role === '') {
            alert('Username, password, dan role harus diisi!');
            e.preventDefault();
            return false;
        }
        $('#loginBtn').attr('disabled', true);
        $('#loginText').text('Loading...');
        $('#loginSpinner').removeClass('d-none');
    });

    // Dark mode toggle
    if (localStorage.getItem('darkmode') === '1') {
        $('body').addClass('dark-mode');
        $('#darkLabel').text('Light');
        $('#toggleDark i').removeClass('fa-moon').addClass('fa-sun');
    }
    $('#toggleDark').on('click', function() {
        $('body').toggleClass('dark-mode');
        var isDark = $('body').hasClass('dark-mode');
        $('#darkLabel').text(isDark ? 'Light' : 'Dark');
        $('#toggleDark i').toggleClass('fa-moon fa-sun');
        localStorage.setItem('darkmode', isDark ? '1' : '0');
    });
});
</script>
</body>
</html> 