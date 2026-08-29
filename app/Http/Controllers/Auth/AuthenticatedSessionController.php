<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // Jika email belum diverifikasi, arahkan wajib ke halaman verifikasi OTP
        if (!$user->hasVerifiedEmail()) {
            if (empty($user->otp_code) || ($user->otp_expires_at && now()->greaterThan($user->otp_expires_at))) {
                $otp = sprintf('%06d', mt_rand(100000, 999999));
                $user->update([
                    'otp_code'       => $otp,
                    'otp_expires_at' => now()->addMinutes(15),
                ]);
                try {
                    \Illuminate\Support\Facades\Mail::to($user->email)->queue(new \App\Mail\OtpMail($otp, 'register', $user->name));
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Login unverified OTP mail failed: ' . $e->getMessage());
                }
            }
            return redirect()->route('verification.notice')->with('warning', 'Email akun Anda belum diverifikasi. Silakan masukkan kode OTP yang telah dikirimkan ke email Anda untuk mengaktifkan akun.');
        }

        return match ($user->role) {
            'super_admin' => redirect()->route('super_admin.dashboard'),
            'admin'       => redirect()->route('admin.dashboard'),
            'seller'      => redirect()->route('seller.dashboard'),
            default       => redirect('/?is_from_login=true'),
        };
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
