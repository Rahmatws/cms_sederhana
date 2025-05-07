<?php
require_once '../config/database.php';
header('Content-Type: application/json');
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$result = [];
if ($q !== '') {
    $stmt = $pdo->prepare("SELECT id, title FROM posts WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC LIMIT 10");
    $like = '%' . $q . '%';
    $stmt->execute([$like, $like]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
echo json_encode($result); 