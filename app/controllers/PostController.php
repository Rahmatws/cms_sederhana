<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Post;

class PostController extends Controller {
    public function show($id) {
        $postModel = new Post();
        $post = $postModel->getPostById($id);
        
        $this->view('post/show', ['post' => $post]);
    }

    // Methods for admin post management (index, create, edit, delete) will be added later
} 