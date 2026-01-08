<?php
// api/index.php - Simple API endpoint for Vercel
// This file helps Vercel recognize this as a PHP project

require_once __DIR__.'/../vendor/autoload.php';

use Illuminate\Http\Request;

// For Vercel deployment, we need to handle the routing
// This is a simplified version for Vercel compatibility
if (isset($_ENV['VERCEL'])) {
    // Handle Vercel environment
    $request = Request::capture();
    $kernel = app()->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
} else {
    // Standard Laravel public/index.php
    $uri = urldecode(
        parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
    );

    if ($uri !== '/' && file_exists(__DIR__.'/../public'.$uri)) {
        return false;
    }

    require_once __DIR__.'/../public/index.php';
}