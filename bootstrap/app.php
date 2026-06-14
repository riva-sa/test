<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckUserRole::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'api.ai.key' => \App\Http\Middleware\AiApiKeyAuth::class,
            'set.locale' => \App\Http\Middleware\SetLocale::class,
            'broker.approved' => \App\Http\Middleware\EnsureBrokerApproved::class,
        ]);

        // Unauthenticated broker portal requests go to the broker login, not the CRM login
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            return $request->is('broker', 'broker/*')
                ? route('broker.login')
                : route('login');
        });

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
