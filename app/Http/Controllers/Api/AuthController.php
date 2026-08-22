<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\OtpMail;

class AuthController extends Controller
{
    /**
     * Handle user login via API (supports Email or Phone Number).
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'login'    => ['sometimes', 'string'],
            'email'    => ['sometimes', 'string'],
            'phone'    => ['sometimes', 'string'],
            'password' => ['required', 'string'],
        ], [
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $loginIdentifier = $request->login ?? $request->email ?? $request->phone;
        if (empty($loginIdentifier)) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat email atau nomor HP wajib diisi.',
            ], 422);
        }

        $loginIdentifier = trim($loginIdentifier);

        // Find user by email OR phone
        $user = User::where(function ($query) use ($loginIdentifier) {
            $query->where('email', strtolower($loginIdentifier))
                  ->orWhere('phone', $loginIdentifier);
        })->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Akun atau kata sandi yang Anda masukkan tidak cocok.',
            ], 401);
        }

        // Generate Sanctum Token
        $token = $user->createToken('nitipdong-mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil! Selamat datang kembali, ' . $user->name,
            'token'   => $token,
            'user'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'phone'      => $user->phone ?? '',
                'role'       => $user->role,
                'avatar_url' => $user->avatar_url,
                'is_verified'=> $user->email_verified_at !== null,
            ],
        ]);
    }

    /**
     * Handle user registration via API.
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone'                 => ['nullable', 'string', 'max:20'],
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
                'name'           => trim($request->name),
                'email'          => strtolower(trim($request->email)),
                'phone'          => $request->phone ? trim($request->phone) : null,
                'password'       => $request->password,
                'role'           => 'customer',
                'otp_code'       => $otp,
                'otp_expires_at' => now()->addMinutes(15),
            ]);

            // Send OTP email in background / safely wrapped
            try {
                Mail::to($user->email)->send(new OtpMail($otp, 'register', $user->name));
            } catch (\Throwable $e) {
                Log::warning('Registration OTP email skipped/failed: ' . $e->getMessage());
            }

            $token = $user->createToken('nitipdong-mobile-app')->plainTextToken;

            return response()->json([
                'success'      => true,
                'message'      => 'Pendaftaran akun berhasil! Silakan verifikasi kode OTP Anda.',
                'token'        => $token,
                'otp_preview'  => app()->environment('local') ? $otp : null,
                'user'         => [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'email'      => $user->email,
                    'phone'      => $user->phone ?? '',
                    'role'       => $user->role,
                    'avatar_url' => $user->avatar_url,
                ],
            ], 201);
        } catch (\Throwable $e) {
            Log::error('API Registration error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendaftar: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle 6-digit OTP verification.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'identifier' => ['required', 'string'],
            'otp_code'   => ['required', 'string', 'size:6'],
        ], [
            'identifier.required' => 'Identitas akun (email atau nomor HP) wajib disertakan.',
            'otp_code.required'   => 'Kode OTP 6 digit wajib diisi.',
            'otp_code.size'       => 'Kode OTP harus terdiri dari 6 angka.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $identifier = trim($request->identifier);
        $user = User::where('email', strtolower($identifier))
                    ->orWhere('phone', $identifier)
                    ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak ditemukan.',
            ], 404);
        }

        // Validate OTP Code
        if ($user->otp_code !== $request->otp_code) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP yang Anda masukkan salah. Silakan coba lagi.',
            ], 422);
        }

        // Validate Expiry
        if ($user->otp_expires_at && now()->isAfter($user->otp_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP telah kedaluwarsa. Silakan kirim ulang kode baru.',
            ], 422);
        }

        // Mark verified & clear OTP
        $user->update([
            'email_verified_at' => now(),
            'otp_code'          => null,
            'otp_expires_at'    => null,
        ]);

        $token = $user->createToken('nitipdong-mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Verifikasi akun berhasil! Selamat menikmati layanan NitipDong.',
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
     * Resend fresh 6-digit OTP code with cooldown.
     */
    public function resendOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'identifier' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Identitas akun wajib disertakan.',
            ], 422);
        }

        $identifier = trim($request->identifier);
        $user = User::where('email', strtolower($identifier))
                    ->orWhere('phone', $identifier)
                    ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak ditemukan.',
            ], 404);
        }

        $otp = sprintf('%06d', mt_rand(100000, 999999));
        $user->update([
            'otp_code'       => $otp,
            'otp_expires_at' => now()->addMinutes(15),
        ]);

        try {
            Mail::to($user->email)->send(new OtpMail($otp, 'resend', $user->name));
        } catch (\Throwable $e) {
            Log::warning('Resend OTP email skipped/failed: ' . $e->getMessage());
        }

        return response()->json([
            'success'          => true,
            'message'          => 'Kode OTP baru telah dikirimkan ke ' . $user->email,
            'cooldown_seconds' => 60,
            'otp_preview'      => app()->environment('local') ? $otp : null,
        ]);
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
