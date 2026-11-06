<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register the role middleware for admin authorization.
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        // Do not redirect API requests to a web login page.
        $middleware->redirectGuestsTo(
            fn (Request $request) =>
                $request->is('api/*') ? null : route('login')
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Return JSON errors for API requests.
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request, \Throwable $exception) =>
                $request->is('api/*') || $request->expectsJson()
        );
    })
    ->create();