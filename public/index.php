<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll require it into the
| script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Create Directories If They Don't Exist
|--------------------------------------------------------------------------
|
| Ensure that necessary directories exist and are writable in the Vercel 
| environment.
|
*/

// Create necessary directories for Laravel to function properly
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

/*
|--------------------------------------------------------------------------
| Set Environment Variables for Vercel
|--------------------------------------------------------------------------
|
| Make sure Laravel can read environment variables in the Vercel environment.
|
*/

// Ensure environment variables are properly set for Vercel
if (isset($_ENV['VERCEL'])) {
    // Set environment variables that Laravel needs
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

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

// For Vercel environment, ensure production mode
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
if ($uri !== '/' && file_exists(__DIR__.$uri)) {
    return false;
}

$request = Request::capture();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);