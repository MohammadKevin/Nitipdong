<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle user login via API and generate Sanctum Token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau kata sandi yang Anda masukkan salah.',
            ], 401);
        }

        // Generate Sanctum Token
        $token = $user->createToken('nitipdong-mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil! Selamat datang kembali.',
            'token'   => $token,
            'user'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'phone'      => $user->phone ?? '',
                'role'       => $user->role,
                'avatar_url' => $user->avatar_url,
            ],
        ]);
    }

    /**
     * Handle user registration via API.
     */
    public function register(Request $request): JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:8'],
            'password_confirmation' => ['required', 'string', 'same:password'],
        ], [
            'name.required'                  => 'Nama lengkap wajib diisi.',
            'email.required'                 => 'Alamat email wajib diisi.',
            'email.email'                    => 'Format email tidak valid.',
            'email.unique'                   => 'Email ini sudah terdaftar. Silakan gunakan email lain atau masuk.',
            'password.required'              => 'Kata sandi wajib diisi.',
            'password.min'                   => 'Kata sandi minimal 8 karakter.',
            'password_confirmation.required' => 'Konfirmasi kata sandi wajib diisi.',
            'password_confirmation.same'     => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $otp = sprintf('%06d', mt_rand(100000, 999999));

            $user = User::create([
                'name'           => $request->name,
                'email'          => strtolower(trim($request->email)),
                'password'       => $request->password,
                'role'           => 'customer',
                'otp_code'       => $otp,
                'otp_expires_at' => now()->addMinutes(15),
            ]);

            // Send OTP email in background / safely wrapped
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(
                    new \App\Mail\OtpMail($otp, 'register', $user->name)
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Registration OTP email skipped/failed: ' . $e->getMessage());
            }

            $token = $user->createToken('nitipdong-mobile-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran akun berhasil!',
                'token'   => $token,
                'user'    => [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'email'      => $user->email,
                    'phone'      => $user->phone ?? '',
                    'role'       => $user->role,
                    'avatar_url' => $user->avatar_url,
                ],
            ], 201);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('API Registration error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendaftar: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get authenticated user profile.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user'    => [
                'id'             => $user->id,
                'name'           => $user->name,
                'email'          => $user->email,
                'phone'          => $user->phone ?? '',
                'role'           => $user->role,
                'avatar_url'     => $user->avatar_url,
                'cart_count'     => $user->carts()->count(),
                'wishlist_count' => $user->wishlists()->count(),
                'orders_count'   => $user->orders()->count(),
                'store'          => $user->store ? [
                    'id'   => $user->store->id,
                    'name' => $user->store->name,
                    'city' => $user->store->city ?? 'Jakarta',
                ] : null,
            ],
        ]);
    }

    /**
     * Handle user logout (revoke token).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil. Sesi Anda telah diakhiri.',
        ]);
    }
}
