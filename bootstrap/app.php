<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    // Reference: No tenant routes loaded (routes/tenant.php removed)
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register custom middleware aliases (none currently)
        $middleware->alias([
            // No custom aliases
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
