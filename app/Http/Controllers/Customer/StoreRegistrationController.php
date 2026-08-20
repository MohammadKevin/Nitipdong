<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StoreRegistrationController extends Controller
{
    public function create(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->store) {
            return redirect()->route('customer.dashboard')->with('info', 'Anda sudah memiliki pengajuan toko.');
        }   

        return view('customer.store.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:stores,name'],
            'description' => ['required', 'string', 'min:15'],
            'address'     => ['required', 'string', 'min:10'],
            'city'        => ['nullable', 'string', 'max:100'],
            'province'    => ['nullable', 'string', 'max:100'],
            'district'    => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
        ]);

        $user = Auth::user();

        if ($user->store) {
            return redirect()->route('customer.dashboard')->with('error', 'Anda sudah memiliki pengajuan toko.');
        }

        Store::create([
            'user_id'     => $user->id,
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
            'address'     => $request->address,
            'city'        => $request->city ?: 'Jakarta Pusat',
            'province'    => $request->province ?: 'DKI Jakarta',
            'district'    => $request->district,
            'postal_code' => $request->postal_code,
            'status'      => 'pending',
        ]);

        return redirect()->route('customer.dashboard')->with('success', 'Pengajuan buka toko berhasil dikirim! Menunggu verifikasi admin.');
    }
}
