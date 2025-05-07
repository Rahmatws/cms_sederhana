<?php
require_once '../config/database.php';
include 'header.php';

// Flash message
$flash = '';
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'viewer';

// Tambah post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_post']) && ($role === 'admin' || $role === 'editor' || $role === 'penulis')) {
    $title = trim($_POST['title']);
    $category_id = $_POST['category_id'];
    $content = trim($_POST['content']);
    $status = $_POST['status'];
    $user_id = $_SESSION['user_id'];
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid('img_').'.'.$ext;
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__.'/../uploads/'.$image);
    }
    $stmt = $pdo->prepare("INSERT INTO posts (title, content, category_id, user_id, status, image, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$title, $content, $category_id, $user_id, $status, $image]);
    $_SESSION['flash'] = '<div class="alert alert-success">Postingan berhasil ditambahkan!</div>';
    header('Location: posts.php');
    exit;
}

// Edit post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_post']) && ($role === 'admin' || $role === 'editor' || $role === 'penulis')) {
    $id = $_POST['post_id'];
    $title = trim($_POST['title']);
    $category_id = $_POST['category_id'];
    $content = trim($_POST['content']);
    $status = $_POST['status'];
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid('img_').'.'.$ext;
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__.'/../uploads/'.$image);
        $stmt = $pdo->prepare("UPDATE posts SET title=?, content=?, category_id=?, status=?, image=? WHERE id=?");
        $stmt->execute([$title, $content, $category_id, $status, $image, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE posts SET title=?, content=?, category_id=?, status=? WHERE id=?");
        $stmt->execute([$title, $content, $category_id, $status, $id]);
    }
    $_SESSION['flash'] = '<div class="alert alert-success">Postingan berhasil diupdate!</div>';
    header('Location: posts.php');
    exit;
}

// Hapus post
if (isset($_GET['delete']) && ($role === 'admin' || $role === 'editor' || $role === 'penulis')) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['flash'] = '<div class="alert alert-success">Postingan berhasil dihapus!</div>';
    header('Location: posts.php');
    exit;
}

// Ambil data kategori
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Auto-seed kategori jika kosong
if (count($categories) === 0) {
    $default_categories = ['Teknologi','Edukasi','Hiburan','Olahraga','Kesehatan','Bisnis','Sains','Travel','Kuliner','Gaya Hidup','Other'];
    foreach ($default_categories as $cat) {
        $pdo->prepare("INSERT INTO categories (name) VALUES (?)")->execute([$cat]);
    }
    $categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
}

