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
                require $file;
                return true;
            }
            return false;
        });
    }
} 