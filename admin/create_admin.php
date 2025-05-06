<?php
require_once '../config/database.php';

// Data admin
$username = 'admin';
$password = 'admin123'; // Password yang akan digunakan
$email = 'admin@example.com';
$role = 'admin';

// Generate password hash
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // Cek apakah user admin sudah ada
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->rowCount() > 0) {
        // Update password dan role jika user sudah ada
        $stmt = $pdo->prepare("UPDATE users SET password = ?, role = ? WHERE username = ?");
        $stmt->execute([$hashed_password, $role, $username]);
        echo "Password dan role admin berhasil diupdate!<br>";
    } else {
        // Buat user baru jika belum ada
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $hashed_password, $email, $role]);
        echo "User admin berhasil dibuat!<br>";
    }
    
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
    echo "Role: admin<br>";
    echo "<a href='login.php'>Klik di sini untuk login</a>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 