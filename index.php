<?php
require_once 'config/database.php';

// Get all posts
$stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Sederhana</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">CMS Sederhana</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin/login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <?php if (empty($posts)): ?>
                    <p>Belum ada artikel yang diposting.</p>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <article class="card mb-4">
                            <div class="card-body">
                                <h2 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h2>
                                <p class="card-text"><?php echo htmlspecialchars(substr($post['content'], 0, 200)) . '...'; ?></p>
                                <a href="post.php?id=<?php echo $post['id']; ?>" class="btn btn-primary">Baca Selengkapnya</a>
                            </div>
                            <div class="card-footer text-muted">
                                Diposting pada: <?php echo date('d F Y', strtotime($post['created_at'])); ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        Sidebar
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Menu</h5>
                        <ul class="list-unstyled">
                            <li><a href="index.php">Beranda</a></li>
                            <li><a href="about.php">Tentang Kami</a></li>
                            <li><a href="contact.php">Kontak</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white mt-5">
        <div class="container py-3">
            <p class="text-center mb-0">&copy; <?php echo date('Y'); ?> CMS Sederhana. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 