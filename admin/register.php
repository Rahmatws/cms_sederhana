<?php
require_once '../config/database.php';

// Flash message
$flash = '';
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

$role_list = ['admin' => 'Admin', 'editor' => 'Editor', 'penulis' => 'Penulis', 'viewer' => 'Viewer'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $errors = [];
    if (strlen($username) < 3) {
        $errors[] = 'Username minimal 3 karakter.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    }
    if (!array_key_exists($role, $role_list)) {
        $errors[] = 'Role tidak valid.';
    }
    // Cek username unik
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = 'Username sudah digunakan.';
    }
    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $hashed, $username.'@mail.com', $role]);
        $_SESSION['flash'] = '<div class="alert alert-success">Akun berhasil dibuat! Silakan login.</div>';
        header('Location: login.php');
        exit;
    } else {
        $flash = '<div class="alert alert-danger">'.implode('<br>', $errors).'</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | MyCMS Rahmat</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <span style="font-size:2rem;font-weight:bold;">MyCMS <span style="color:#007bff">Rahmat</span></span>
    </div>
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Register akun baru</p>
            <?php if ($flash) echo $flash; ?>
            <form method="POST" autocomplete="off">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="username" placeholder="Username" required minlength="3">
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-user"></span></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="password" placeholder="Password" required minlength="6">
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <select name="role" class="form-control" required>
                        <option value="">-- Pilih Role --</option>
                        <?php foreach ($role_list as $key => $val): ?>
                            <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-users"></span></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Register</button>
                    </div>
                </div>
            </form>
            <p class="mb-1 text-center mt-3">
                <a href="login.php">Sudah punya akun? Login</a>
            </p>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html> 