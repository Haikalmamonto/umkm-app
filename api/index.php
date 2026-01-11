<?php

// Enable error logging for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Tetap matikan di production
ini_set('log_errors', 1);
ini_set('error_log', dirname(__DIR__) . '/storage/logs/error.log');

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// For Vercel, ensure directories exist
if (!is_dir(__DIR__.'/../storage')) {
    mkdir(__DIR__.'/../storage', 0755, true);
}
if (!is_dir(__DIR__.'/../storage/logs')) {
    mkdir(__DIR__.'/../storage/logs', 0755, true);
}
if (!is_dir(__DIR__.'/../storage/framework')) {
    mkdir(__DIR__.'/../storage/framework', 0755, true);
}
if (!is_dir(__DIR__.'/../storage/framework/cache')) {
    mkdir(__DIR__.'/../storage/framework/cache', 0755, true);
}
if (!is_dir(__DIR__.'/../storage/framework/sessions')) {
    mkdir(__DIR__.'/../storage/framework/sessions', 0755, true);
}
if (!is_dir(__DIR__.'/../storage/framework/views')) {
    mkdir(__DIR__.'/../storage/framework/views', 0755, true);
}
if (!is_dir(__DIR__.'/../storage/framework/testing')) {
    mkdir(__DIR__.'/../storage/framework/testing', 0755, true);
}
if (!is_dir(__DIR__.'/../bootstrap/cache')) {
    mkdir(__DIR__.'/../bootstrap/cache', 0755, true);
}

// Ensure SQLite database exists
$dbPath = __DIR__ . '/../database/database.sqlite';
if (!file_exists($dbPath)) {
    // Create the database file
    touch($dbPath);
    chmod($dbPath, 0664);
}

// Check maintenance mode
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register autoloader
require __DIR__.'/../vendor/autoload.php';

// For Vercel environment, ensure production mode
if (isset($_ENV['VERCEL'])) {
    // Set environment variables
    if (!isset($_ENV['APP_KEY']) && isset($_SERVER['APP_KEY'])) {
        $_ENV['APP_KEY'] = $_SERVER['APP_KEY'];
    }
    if (!isset($_ENV['APP_ENV']) && isset($_SERVER['APP_ENV'])) {
        $_ENV['APP_ENV'] = $_SERVER['APP_ENV'];
    }
    if (!isset($_ENV['APP_DEBUG']) && isset($_SERVER['APP_DEBUG'])) {
        $_ENV['APP_DEBUG'] = $_SERVER['APP_DEBUG'];
    }
    if (!isset($_ENV['DB_CONNECTION']) && isset($_SERVER['DB_CONNECTION'])) {
        $_ENV['DB_CONNECTION'] = $_SERVER['DB_CONNECTION'];
    }
    if (!isset($_ENV['DB_DATABASE']) && isset($_SERVER['DB_DATABASE'])) {
        $_ENV['DB_DATABASE'] = $_SERVER['DB_DATABASE'];
    }
}

try {
    // Create application
    $app = require_once __DIR__.'/../bootstrap/app.php';

    if (isset($_ENV['VERCEL'])) {
        $app->useEnvironmentPath(__DIR__.'/..');
        $app->detectEnvironment(function () {
            return 'production';
        });
    }

    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    // Handle static assets first
    $uri = urldecode(
        parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
    );

    // Serve static files directly if they exist
    if ($uri !== '/' && $uri !== '/index.php' && file_exists(__DIR__.'/../public'.$uri)) {
        // For CSS, JS, images and other static files
        $path = __DIR__.'/../public'.$uri;
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        
        // Set appropriate content type
        $contentTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject'
        ];
        
        if (isset($contentTypes[$ext])) {
            header('Content-Type: ' . $contentTypes[$ext]);
        }
        
        readfile($path);
        return;
    }

    // Capture and handle request
    $request = Request::capture();
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
} catch (Exception $e) {
    // Log the actual error for debugging
    error_log('Laravel Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    
    // Return a simple error response
    http_response_code(500);
    echo "Internal Server Error";
    exit;
}