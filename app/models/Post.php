<?php
namespace App\Models;

use Core\Model;

class Post extends Model {
    protected $table = 'posts';

    public function getAllPosts() {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        return $this->db->fetchAll($sql);
    }

    public function getPostById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function createPost($data) {
        return $this->create($data);
    }

    public function updatePost($id, $data) {
        return $this->update($id, $data);
    }

    public function deletePost($id) {
        return $this->delete($id);
    }

    // You can add more methods here for create, update, delete if needed later
} 