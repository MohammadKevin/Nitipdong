<?php

use App\Http\Middleware\CheckWebMaintenanceMode;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->preventRequestsDuringMaintenance(except: [
            '/api/v1/system/status',
            'api/v1/system/status',
            'up',
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            '/api/payment/notification',
            '/api/midtrans/notification',
            '/api/midtrans/callback',
        ]);

        $middleware->append(CheckWebMaintenanceMode::class);

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