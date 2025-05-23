<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get all posts
$stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total posts count
$stmt = $pdo->query("SELECT COUNT(*) as total FROM posts");
$total_posts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get total categories count
$stmt = $pdo->query("SELECT COUNT(*) as total FROM categories");
$total_categories = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get total users count
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get user role count
$stmt = $pdo->query("SELECT role, COUNT(*) as total FROM users GROUP BY role");
$role_counts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statistik postingan per bulan (tahun berjalan)
$year = date('Y');
$chart_months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
$chart_posts = array_fill(0, 12, 0);
$stmt = $pdo->prepare("SELECT MONTH(created_at) as m, COUNT(*) as total FROM posts WHERE YEAR(created_at)=? GROUP BY m");
$stmt->execute([$year]);
foreach ($stmt as $row) {
    $chart_posts[$row['m']-1] = (int)$row['total'];
}

// Recent logins
$recent_logins = $pdo->query("SELECT l.login_time, u.username FROM logins l JOIN users u ON l.user_id=u.id ORDER BY l.login_time DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
// Recent posts
$recent_posts = $pdo->query("SELECT p.title, p.created_at, u.username FROM posts p JOIN users u ON p.user_id=u.id ORDER BY p.created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Simulasi status server
$server_status = 'Aktif';
$db_status = 'Terkoneksi';

// Handle quick draft (simulasi, tidak disimpan ke DB)
$draft_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quick_draft'])) {
    $draft_title = htmlspecialchars($_POST['draft_title']);
    $draft_content = htmlspecialchars($_POST['draft_content']);
    $draft_message = 'Draft berhasil disimpan (simulasi)!';
}

include 'header.php';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'admin';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Hero Welcome Section -->
    <section class="content-header bg-white" style="border-bottom:1px solid #eee;">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap">
            <div>
                <h2 class="mb-0">👋 Selamat datang kembali, <b><?php echo ucfirst($role); ?> <?php echo htmlspecialchars($username); ?></b>!</h2>
                <p class="mb-0">Hari ini kamu memiliki <span class="badge badge-<?php echo $total_posts == 0 ? 'danger' : 'success'; ?>"><?php echo $total_posts; ?> postingan</span> untuk dikelola.</p>
            </div>
            <div class="d-none d-md-block">
                <img src="https://undraw.co/api/illustrations/5e4b5e4b5e4b5e4b5e4b5e4b" alt="Mascot" style="height:80px;">
            </div>
        </div>
    </section>
    <!-- /.hero section -->

    <!-- Main content -->
    <div class="content pt-2">
        <div class="container-fluid">
            <!-- Quick Access & Search -->
            <div class="row mb-3 align-items-center">
                <div class="col-md-8 mb-2 mb-md-0">
                    <div class="btn-group" role="group">
                        <a href="posts.php" class="btn btn-outline-primary btn-lg"><i class="fas fa-file-alt"></i> Kelola Post</a>
                        <a href="users.php" class="btn btn-outline-success btn-lg"><i class="fas fa-user-plus"></i> Tambah User</a>
                        <a href="categories.php" class="btn btn-outline-warning btn-lg"><i class="fas fa-tags"></i> Kategori</a>
                    </div>
                </div>
                <div class="col-md-4 position-relative">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchPost" placeholder="🔍 Cari Postingan..." autocomplete="off">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button" id="btnSearchPost"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <div id="searchResult" class="list-group position-absolute w-100" style="z-index:1000; display:none;"></div>
                </div>
            </div>

            <!-- Info boxes with hover animation -->
            <div class="row">
                <div class="col-12 col-sm-6 col-md-4 mb-3">
                    <div class="info-box card-hover" style="transition:box-shadow .2s;">
                        <span class="info-box-icon bg-info elevation-1"><i class="fas fa-file-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Posts</span>
                            <span class="info-box-number"><?php echo $total_posts; ?> <?php if ($total_posts == 0): ?><span class="badge badge-danger ml-2">0 post</span><?php endif; ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 mb-3">
                    <div class="info-box card-hover" style="transition:box-shadow .2s;">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-tags"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Categories</span>
                            <span class="info-box-number"><?php echo $total_categories; ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 mb-3">
                    <div class="info-box card-hover" style="transition:box-shadow .2s;">
                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Users</span>
                            <span class="info-box-number"><?php echo $total_users; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik Chart.js -->
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header"><b>Statistik Postingan per Bulan</b></div>
                        <div class="card-body"><canvas id="postsChart"></canvas></div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header"><b>User per Role</b></div>
                        <div class="card-body"><canvas id="roleChart"></canvas></div>
                    </div>
                </div>
            </div>

            <!-- Widget: Server Status, Quick Draft, Recent Activity -->
            <div class="row mb-3">
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-header"><b>Status Server</b></div>
                        <div class="card-body">
                            <p><i class="fas fa-server text-success"></i> Server: <span class="badge badge-success">Aktif</span></p>
                            <p><i class="fas fa-database text-info"></i> Database: <span class="badge badge-info">Terkoneksi</span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-header"><b>Quick Draft</b></div>
                        <div class="card-body">
                            <?php if ($draft_message): ?><div class="alert alert-success py-1"><?php echo $draft_message; ?></div><?php endif; ?>
                            <form method="POST">
                                <input type="hidden" name="quick_draft" value="1">
                                <div class="form-group mb-2">
                                    <input type="text" name="draft_title" class="form-control" placeholder="Judul draft..." required>
                                </div>
                                <div class="form-group mb-2">
                                    <textarea name="draft_content" class="form-control" rows="2" placeholder="Isi draft..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block btn-sm">Simpan Draft</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-header"><b>Recent Activity</b></div>
                        <div class="card-body">
                            <?php foreach ($recent_logins as $log): ?>
                                <div><span class='badge badge-info'><?php echo date('d/m H:i', strtotime($log['login_time'])); ?></span> Login: <b><?php echo htmlspecialchars($log['username']); ?></b></div>
                            <?php endforeach; ?>
                            <?php foreach ($recent_posts as $post): ?>
                                <div><span class='badge badge-success'><?php echo date('d/m H:i', strtotime($post['created_at'])); ?></span> Post: <b><?php echo htmlspecialchars($post['title']); ?></b> oleh <?php echo htmlspecialchars($post['username']); ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main row: Recent Posts Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Posts</h3>
                            <div class="card-tools">
                                <a href="create_post.php" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> New Post
                                </a>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap" id="postsTable">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($posts)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center">No posts found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($posts as $post): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($post['title']); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></td>
                                                <td>
                                                    <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <a href="delete_post.php?id=<?php echo $post['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this post?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Card hover effect
$(function() {
    $('.card-hover').hover(function() {
        $(this).css('box-shadow', '0 4px 24px rgba(0,0,0,0.15)');
    }, function() {
        $(this).css('box-shadow', '');
    });
});
// Chart.js: Post per month
const ctxPosts = document.getElementById('postsChart').getContext('2d');
new Chart(ctxPosts, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($chart_months); ?>,
        datasets: [{
            label: 'Posts',
            data: <?php echo json_encode($chart_posts); ?>,
            backgroundColor: '#007bff',
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } }
    }
});
// Chart.js: User per role
const ctxRole = document.getElementById('roleChart').getContext('2d');
new Chart(ctxRole, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode(array_column($role_counts, 'role')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($role_counts, 'total')); ?>,
            backgroundColor: ['#007bff','#28a745','#ffc107','#6c757d']
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});
// Autocomplete search post AJAX
function doSearchPost() {
    var query = $('#searchPost').val().trim();
    if (query.length === 0) {
        $('#searchResult').hide().empty();
        return;
    }
    $.get('search_post_ajax.php', {q: query}, function(data) {
        var res = JSON.parse(data);
        var html = '';
        if (res.length > 0) {
            res.forEach(function(post) {
                html += '<a href="post_detail.php?id='+post.id+'" class="list-group-item list-group-item-action" target="_blank">'+post.title+'</a>';
            });
        } else {
            html = '<div class="list-group-item text-danger">Postingan Tidak ditemukan</div>';
        }
        $('#searchResult').html(html).show();
    });
}
$('#searchPost').on('input', doSearchPost);
$('#btnSearchPost').on('click', doSearchPost);
$('#searchPost').on('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        doSearchPost();
        $('#searchResult').show();
    }
});
$('#searchPost').on('blur', function() {
    setTimeout(function() { $('#searchResult').hide(); }, 200);
});
$('#searchPost').on('focus', function() {
    if ($('#searchResult').children().length > 0) $('#searchResult').show();
});
</script>

<?php include 'footer.php'; ?>

<style>
.card-hover:hover { box-shadow: 0 4px 24px rgba(0,0,0,0.15) !important; transform: translateY(-2px) scale(1.02); transition: .2s; }
.dark-mode .card-hover:hover { box-shadow: 0 4px 24px rgba(0,0,0,0.5) !important; }
.dark-mode .info-box-icon, .dark-mode .info-box-content { color: #fff !important; }
.dark-mode .info-box-icon.bg-info { background: #0056b3 !important; }
.dark-mode .info-box-icon.bg-success { background: #146c43 !important; }
.dark-mode .info-box-icon.bg-warning { background: #b8860b !important; }
.dark-mode .badge-danger { background: #ff5555 !important; }
</style> 