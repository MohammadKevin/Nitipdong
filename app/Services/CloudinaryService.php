<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    protected ?string $cloudName;
    protected ?string $apiKey;
    protected ?string $apiSecret;

    public function __construct()
    {
        $this->cloudName = config('services.cloudinary.cloud_name') ?: env('CLOUDINARY_CLOUD_NAME');
        $this->apiKey = config('services.cloudinary.api_key') ?: env('CLOUDINARY_API_KEY');
        $this->apiSecret = config('services.cloudinary.api_secret') ?: env('CLOUDINARY_API_SECRET');
    }

    /**
     * Check if Cloudinary credentials are fully provided.
     */
    public function isConfigured(): bool
    {
        return !empty($this->cloudName) && !empty($this->apiKey) && !empty($this->apiSecret);
    }

    /**
     * Upload an image to Cloudinary and return its HTTPS Secure URL.
     *
     * @param UploadedFile|string $file
     * @param string $folder
     * @return string|null
     */
    public function upload(UploadedFile|string $file, string $folder = 'belanjain_products'): ?string
    {
        if (!$this->isConfigured()) {
            Log::warning('[CloudinaryService] Credentials are not configured.');
            return null;
        }

        try {
            $timestamp = time();
            $paramsToSign = "folder={$folder}&timestamp={$timestamp}";
            $signature = sha1($paramsToSign . $this->apiSecret);

            $endpoint = "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload";
            $client = Http::timeout(45);

            if ($file instanceof UploadedFile) {
                $response = $client->attach(
                    'file',
                    file_get_contents($file->getRealPath()),
                    $file->hashName()
                )->post($endpoint, [
                    'api_key'   => $this->apiKey,
                    'timestamp' => $timestamp,
                    'folder'    => $folder,
                    'signature' => $signature,
                ]);
            } else {
                $response = $client->post($endpoint, [
                    'file'      => $file,
                    'api_key'   => $this->apiKey,
                    'timestamp' => $timestamp,
                    'folder'    => $folder,
                    'signature' => $signature,
                ]);
            }

            if ($response->successful()) {
                $data = $response->json();
                return $data['secure_url'] ?? $data['url'] ?? null;
            }

            Log::error('[CloudinaryService] Upload failed: ' . $response->body());
            return null;
        } catch (\Throwable $e) {
            Log::error('[CloudinaryService] Upload exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete an image from Cloudinary by its public ID or full Cloudinary URL.
     *
     * @param string $publicIdOrUrl
     * @return bool
     */
    public function delete(string $publicIdOrUrl): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        try {
            $publicId = $this->extractPublicId($publicIdOrUrl);
            if (!$publicId) {
                return false;
            }

            $timestamp = time();
            $paramsToSign = "public_id={$publicId}&timestamp={$timestamp}";
            $signature = sha1($paramsToSign . $this->apiSecret);

            $endpoint = "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/destroy";

            $response = Http::timeout(20)->post($endpoint, [
                'public_id' => $publicId,
                'api_key'   => $this->apiKey,
                'timestamp' => $timestamp,
                'signature' => $signature,
            ]);

            return $response->successful() && ($response->json('result') === 'ok');
        } catch (\Throwable $e) {
            Log::error('[CloudinaryService] Destroy exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Helper to extract public_id from a Cloudinary URL.
     */
    public function extractPublicId(string $publicIdOrUrl): ?string
    {
        if (!str_starts_with($publicIdOrUrl, 'http://') && !str_starts_with($publicIdOrUrl, 'https://')) {
            return $publicIdOrUrl;
        }

        $path = parse_url($publicIdOrUrl, PHP_URL_PATH);
        if (!$path) {
            return null;
        }

        // Match pattern: /upload/(?:v\d+/)?(folder/filename_without_ext)
        if (preg_match('/\/upload\/(?:v\d+\/)?(.+?)(?:\.[a-zA-Z0-9]+)?$/', $path, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
