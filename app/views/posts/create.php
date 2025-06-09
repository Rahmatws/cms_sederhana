<div class="container mt-4">
    <h2>Tambah Postingan</h2>
    <form method="post" action="/admin/posts/create">
        <div class="form-group">
            <label>Judul</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Konten</label>
            <textarea name="content" class="form-control" rows="6" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="/admin/posts" class="btn btn-secondary">Batal</a>
    </form>
</div> 