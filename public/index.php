<?php
// Define base path
define('BASE_PATH', dirname(__DIR__));

// Load autoloader
require_once BASE_PATH . '/core/Autoloader.php';
Core\Autoloader::register();

// Error handling
ini_set('display_errors', 1);
error_reporting(E_ALL);
echo 'index.php loaded<br>';

session_start();

try {
    // Initialize router
    $router = new Core\Router();
    
    // Define routes
    $router->add('', 'HomeController', 'index');
    $router->add('post/{id}', 'PostController', 'show');
    $router->add('admin/login', 'AuthController', 'login');
    $router->add('admin/logout', 'AuthController', 'logout');
    $router->add('admin/posts', 'PostController', 'index');
    $router->add('admin/posts/create', 'PostController', 'create');
    $router->add('admin/posts/edit/{id}', 'PostController', 'edit');
    $router->add('admin/posts/delete/{id}', 'PostController', 'delete');
    $router->add('admin/posts/create', 'PostController', 'store');
    $router->add('admin/posts/edit/{id}', 'PostController', 'update');
    
    // Dispatch the route
    $router->dispatch();
} catch (\Exception $e) {
    // Handle errors
    if ($e->getCode() === 404) {
        header("HTTP/1.0 404 Not Found");
        echo "404 Not Found";
    } else {
        header("HTTP/1.0 500 Internal Server Error");
        echo "500 Internal Server Error";
        if (getenv('APP_ENV') === 'development') {
            echo "<br>Error: " . $e->getMessage();
        }
    }
} 