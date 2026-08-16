<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpVerificationController extends Controller
{
    /**
     * Tampilkan form input OTP.
     */
    public function show(Request $request)
    {
        // Pastikan user sedang login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        // Jika user sudah terverifikasi dan tidak sedang meminta ganti email, redirect ke dashboard
        if ($user->hasVerifiedEmail() && empty($user->pending_email)) {
            return redirect()->route($this->getDashboardRoute($user));
        }

        return view('auth.verify');
    }

    /**
     * Verifikasi kode OTP yang diinputkan pengguna.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();

        // 1. Cek apakah OTP cocok
        if ($user->otp_code !== $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau salah.']);
        }

        // 2. Cek apakah OTP sudah kedaluwarsa
        if ($user->otp_expires_at && now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kedaluwarsa. Silakan minta kode baru.']);
        }

        // 3. Verifikasi OTP Berhasil
        // Skenario A: Mengganti Email (pending_email ada isinya)
        if (!empty($user->pending_email)) {
            $user->email = $user->pending_email;
            $user->pending_email = null;
            $user->email_verified_at = now();
            $user->otp_code = null;
            $user->otp_expires_at = null;
            $user->save();

            return redirect()->route('profile.edit')->with('status', 'email-updated-successfully');
        }

        // Skenario B: Pendaftaran Akun Baru (belum verifikasi)
        if (!$user->hasVerifiedEmail()) {
            $user->email_verified_at = now();
            $user->otp_code = null;
            $user->otp_expires_at = null;
            $user->save();

            return redirect()->route($this->getDashboardRoute($user))->with('success', 'Akun berhasil diverifikasi!');
        }

        return redirect()->route($this->getDashboardRoute($user));
    }

    /**
     * Dapatkan rute dashboard sesuai role.
     */
    private function getDashboardRoute($user)
    {
        return match ($user->role) {
            'super_admin' => 'super_admin.dashboard',
            'admin' => 'admin.dashboard',
            'seller' => 'seller.dashboard',
            default => 'customer.dashboard',
        };
    }

    /**
     * Kirim ulang kode OTP.
     */
    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail() && empty($user->pending_email)) {
            return redirect()->route($this->getDashboardRoute($user));
        }

        // Generate OTP Baru
        $otp = sprintf('%06d', mt_rand(100000, 999999));
        
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(15),
        ]);

        $targetEmail = $user->pending_email ?: $user->email;
        $type = $user->pending_email ? 'change_email' : 'register';

        \Illuminate\Support\Facades\Mail::to($targetEmail)->send(new \App\Mail\OtpMail($otp, $type, $user->name));

        return back()->with('status', 'Kode OTP baru telah dikirim ke email Anda.');
    }
}
