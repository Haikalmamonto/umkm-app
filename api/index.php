<?php

/**
 * Vercel + Laravel Entry Point
 * 
 * This file serves as the entry point for Vercel deployments.
 * Vercel will automatically detect this as a PHP application
 * and route all requests through Laravel.
 */

// Ensure the application can write to necessary directories
if (!is_dir(__DIR__.'/../bootstrap/cache')) {
    mkdir(__DIR__.'/../bootstrap/cache', 0755, true);
}

// For Vercel environment, ensure we're in production mode
if (isset($_ENV['VERCEL'])) {
    putenv('APP_ENV=production');
    $_ENV['APP_ENV'] = 'production';
    $_SERVER['APP_ENV'] = 'production';
    
    // Set other important environment variables
    putenv('APP_DEBUG=false');
    $_ENV['APP_DEBUG'] = 'false';
    $_SERVER['APP_DEBUG'] = 'false';
}

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Check if the application is under maintenance
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the auto loader
require __DIR__.'/../vendor/autoload.php';

// Run the application
$app = require_once __DIR__.'/../bootstrap/app.php';

// For Vercel, we need to handle static assets before routing through Laravel
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// Handle static assets directly
if ($uri !== '/' && file_exists(__DIR__.'/../public'.$uri)) {
    return false;
}

// Capture the request and send it through Laravel
$request = Request::capture();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);