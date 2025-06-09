<?php
namespace Core;

class Router {
    private $routes = [];
    private $params = [];

    public function add($route, $controller, $action) {
        $route = trim($route, '/');
        $this->routes[$route] = [
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function dispatch() {
        $url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        // Perbaikan: Hilangkan path ke /public jika ada
        $base = trim(dirname($_SERVER['SCRIPT_NAME']), '/');
        if ($base && strpos($url, $base) === 0) {
            $url = substr($url, strlen($base));
            $url = ltrim($url, '/');
        }
        echo "<pre>REQUEST_URI: {$_SERVER['REQUEST_URI']}\nSCRIPT_NAME: {$_SERVER['SCRIPT_NAME']}\nBase: $base\nURL after fix: $url</pre>";
        // If URL is empty, set to default route
        if (empty($url)) {
            $url = '';
        }

        // Check if route exists
        if (array_key_exists($url, $this->routes)) {
            $controller = $this->routes[$url]['controller'];
            $action = $this->routes[$url]['action'];
        } else {
            // Try to match dynamic routes
            foreach ($this->routes as $route => $params) {
                $pattern = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[^/]+)', $route);
                $pattern = "#^{$pattern}$#";
                
                if (preg_match($pattern, $url, $matches)) {
                    $this->params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                    $controller = $params['controller'];
                    $action = $params['action'];
                    break;
                }
            }
            
            if (!isset($controller)) {
                throw new \Exception('Route not found', 404);
            }
        }

        // Create controller instance
        $controllerClass = "App\\Controllers\\{$controller}";
        echo "<pre>Trying to load controller: $controllerClass</pre>";
        if (!class_exists($controllerClass)) {
            echo "<pre>Controller class not found: $controllerClass</pre>";
            throw new \Exception("Controller not found: {$controller}", 404);
        }

        $controllerInstance = new $controllerClass();
        
        // Check if action exists
        if (!method_exists($controllerInstance, $action)) {
            throw new \Exception("Action not found: {$action}", 404);
        }

        // Call the action with parameters
        return call_user_func_array([$controllerInstance, $action], $this->params);
    }
} 