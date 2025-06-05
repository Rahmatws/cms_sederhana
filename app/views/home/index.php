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
                        <a href="/post/<?php echo $post['id']; ?>" class="btn btn-primary">Baca Selengkapnya</a>
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
                    <li><a href="/">Beranda</a></li>
                    <li><a href="/about">Tentang Kami</a></li>
                    <li><a href="/contact">Kontak</a></li>
                </ul>
            </div>
        </div>
    </div>
</div> 