<?php
require_once '../config/database.php';
include 'header.php';

// Ambil data user
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$role_list = ['admin' => 'Admin', 'editor' => 'Editor', 'penulis' => 'Penulis', 'viewer' => 'Viewer'];
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <h1 class="mb-0">👥 Kelola User</h1>
            <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#addUserModal"><i class="fas fa-plus"></i> Tambah User</a>
        </div>
    </section>
    <section class="content pt-2">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr><td colspan="4" class="text-center">Belum ada user.</td></tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><span class="badge badge-<?php echo $user['role'] == 'admin' ? 'danger' : ($user['role'] == 'editor' ? 'success' : ($user['role'] == 'penulis' ? 'warning' : 'secondary')); ?>"><?php echo $role_list[$user['role']]; ?></span></td>
                                        <td>
                                            <a href="#" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                            <a href="#" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</a>
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
<!-- Modal Tambah User -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addUserModalLabel">Tambah User Baru</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label>Nama</label>
            <input type="text" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Role</label>
            <select class="form-control" required>
              <?php foreach ($role_list as $key => $val): ?>
                <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?> 