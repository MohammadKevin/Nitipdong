<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $emailChanged = $user->email !== $validated['email'];

        if ($emailChanged) {
            $newEmail = $validated['email'];
            unset($validated['email']);
            
            $otp = sprintf('%06d', mt_rand(100000, 999999));
            
            $user->pending_email = $newEmail;
            $user->otp_code = $otp;
            $user->otp_expires_at = now()->addMinutes(15);
            
            Mail::to($newEmail)->send(new OtpMail($otp, 'change_email', $user->name));
        }

        $user->fill($validated);
        $user->save();

        if ($emailChanged) {
            return Redirect::route('verification.notice')->with('status', 'Kode OTP telah dikirim ke email baru Anda.');
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
