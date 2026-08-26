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
     * Get the appropriate Google OAuth redirect URL dynamically.
     */
    protected function getRedirectUrl(): string
    {
        $configured = config('services.google.redirect');
        if (!empty($configured) && filter_var($configured, FILTER_VALIDATE_URL)) {
            $currentHost = request()->getHost();
            if (($currentHost === 'localhost' || $currentHost === '127.0.0.1') && !str_contains($configured, 'localhost') && !str_contains($configured, '127.0.0.1')) {
                return url('/auth/google/callback');
            }
            return $configured;
        }

        return url('/auth/google/callback');
    }

    /**
     * Redirect the user to Google OAuth authentication page.
     */
    public function redirectToGoogle()
    {
        $redirectUrl = $this->getRedirectUrl();

        return Socialite::driver('google')
            ->redirectUrl($redirectUrl)
            ->stateless()
            ->redirect();
    }

    /**
     * Obtain the user information from Google after OAuth redirect.
     */
    public function handleGoogleCallback()
    {
        try {
            $redirectUrl = $this->getRedirectUrl();

            // Ambil data user Google secara stateless untuk mencegah InvalidStateException
            $googleUser = Socialite::driver('google')
                ->redirectUrl($redirectUrl)
                ->stateless()
                ->user();

            if (!$googleUser || empty($googleUser->getEmail())) {
                return redirect()->route('login')->with('error', 'Gagal mendapatkan data email dari akun Google Anda.');
            }

            // 1. Cari user berdasarkan google_id terlebih dahulu
            $user = User::where('google_id', $googleUser->getId())->first();

            // 2. Jika tidak ditemukan, cari berdasarkan email terdaftar
            if (!$user) {
                $user = User::where('email', strtolower($googleUser->getEmail()))->first();

                if ($user) {
                    // Hubungkan akun yang sudah ada dengan Google ID & update avatar jika belum ada
                    $user->update([
                        'google_id'         => $googleUser->getId(),
                        'avatar'            => $user->avatar ?: $googleUser->getAvatar(),
                        'email_verified_at' => $user->email_verified_at ?: now(),
                    ]);
                } else {
                    // 3. Jika belum pernah mendaftar, buat akun baru otomatis
                    $userName = $googleUser->getName();
                    if (empty($userName)) {
                        $userName = explode('@', $googleUser->getEmail())[0];
                    }

                    $user = User::create([
                        'uuid'              => (string) Str::uuid(),
                        'name'              => $userName,
                        'email'             => strtolower($googleUser->getEmail()),
                        'google_id'         => $googleUser->getId(),
                        'avatar'            => $googleUser->getAvatar(),
                        'password'          => Hash::make(Str::random(32)),
                        'role'              => 'customer',
                        'email_verified_at' => now(),
                        'is_banned'         => false,
                    ]);
                }
            } else {
                // Update avatar & status verifikasi jika belum ada
                $updateData = [];
                if (empty($user->avatar) && $googleUser->getAvatar()) {
                    $updateData['avatar'] = $googleUser->getAvatar();
                }
                if (empty($user->email_verified_at)) {
                    $updateData['email_verified_at'] = now();
                }
                if (!empty($updateData)) {
                    $user->update($updateData);
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
            'name'      => 'nullable|string',
            'google_id' => 'required|string',
            'avatar'    => 'nullable|string',
        ]);

        try {
            $email = strtolower(trim($request->email));
            $googleId = trim($request->google_id);
            $name = trim($request->name ?? '');
            if (empty($name)) {
                $name = explode('@', $email)[0];
            }

            $user = User::where('google_id', $googleId)->first();

            if (!$user) {
                $user = User::where('email', $email)->first();

                if ($user) {
                    $user->update([
                        'google_id'         => $googleId,
                        'avatar'            => $user->avatar ?: $request->avatar,
                        'email_verified_at' => $user->email_verified_at ?: now(),
                    ]);
                } else {
                    $user = User::create([
                        'uuid'              => (string) Str::uuid(),
                        'name'              => $name,
                        'email'             => $email,
                        'google_id'         => $googleId,
                        'avatar'            => $request->avatar,
                        'password'          => Hash::make(Str::random(32)),
                        'role'              => 'customer',
                        'email_verified_at' => now(),
                        'is_banned'         => false,
                    ]);
                }
            } else {
                $updateData = [];
                if (empty($user->avatar) && !empty($request->avatar)) {
                    $updateData['avatar'] = $request->avatar;
                }
                if (empty($user->email_verified_at)) {
                    $updateData['email_verified_at'] = now();
                }
                if (!empty($updateData)) {
                    $user->update($updateData);
                }
            }

            if ($user->is_banned) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda telah dinonaktifkan oleh administrator.',
                ], 403);
            }

            // Generate Sanctum Access Token for Mobile
            $token = $user->createToken('nitipdong-mobile-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Berhasil masuk dengan Google! Selamat datang, ' . $user->name,
                'token'   => $token,
                'user'    => [
                    'id'                => $user->id,
                    'uuid'              => $user->uuid,
                    'name'              => $user->name,
                    'email'             => $user->email,
                    'phone'             => $user->phone ?? '',
                    'role'              => $user->role,
                    'avatar'            => $user->avatar,
                    'avatar_url'        => $user->avatar_url,
                    'google_id'         => $user->google_id,
                    'biometric_enabled' => (bool) $user->biometric_enabled,
                    'biometric_type'    => $user->biometric_type ?? 'fingerprint',
                    'cart_count'        => method_exists($user, 'cartItems') ? $user->cartItems()->count() : 0,
                    'wishlist_count'    => method_exists($user, 'wishlists') ? $user->wishlists()->count() : 0,
                    'orders_count'      => method_exists($user, 'orders') ? $user->orders()->count() : 0,
                    'is_verified'       => $user->email_verified_at !== null,
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
