<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Post;

class HomeController extends Controller {
    public function index() {
        echo "HomeController index loaded<br>";
        $postModel = new Post();
        $posts = $postModel->getAllPosts();
        
        $this->view('home/index', ['posts' => $posts]);
    }
} 