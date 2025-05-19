<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckUserRole::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);

        // Middleware groups
        $middleware->group('custom-dashboard', [
            // Add middleware to this group
            \App\Http\Middleware\CheckUserRole::class,
            \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
