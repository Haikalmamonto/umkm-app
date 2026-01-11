<?php

// Set error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Matikan di production
ini_set('log_errors', 1);
ini_set('error_log', 'php://stderr'); // Log ke stderr agar bisa dilihat di Vercel

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Cek apakah direktori yang diperlukan ada
$required_dirs = [
    __DIR__ . '/../storage',
    __DIR__ . '/../storage/logs',
    __DIR__ . '/../storage/framework',
    __DIR__ . '/../storage/framework/cache',
    __DIR__ . '/../storage/framework/sessions',
    __DIR__ . '/../storage/framework/views',
    __DIR__ . '/../bootstrap/cache'
];

foreach ($required_dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Buat file database sederhana jika tidak ada
$dbPath = '/tmp/database.sqlite';
if (!file_exists($dbPath)) {
    touch($dbPath);
    chmod($dbPath, 0664);
}

// Cek maintenance mode
$maintenance_file = __DIR__ . '/../storage/framework/maintenance.php';
if (file_exists($maintenance_file)) {
    require $maintenance_file;
    exit;
}

// Coba load aplikasi Laravel
try {
    // Register autoloader
    require_once __DIR__ . '/../vendor/autoload.php';

    // Set environment variables untuk Vercel
    if (isset($_ENV['VERCEL'])) {
        $_ENV['APP_ENV'] = 'production';
        $_ENV['APP_DEBUG'] = 'false';
        $_ENV['APP_KEY'] = 'base64:Hb1HJW1KSA5L2jP6OR+YZ0R7twt6jZFvH10QGQ8JeYE=';
        $_ENV['DB_CONNECTION'] = 'sqlite';
        $_ENV['DB_DATABASE'] = $dbPath;
    }

    // Buat aplikasi
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // Jalankan kernel
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    // Tangani permintaan statis dulu
    $uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '');

    // Jika permintaan untuk file statis, kirim langsung
    $static_path = __DIR__ . '/../public' . $uri;
    if ($uri !== '/' && $uri !== '/index.php' && file_exists($static_path) && !is_dir($static_path)) {
        $ext = strtolower(pathinfo($static_path, PATHINFO_EXTENSION));
        
        $mime_types = [
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

        if (isset($mime_types[$ext])) {
            header('Content-Type: ' . $mime_types[$ext]);
        }
        
        readfile($static_path);
        exit;
    }

    // Tangani permintaan aplikasi
    $request = Request::capture();
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);

} catch (Exception $e) {
    // Log error
    error_log('Laravel Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    
    // Kirim error sederhana
    http_response_code(500);
    echo '<!DOCTYPE html>
<html>
<head>
    <title>500 Internal Server Error</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .error-container { max-width: 600px; margin: 0 auto; text-align: center; }
        h1 { color: #d00; }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>500 Internal Server Error</h1>
        <p>Sorry, something went wrong.</p>
        <p>Please try again later.</p>
    </div>
</body>
</html>';
    exit;
}