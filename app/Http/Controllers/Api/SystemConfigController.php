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

        return response()->json([
            'success'             => true,
            'is_maintenance'      => (bool) $isMaintenance,
            'maintenance_title'   => 'Mode Pemeliharaan & Pengembangan 🛠️',
            'maintenance_message' => 'Aplikasi NitipDong sedang dalam tahap pembaruan fitur & optimalisasi sistem untuk pengalaman belanja yang lebih baik. Silakan coba kembali beberapa saat lagi.',
            'min_version'         => '1.0.0',
            'latest_version'      => '1.0.0',
            'update_url'          => url('/'),
            'support_whatsapp'    => '6281234567890',
        ]);
    }
}
