<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Mail\OtpMail;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    protected CloudinaryService $cloudinary;

    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

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

        // Handle Avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                if (str_starts_with($user->avatar, 'http://') || str_starts_with($user->avatar, 'https://')) {
                    if (str_contains($user->avatar, 'cloudinary.com')) {
                        $this->cloudinary->delete($user->avatar);
                    }
                } elseif (Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
            }

            // Upload new avatar
            $avatarUrl = null;
            if ($this->cloudinary->isConfigured()) {
                $avatarUrl = $this->cloudinary->upload($request->file('avatar'), 'belanjain_avatars');
            }
            if (!$avatarUrl) {
                $avatarUrl = $request->file('avatar')->store('avatars', 'public');
            }
            $user->avatar = $avatarUrl;
        }

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
