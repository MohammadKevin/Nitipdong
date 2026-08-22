<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class SystemConfigController extends Controller
{
    /**
     * Get system status, maintenance mode, and app version config.
     */
    public function status(): JsonResponse
    {
        // Maintenance mode can be triggered by Laravel down command or env APP_MAINTENANCE=true
        $isMaintenance = env('APP_MAINTENANCE', false) || file_exists(storage_path('framework/down'));

        $latestVersion = env('APP_MOBILE_LATEST_VERSION', '1.0.1');
        $minVersion = env('APP_MOBILE_MIN_VERSION', '1.0.0');

        return response()->json([
            'success'             => true,
            'is_maintenance'      => (bool) $isMaintenance,
            'maintenance_title'   => 'Mode Pemeliharaan & Pengembangan 🛠️',
            'maintenance_message' => 'Aplikasi NitipDong sedang dalam tahap pembaruan fitur & optimalisasi sistem untuk pengalaman belanja yang lebih baik. Silakan coba kembali beberapa saat lagi.',
            'min_version'         => $minVersion,
            'latest_version'      => $latestVersion,
            'update_url'          => url('/download/app'),
            'support_whatsapp'    => '6281234567890',
        ]);
    }
}
