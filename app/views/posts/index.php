<div class="container mt-4">
    <h2>Daftar Postingan</h2>
    <a href="/admin/posts/create" class="btn btn-success mb-3">Tambah Postingan</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
            <tr>
                <td><?php echo htmlspecialchars($post['title']); ?></td>
                <td><?php echo date('d F Y', strtotime($post['created_at'])); ?></td>
                <td>
                    <a href="/admin/posts/edit/<?php echo $post['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="/admin/posts/delete/<?php echo $post['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div> 