<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class OtpVerificationController extends Controller
{
    public function show(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        if ($user->hasVerifiedEmail() && empty($user->pending_email)) {
            return redirect()->route($this->getDashboardRoute($user));
        }

        return view('auth.verify');
    }

    public function verify(Request $request)
    {
        // Bersihkan spasi, tanda hubung, dan karakter non-digit dari input
        $cleanedOtp = preg_replace('/\D/', '', (string) $request->otp);
        $request->merge(['otp' => $cleanedOtp]);

        $request->validate([
            'otp' => ['required', 'digits:6'],
        ], [
            'otp.required' => 'Kode OTP 6 digit wajib diisi.',
            'otp.digits'   => 'Kode OTP harus terdiri dari 6 angka.',
        ]);

        $user = $request->user();
        $enteredOtp = $cleanedOtp;
        $actualOtp = trim((string) $user->otp_code);

        // Periksa kedaluwarsa OTP SEBELUM validasi OTP
        if ($user->otp_expires_at && now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors([
                'otp' => 'Kode OTP sudah kedaluwarsa (15 menit). Silakan kirim ulang kode baru.'
            ]);
        }

        if ($actualOtp === '' || $actualOtp !== $enteredOtp) {
            return back()->withErrors(['otp' => 'Kode OTP tidak sesuai atau salah. Silakan periksa kembali email Anda.']);
        }

        if (!empty($user->pending_email)) {
            // Cek apakah email tujuan sudah dipakai user lain
            $existingUser = User::where('email', $user->pending_email)
                ->where('id', '!=', $user->id)
                ->first();

            if ($existingUser) {
                return back()->withErrors(['otp' => 'Email tujuan sudah terdaftar oleh pengguna lain.']);
            }

            DB::transaction(function () use ($user) {
                $user->email = $user->pending_email;
                $user->pending_email = null;
                $user->email_verified_at = now();
                $user->otp_code = null;
                $user->otp_expires_at = null;
                $user->save();
            });

            return redirect()->route('profile.edit')->with('status', 'email-updated-successfully');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->email_verified_at = now();
            $user->otp_code = null;
            $user->otp_expires_at = null;
            $user->save();

            return redirect()->route($this->getDashboardRoute($user))->with('success', 'Akun berhasil diverifikasi! Selamat datang di NitipDong.');
        }

        return redirect()->route($this->getDashboardRoute($user));
    }

    public function cancelChangeEmail(Request $request)
    {
        $user = $request->user();
        if (!empty($user->pending_email)) {
            $user->pending_email = null;
            $user->otp_code = null;
            $user->otp_expires_at = null;
            $user->save();
        }

        return redirect()->route('profile.edit')->with('status', 'Perubahan email telah dibatalkan.');
    }

    private function getDashboardRoute($user)
    {
        return match ($user->role) {
            'super_admin' => 'super_admin.dashboard',
            'admin' => 'admin.dashboard',
            'seller' => 'seller.dashboard',
            default => 'home',
        };
    }

    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail() && empty($user->pending_email)) {
            return redirect()->route($this->getDashboardRoute($user));
        }

        // Cooldown enforcement: 30 seconds
        $cacheKey = 'otp_resend_cooldown_web_' . $user->id;
        if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
            $remaining = (int) (\Illuminate\Support\Facades\Cache::get($cacheKey) - time());
            if ($remaining > 0) {
                return back()->withErrors(['otp' => "Mohon tunggu {$remaining} detik sebelum meminta kode OTP baru."]);
            }
        }

        \Illuminate\Support\Facades\Cache::put($cacheKey, time() + 30, 30);

        $otp = sprintf('%06d', mt_rand(100000, 999999));
        
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(15),
        ]);

        $targetEmail = $user->pending_email ?: $user->email;
        $type = $user->pending_email ? 'change_email' : 'register';

        try {
            if (config('mail.default') === 'log') {
                Log::warning('Web OTP resend skipped: mailer is set to "log". Configure MAIL_MAILER in .env for production use. OTP: ' . $otp);
            }
            \Illuminate\Support\Facades\Mail::to($targetEmail)->send(new \App\Mail\OtpMail($otp, $type, $user->name));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Web OTP resend error: ' . $e->getMessage());
            // Reset OTP state kalau email gagal dikirim
            $user->update([
                'otp_code'       => null,
                'otp_expires_at' => null,
            ]);
            return back()->with('error', 'Gagal mengirim kode OTP baru. Silakan coba lagi beberapa saat kemudian.');
        }

        return back()->with('status', 'Kode OTP baru telah berhasil dikirim ke ' . $targetEmail . '. Silakan periksa kotak masuk atau folder spam Anda.');
    }
}
