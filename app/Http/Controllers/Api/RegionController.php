<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IndonesianRegionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * Get list of all provinces in Indonesia.
     */
    public function provinces(): JsonResponse
    {
        $provinces = IndonesianRegionService::getProvinces();

        return response()->json([
            'success' => true,
            'data'    => $provinces,
        ]);
    }

    /**
     * Get list of regencies/cities in a province.
     */
    public function regencies(string $provinceId): JsonResponse
    {
        $regencies = IndonesianRegionService::getRegencies($provinceId);

        return response()->json([
            'success' => true,
            'data'    => $regencies,
        ]);
    }

    /**
     * Get list of districts (kecamatan) in a regency/city.
     */
    public function districts(string $regencyId): JsonResponse
    {
        $districts = IndonesianRegionService::getDistricts($regencyId);

        return response()->json([
            'success' => true,
            'data'    => $districts,
        ]);
    }

    /**
     * Get list of villages (kelurahan/desa) in a district.
     */
    public function villages(string $districtId): JsonResponse
    {
        $villages = IndonesianRegionService::getVillages($districtId);

        return response()->json([
            'success' => true,
            'data'    => $villages,
        ]);
    }

    /**
     * Reverse Geocode coordinates to address.
     */
    public function reverseGeocode(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
        ]);

        $lat = (float) $request->lat;
        $lng = (float) $request->lng;

        $result = IndonesianRegionService::reverseGeocode($lat, $lng);

        return response()->json([
            'success' => true,
            'data'    => $result,
        ]);
    }
}
