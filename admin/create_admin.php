<?php
require_once '../config/database.php';

// Data admin
$username = 'admin';
$password = 'admin123'; // Password yang akan digunakan
$email = 'admin@example.com';

// Generate password hash
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // Cek apakah user admin sudah ada
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->rowCount() > 0) {
        // Update password jika user sudah ada
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->execute([$hashed_password, $username]);
        echo "Password admin berhasil diupdate!<br>";
    } else {
        // Buat user baru jika belum ada
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        $stmt->execute([$username, $hashed_password, $email]);
        echo "User admin berhasil dibuat!<br>";
    }
    
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
    echo "<a href='login.php'>Klik di sini untuk login</a>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 