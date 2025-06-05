<?php
namespace Core;

abstract class Controller {
    protected function view($view, $data = []) {
        extract($data);
        
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            ob_start();
            require $viewFile;
            $content = ob_get_clean();
            
            // Load layout if exists
            $layoutFile = __DIR__ . '/../views/layouts/main.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                echo $content;
            }
        } else {
            throw new \Exception("View file not found: {$view}");
        }
    }

    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }

    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function getPost($key = null) {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? null;
    }

    protected function getQuery($key = null) {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? null;
    }
} 