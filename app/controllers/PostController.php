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

    public function index() {
        $postModel = new Post();
        $posts = $postModel->getAllPosts();
        $this->view('posts/index', ['posts' => $posts]);
    }

    public function create() {
        $this->view('posts/create');
    }

    public function store() {
        $postModel = new Post();
        $data = [
            'title' => $_POST['title'],
            'content' => $_POST['content'],
            'created_at' => date('Y-m-d H:i:s')
        ];
        $postModel->createPost($data);
        $this->redirect('/admin/posts');
    }

    public function edit($id) {
        $postModel = new Post();
        $post = $postModel->getPostById($id);
        $this->view('posts/edit', ['post' => $post]);
    }

    public function update($id) {
        $postModel = new Post();
        $data = [
            'title' => $_POST['title'],
            'content' => $_POST['content']
        ];
        $postModel->updatePost($id, $data);
        $this->redirect('/admin/posts');
    }

    public function delete($id) {
        $postModel = new Post();
        $postModel->deletePost($id);
        $this->redirect('/admin/posts');
    }

    // Methods for admin post management (index, create, edit, delete) will be added later
} 