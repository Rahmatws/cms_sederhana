<?php
namespace Core;

class Autoloader {
    public static function register() {
        spl_autoload_register(function ($class) {
            // Convert namespace to full file path
            $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
            $file = __DIR__ . '/../' . $file;
            
            // If the file exists, require it
            if (file_exists($file)) {
                echo "<pre>Autoloading: $file</pre>";
                require $file;
                return true;
            } else {
                echo "<pre>File not found for class: $class, path: $file</pre>";
            }
            return false;
        });
    }
} 