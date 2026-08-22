<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckWebMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->is('api/*') && !$request->is('download/*') && !$request->is('up')) {
            $isWebDown = env('APP_WEB_MAINTENANCE', false) || file_exists(storage_path('framework/maintenance_web.json'));
            if ($isWebDown) {
                abort(503, 'Website sedang dalam pemeliharaan sistem.');
            }
        }

        return $next($request);
    }
}
