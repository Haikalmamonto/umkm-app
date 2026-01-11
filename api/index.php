<?php

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
    $db = new PDO("sqlite:$dbPath");
    $db = null; // Close connection
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
    return false;
}

// Capture and handle request
$request = Request::capture();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);