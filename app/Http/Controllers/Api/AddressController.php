<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Get the authenticated user's primary or latest shipping address.
     */
    public function primary(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $address = UserAddress::where('user_id', $user->id)
            ->orderByDesc('is_default')
            ->orderByDesc('updated_at')
            ->first();

        if (!$address) {
            return response()->json([
                'success' => true,
                'address' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'address' => [
                'id'             => $address->id,
                'recipient_name' => $address->recipient_name,
                'phone'          => $address->phone,
                'full_address'   => $address->full_address,
                'city'           => $address->city,
                'district'       => $address->district,
                'province'       => $address->province,
                'postal_code'    => $address->postal_code,
                'notes'          => $address->notes,
                'latitude'       => $address->latitude,
                'longitude'      => $address->longitude,
                'is_default'     => $address->is_default,
            ],
        ]);
    }

    /**
     * Save or update user's primary shipping address.
     */
    public function storeOrUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone'          => ['required', 'string', 'max:50'],
            'full_address'   => ['required', 'string'],
            'province'       => ['nullable', 'string', 'max:100'],
            'city'           => ['nullable', 'string', 'max:100'],
            'district'       => ['nullable', 'string', 'max:100'],
            'village'        => ['nullable', 'string', 'max:100'],
            'postal_code'    => ['nullable', 'string', 'max:20'],
            'notes'          => ['nullable', 'string', 'max:255'],
            'latitude'       => ['nullable', 'numeric'],
            'longitude'      => ['nullable', 'numeric'],
        ]);

        $user = $request->user();

        // Update existing primary address or create new one
        $address = UserAddress::updateOrCreate(
            [
                'user_id'    => $user->id,
                'is_default' => true,
            ],
            [
                'label'          => $request->label ?? 'Rumah',
                'recipient_name' => $request->recipient_name,
                'phone'          => $request->phone,
                'full_address'   => $request->full_address,
                'province'       => $request->province ?? 'DKI Jakarta',
                'city'           => $request->city ?? 'Jakarta Pusat',
                'district'       => $request->district,
                'village'        => $request->village,
                'postal_code'    => $request->postal_code,
                'notes'          => $request->notes,
                'latitude'       => $request->latitude,
                'longitude'      => $request->longitude,
                'is_default'     => true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Alamat pengiriman berhasil disimpan ke akun Anda.',
            'address' => [
                'id'             => $address->id,
                'recipient_name' => $address->recipient_name,
                'phone'          => $address->phone,
                'full_address'   => $address->full_address,
                'province'       => $address->province,
                'city'           => $address->city,
                'district'       => $address->district,
                'village'        => $address->village,
                'postal_code'    => $address->postal_code,
                'notes'          => $address->notes,
                'latitude'       => $address->latitude,
                'longitude'      => $address->longitude,
                'is_default'     => $address->is_default,
            ],
        ]);
    }
}
