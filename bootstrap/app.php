<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Check if the newer Laravel 11+ configuration classes exist
if (class_exists(Middleware::class) && class_exists(Exceptions::class)) {
    // Laravel 11+ style configuration
    return Application::configure(basePath: dirname(__DIR__))
        ->withRouting(
            web: __DIR__.'/../routes/web.php',
            commands: __DIR__.'/../routes/console.php',
            health: '/up',
        )
        ->withMiddleware(function (Middleware $middleware) {
            //
        })
        ->withExceptions(function (Exceptions $exceptions) {
            //
        })->create();
} else {
    // Fallback for older Laravel versions or incomplete installations
    $app = new Application(
        $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
    );

    $app->singleton(
        Illuminate\Contracts\Console\Kernel::class,
        Illuminate\Foundation\Console\Kernel::class
    );

    $app->singleton(
        Illuminate\Contracts\Debug\ExceptionHandler::class,
        Illuminate\Foundation\Exceptions\Handler::class
    );

    return $app;
}