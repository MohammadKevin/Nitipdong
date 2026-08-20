<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            '/api/payment/notification',
            '/api/duitku/callback',
        ]);

        $middleware->redirectUsersTo(function (Request $request) {
            $user = $request->user();
            return match ($user?->role) {
                'super_admin' => route('super_admin.dashboard'),
                'admin' => route('admin.dashboard'),
                'seller' => route('seller.dashboard'),
                default => url('/?is_from_login=true'),
            };
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();