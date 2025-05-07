<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'viewer';
// Redirect viewer if try to access admin
if ($role === 'viewer' && basename($_SERVER['PHP_SELF']) !== 'dashboard.php') {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CMS Sederhana | Admin</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>
        .dark-toggle { position: absolute; top: 10px; right: 20px; z-index: 10; }
        .logout-fixed {
            position: fixed;
            left: 20px;
            bottom: 20px;
            z-index: 9999;
        }
        .logout-fixed .btn-logout {
            background: #dc3545;
            color: #fff;
            font-weight: bold;
            border-radius: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            padding: 12px 24px;
            font-size: 1.1rem;
        }
        .logout-fixed .btn-logout:hover {
            background: #b52a37;
            color: #fff;
        }
        .dark-mode .main-header, .dark-mode .main-sidebar, .dark-mode .content-wrapper, .dark-mode .main-footer, .dark-mode .card, .dark-mode .info-box, .dark-mode .table, .dark-mode .modal-content {
            background: #222 !important; color: #fff !important;
        }
        .dark-mode .form-control, .dark-mode .input-group-text { background: #333 !important; color: #fff !important; }
        .dark-mode .btn-primary { background: #444 !important; border-color: #444 !important; }
        .dark-mode .sidebar-dark-primary { background: #181818 !important; }
        .dark-mode .nav-sidebar .nav-link.active { background: #333 !important; color: #fff !important; }
        .dark-mode .nav-sidebar .nav-link { color: #bbb !important; }
        .dark-mode .alert { background: #333; color: #fff; border-color: #444; }
        .dark-mode .icheck-primary label { color: #fff; }
        .dark-mode .btn-logout { background: #ff5555 !important; color: #fff !important; }
        .dark-mode .btn-logout:hover { background: #b52a37 !important; }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<script>
// Apply dark mode on page load if set in localStorage
if (localStorage.getItem('darkmode') === '1') {
    document.body.classList.add('dark-mode');
}
</script>
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light position-relative">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        <!-- Dark mode toggle -->
        <button class="btn btn-sm btn-secondary dark-toggle" id="toggleDark"><i class="fas fa-moon"></i> <span id="darkLabel">Dark</span></button>
        <!-- Right navbar links -->
        <!-- (Logout button removed from here) -->
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="dashboard.php" class="brand-link">
            <span class="brand-text font-weight-light">CMS Sederhana</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <?php if ($role === 'admin' || $role === 'editor' || $role === 'penulis'): ?>
                    <li class="nav-item">
                        <a href="posts.php" class="nav-link">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Kelola Post</p>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if ($role === 'admin' || $role === 'editor'): ?>
                    <li class="nav-item">
                        <a href="categories.php" class="nav-link">
                            <i class="nav-icon fas fa-tags"></i>
                            <p>Kelola Kategori</p>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if ($role === 'admin'): ?>
                    <li class="nav-item">
                        <a href="users.php" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Kelola User</p>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
    <!-- Logout Button Fixed -->
    <div class="logout-fixed">
        <button class="btn btn-logout" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Logout</button>
    </div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Dark mode toggle logic
$(function() {
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
    // Logout confirmation
    $('#logoutBtn').on('click', function(e) {
        e.preventDefault();
        if (confirm('Apakah Anda yakin ingin logout?')) {
            window.location.href = 'logout.php';
        }
    });
});
</script> 