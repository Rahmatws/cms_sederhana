<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Post;

class PostController extends Controller {
    public function show($id) {
        $postModel = new Post();
        $post = $postModel->getPostById($id);
        
        // This will be replaced by rendering a view in the next commit
        if ($post) {
            echo "<h1>" . htmlspecialchars($post['title']) . "</h1>";
            echo "<p>" . nl2br(htmlspecialchars($post['content'])) . "</p>";
            echo "<p>Diposting pada: " . date('d F Y', strtotime($post['created_at'])) . "</p>
";
        } else {
            // Handle post not found (will be improved later)
            echo "<p>Postingan tidak ditemukan.</p>";
        }
    }

    // Methods for admin post management (index, create, edit, delete) will be added later
} 