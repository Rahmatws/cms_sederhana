<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Post;

class HomeController extends Controller {
    public function index() {
        $postModel = new Post();
        $posts = $postModel->getAllPosts();
        
        $this->view('home/index', ['posts' => $posts]);
    }
} 