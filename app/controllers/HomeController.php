<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Post;

class HomeController extends Controller {
    public function index() {
        $postModel = new Post();
        $posts = $postModel->getAllPosts();
        
        // This will be replaced by rendering a view in the next commit
        echo "<h1>Daftar Postingan</h1>";
        if (empty($posts)) {
            echo "<p>Belum ada artikel yang diposting.</p>";
        } else {
            foreach ($posts as $post) {
                echo "<h2>" . htmlspecialchars($post['title']) . "</h2>";
                echo "<p>" . htmlspecialchars(substr($post['content'], 0, 200)) . "...</p>";
                echo "<p><a href=\"/post/" . $post['id'] . "\">Baca Selengkapnya</a></p>";
            }
        }
    }
} 