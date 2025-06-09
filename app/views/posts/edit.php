<div class="container mt-4">
    <h2>Edit Postingan</h2>
    <form method="post" action="/admin/posts/edit/<?php echo $post['id']; ?>">
        <div class="form-group">
            <label>Judul</label>
            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($post['title']); ?>" required>
        </div>
        <div class="form-group">
            <label>Konten</label>
            <textarea name="content" class="form-control" rows="6" required><?php echo htmlspecialchars($post['content']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="/admin/posts" class="btn btn-secondary">Batal</a>
    </form>
</div> 