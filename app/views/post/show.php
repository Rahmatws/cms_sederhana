<?php if ($post): ?>
    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
    <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
    <p>Diposting pada: <?php echo date('d F Y', strtotime($post['created_at'])); ?></p>
<?php else: ?>
    <p>Postingan tidak ditemukan.</p>
<?php endif; ?> 