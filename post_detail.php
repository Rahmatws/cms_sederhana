<?php
require_once 'config/database.php';
if (!isset($_GET['id'])) {
    echo '<div class="alert alert-danger">ID artikel tidak ditemukan.</div>';
    exit;
}
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT posts.*, categories.name AS category_name, users.username AS author FROM posts LEFT JOIN categories ON posts.category_id = categories.id LEFT JOIN users ON posts.user_id = users.id WHERE posts.id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$post) {
    echo '<div class="alert alert-danger">Artikel tidak ditemukan.</div>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($post['title']); ?> | MyCMS Rahmat</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>
        .post-img { max-width: 100%; max-height: 350px; object-fit: cover; border-radius: 8px; margin-bottom: 1rem; }
        .post-meta { color: #888; font-size: 0.95rem; margin-bottom: 1rem; }
        .post-content { font-size: 1.1rem; line-height: 1.7; }
    </style>
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
        <div class="container">
            <a href="index.php" class="navbar-brand"><b>MyCMS</b> Rahmat</a>
        </div>
    </nav>
    <div class="content-wrapper" style="margin-left:0;">
        <div class="content pt-4">
            <div class="container">
                <div class="card mx-auto" style="max-width:700px;">
                    <div class="card-body">
                        <h2 class="mb-2"><?php echo htmlspecialchars($post['title']); ?></h2>
                        <div class="post-meta mb-2">
                            <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($post['author']); ?></span> |
                            <span><i class="fas fa-calendar"></i> <?php echo date('d M Y', strtotime($post['created_at'])); ?></span> |
                            <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($post['category_name']); ?></span>
                        </div>
                        <?php if ($post['image']): ?>
                            <img src="uploads/<?php echo $post['image']; ?>" class="post-img" alt="Gambar Artikel">
                        <?php endif; ?>
                        <div class="post-content mt-3"><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html> 