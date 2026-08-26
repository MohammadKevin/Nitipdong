<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to Google OAuth authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google after OAuth redirect.
     */
    public function handleGoogleCallback()
    {
        try {
            // Coba ambil data user Google secara stateful, jika sesi terputus fallback ke stateless
            try {
                $googleUser = Socialite::driver('google')->user();
            } catch (\Exception $e) {
                $googleUser = Socialite::driver('google')->stateless()->user();
            }

            if (empty($googleUser->getEmail())) {
                return redirect()->route('login')->with('error', 'Gagal mendapatkan data email dari akun Google Anda.');
            }

            // 1. Cari user berdasarkan google_id terlebih dahulu
            $user = User::where('google_id', $googleUser->getId())->first();

            // 2. Jika tidak ditemukan, cari berdasarkan email terdaftar
            if (!$user) {
                $user = User::where('email', $googleUser->getEmail())->first();

                if ($user) {
                    // Hubungkan akun yang sudah ada dengan Google ID
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar'    => $user->avatar ?: $googleUser->getAvatar(),
                    ]);
                } else {
                    // 3. Jika belum pernah mendaftar, buat akun baru otomatis
                    $user = User::create([
                        'uuid'              => (string) Str::uuid(),
                        'name'              => $googleUser->getName() ?: explode('@', $googleUser->getEmail())[0],
                        'email'             => $googleUser->getEmail(),
                        'google_id'         => $googleUser->getId(),
                        'avatar'            => $googleUser->getAvatar(),
                        'password'          => Hash::make(Str::random(32)),
                        'role'              => 'customer',
                        'email_verified_at' => now(),
                        'is_banned'         => false,
                    ]);
                }
            }

            // Cek status banned
            if ($user->is_banned) {
                return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan oleh administrator.');
            }

            // Login user ke sesi web
            Auth::login($user, true);
            request()->session()->regenerate();

            return redirect()->intended('/?is_from_login=true')->with('success', 'Berhasil masuk dengan Akun Google! Selamat berbelanja di NitipDong.');

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Terjadi kendala saat login dengan Google: ' . $e->getMessage());
        }
    }

    /**
     * API Endpoint for Mobile App Google Sign-In authentication.
     */
    public function handleApiGoogleLogin(Request $request)
    {
        $request->validate([
            'email'     => 'required|email',
            'name'      => 'required|string',
            'google_id' => 'required|string',
            'avatar'    => 'nullable|string',
        ]);

        try {
            $user = User::where('google_id', $request->google_id)->first();

            if (!$user) {
                $user = User::where('email', $request->email)->first();

                if ($user) {
                    $user->update([
                        'google_id' => $request->google_id,
                        'avatar'    => $user->avatar ?: $request->avatar,
                    ]);
                } else {
                    $user = User::create([
                        'uuid'              => (string) Str::uuid(),
                        'name'              => $request->name,
                        'email'             => $request->email,
                        'google_id'         => $request->google_id,
                        'avatar'            => $request->avatar,
                        'password'          => Hash::make(Str::random(32)),
                        'role'              => 'customer',
                        'email_verified_at' => now(),
                        'is_banned'         => false,
                    ]);
                }
            }

            if ($user->is_banned) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda telah dinonaktifkan oleh administrator.',
                ], 403);
            }

            // Generate Sanctum Access Token for Mobile
            $token = $user->createToken('mobile-google-auth')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Berhasil masuk dengan Google!',
                'token'   => $token,
                'user'    => [
                    'id'        => $user->id,
                    'uuid'      => $user->uuid,
                    'name'      => $user->name,
                    'email'     => $user->email,
                    'role'      => $user->role,
                    'phone'     => $user->phone,
                    'avatar'    => $user->avatar,
                    'google_id' => $user->google_id,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses login Google: ' . $e->getMessage(),
            ], 500);
        }
    }
}