// Filter
$where = [];
$params = [];
if (!empty($_GET['category'])) {
    $where[] = 'category_id = ?';
    $params[] = $_GET['category'];
}
if (!empty($_GET['status'])) {
    $where[] = 'status = ?';
    $params[] = $_GET['status'];
}
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Ambil data post
$sql = "SELECT posts.*, categories.name AS category_name, users.username AS author FROM posts LEFT JOIN categories ON posts.category_id = categories.id LEFT JOIN users ON posts.user_id = users.id $where_sql ORDER BY posts.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Status list
$status_list = ['Published', 'Draft', 'Archived'];
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <h1 class="mb-0">📄 Kelola Post</h1>
            <?php if ($role !== 'viewer'): ?>
            <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#addPostModal"><i class="fas fa-plus"></i> Tambah Postingan</a>
            <?php endif; ?>
        </div>
    </section>
    <section class="content pt-2">
        <div class="container-fluid">
            <?php if ($flash) echo $flash; ?>
            <!-- Filter & Search -->
            <form class="form-inline mb-3" method="get">
                <div class="form-group mr-2">
                    <select name="category" class="form-control">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php if (!empty($_GET['category']) && $_GET['category'] == $cat['id']) echo 'selected'; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mr-2">
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <?php foreach ($status_list as $status): ?>
                            <option value="<?php echo $status; ?>" <?php if (!empty($_GET['status']) && $_GET['status'] == $status) echo 'selected'; ?>><?php echo $status; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <input type="text" name="q" class="form-control mr-2" placeholder="Cari judul..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                <button type="submit" class="btn btn-secondary">Filter</button>
            </form>
            <!-- Tabel Post -->
            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Penulis</th>
                                <th>Kategori</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Gambar</th>
                                <?php if ($role !== 'viewer'): ?><th>Aksi</th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($posts)): ?>
                                <tr><td colspan="7" class="text-center">Belum ada postingan.</td></tr>
                            <?php else: ?>
                                <?php foreach ($posts as $post): ?>
                                    <tr>
                                        <td><a href="post_detail.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></td>
                                        <td><?php echo htmlspecialchars($post['author']); ?></td>
                                        <td><?php echo htmlspecialchars($post['category_name']); ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($post['created_at'])); ?></td>
                                        <td><span class="badge badge-<?php echo $post['status'] == 'Published' ? 'success' : ($post['status'] == 'Draft' ? 'warning' : 'secondary'); ?>"><?php echo $post['status']; ?></span></td>
                                        <td><?php if ($post['image']): ?><img src="../uploads/<?php echo $post['image']; ?>" alt="img" style="max-width:60px;max-height:40px;object-fit:cover;"> <?php endif; ?></td>
                                        <?php if ($role !== 'viewer'): ?>
                                        <td>
                                            <a href="#" class="btn btn-warning btn-sm editBtn" data-id="<?php echo $post['id']; ?>" data-title="<?php echo htmlspecialchars($post['title']); ?>" data-category="<?php echo $post['category_id']; ?>" data-content="<?php echo htmlspecialchars($post['content']); ?>" data-status="<?php echo $post['status']; ?>"><i class="fas fa-edit"></i> Edit</a>
                                            <a href="?delete=<?php echo $post['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus postingan ini?');"><i class="fas fa-trash"></i> Hapus</a>
                                        </td>
                                        <?php endif; ?>
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
<!-- Modal Tambah Post -->
<div class="modal fade" id="addPostModal" tabindex="-1" role="dialog" aria-labelledby="addPostModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="addPostModalLabel">Tambah Postingan Baru</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="add_post" value="1">
          <div class="form-group">
            <label>Judul</label>
            <input type="text" name="title" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Kategori</label>
            <select name="category_id" class="form-control" required>
              <option value="">Pilih Kategori</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Isi Konten</label>
            <textarea name="content" class="form-control" rows="4" required></textarea>
          </div>
          <div class="form-group">
            <label>Upload Gambar</label>
            <input type="file" name="image" class="form-control-file" accept="image/*">
          </div>
          <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control" required>
              <?php foreach ($status_list as $status): ?>
                <option value="<?php echo $status; ?>"><?php echo $status; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Modal Edit Post -->
<div class="modal fade" id="editPostModal" tabindex="-1" role="dialog" aria-labelledby="editPostModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="editPostModalLabel">Edit Postingan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="edit_post" value="1">
          <input type="hidden" name="post_id" id="editPostId">
          <div class="form-group">
            <label>Judul</label>
            <input type="text" name="title" id="editPostTitle" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Kategori</label>
            <select name="category_id" id="editPostCategory" class="form-control" required>
              <option value="">Pilih Kategori</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Isi Konten</label>
            <textarea name="content" id="editPostContent" class="form-control" rows="4" required></textarea>
          </div>
          <div class="form-group">
            <label>Upload Gambar (opsional, kosongkan jika tidak ingin ganti)</label>
            <input type="file" name="image" class="form-control-file" accept="image/*">
          </div>
          <div class="form-group">
            <label>Status</label>
            <select name="status" id="editPostStatus" class="form-control" required>
              <?php foreach ($status_list as $status): ?>
                <option value="<?php echo $status; ?>"><?php echo $status; ?></option>
              <?php endforeach; ?>
            </select>
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
// Modal edit post
$(function() {
    $('.editBtn').on('click', function() {
        $('#editPostId').val($(this).data('id'));
        $('#editPostTitle').val($(this).data('title'));
        $('#editPostCategory').val($(this).data('category'));
        $('#editPostContent').val($(this).data('content'));
        $('#editPostStatus').val($(this).data('status'));
        $('#editPostModal').modal('show');
    });
});
</script>
<?php include 'footer.php'; ?> 