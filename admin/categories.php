<?php
require_once '../config/database.php';
include 'header.php';

// Flash message
$flash = '';
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

// Tambah kategori
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['category_name']);
    // Cek duplikat
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE name = ?");
    $stmt->execute([$name]);
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['flash'] = '<div class="alert alert-danger">Nama kategori sudah ada!</div>';
    } else {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$name]);
        $_SESSION['flash'] = '<div class="alert alert-success">Kategori berhasil ditambahkan!</div>';
    }
    header('Location: categories.php');
    exit;
}

// Edit kategori
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_category'])) {
    $id = $_POST['category_id'];
    $name = trim($_POST['category_name']);
    // Cek duplikat (kecuali dirinya sendiri)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE name = ? AND id != ?");
    $stmt->execute([$name, $id]);
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['flash'] = '<div class="alert alert-danger">Nama kategori sudah ada!</div>';
    } else {
        $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->execute([$name, $id]);
        $_SESSION['flash'] = '<div class="alert alert-success">Kategori berhasil diupdate!</div>';
    }
    header('Location: categories.php');
    exit;
}

// Hapus kategori
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['flash'] = '<div class="alert alert-success">Kategori berhasil dihapus!</div>';
    header('Location: categories.php');
    exit;
}

// Ambil data kategori dan jumlah post
$sql = "SELECT c.*, (SELECT COUNT(*) FROM posts p WHERE p.category_id = c.id) as post_count FROM categories c ORDER BY c.name";
$categories = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <h1 class="mb-0">🏷️ Kelola Kategori</h1>
            <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#addCategoryModal"><i class="fas fa-plus"></i> Tambah Kategori</a>
        </div>
    </section>
    <section class="content pt-2">
        <div class="container-fluid">
            <?php if ($flash) echo $flash; ?>
            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Kategori</th>
                                <th>Jumlah Postingan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($categories)): ?>
                                <tr><td colspan="3" class="text-center">Belum ada kategori.</td></tr>
                            <?php else: ?>
                                <?php foreach ($categories as $cat): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($cat['name']); ?></td>
                                        <td><?php echo $cat['post_count']; ?></td>
                                        <td>
                                            <a href="#" class="btn btn-warning btn-sm editBtn" data-id="<?php echo $cat['id']; ?>" data-name="<?php echo htmlspecialchars($cat['name']); ?>"><i class="fas fa-edit"></i> Edit</a>
                                            <a href="?delete=<?php echo $cat['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus kategori ini?');"><i class="fas fa-trash"></i> Hapus</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Modal Tambah Kategori -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="addCategoryModalLabel">Tambah Kategori Baru</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="add_category" value="1">
            <div class="form-group">
              <label>Nama Kategori</label>
              <input type="text" name="category_name" class="form-control" required>
              <small class="form-text text-muted">Nama kategori harus unik.</small>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Modal Edit Kategori -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="editCategoryModalLabel">Edit Kategori</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="edit_category" value="1">
            <input type="hidden" name="category_id" id="editCategoryId">
            <div class="form-group">
              <label>Nama Kategori</label>
              <input type="text" name="category_name" id="editCategoryName" class="form-control" required>
              <small class="form-text text-muted">Nama kategori harus unik.</small>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
// Modal edit kategori
$(function() {
    $('.editBtn').on('click', function() {
        $('#editCategoryId').val($(this).data('id'));
        $('#editCategoryName').val($(this).data('name'));
        $('#editCategoryModal').modal('show');
    });
});
</script>
<?php include 'footer.php'; ?> 