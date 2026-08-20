<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\IndonesianRegionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StoreSettingsController extends Controller
{
    public function edit(): View|RedirectResponse
    {
        $store = Auth::user()->store;

        if (!$store) {
            return redirect()->route('store.register')->with('info', 'Silakan daftarkan toko Anda terlebih dahulu.');
        }

        $provincesData = IndonesianRegionService::PROVINCES_DATA;

        return view('seller.settings.edit', compact('store', 'provincesData'));
    }

    public function update(Request $request): RedirectResponse
    {
        $store = Auth::user()->store;

        if (!$store) {
            return redirect()->route('store.register')->with('error', 'Toko tidak ditemukan.');
        }

        $request->validate([
            'name'                => ['required', 'string', 'max:255', 'unique:stores,name,' . $store->id],
            'description'         => ['nullable', 'string', 'max:2000'],
            'province'            => ['required', 'string', 'max:100'],
            'city'                => ['required', 'string', 'max:100'],
            'district'            => ['nullable', 'string', 'max:100'],
            'postal_code'         => ['nullable', 'string', 'max:10'],
            'address'             => ['required', 'string', 'max:1000'],
            'latitude'            => ['nullable', 'numeric'],
            'longitude'           => ['nullable', 'numeric'],
            'logo'                => ['nullable', 'image', 'max:2048'],
            'banner'              => ['nullable', 'image', 'max:4096'],
            'bank_name'           => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_holder' => ['nullable', 'string', 'max:100'],
        ], [
            'name.required'     => 'Nama toko wajib diisi.',
            'name.unique'       => 'Nama toko ini sudah digunakan oleh toko lain.',
            'province.required' => 'Pilih provinsi asal pengiriman toko.',
            'city.required'     => 'Pilih kota/kabupaten asal pengiriman toko (penentu gratis ongkir).',
            'address.required'  => 'Alamat lengkap toko/gudang wajib diisi.',
        ]);

        $data = [
            'name'                => $request->name,
            'slug'                => Str::slug($request->name),
            'description'         => $request->description,
            'province'            => $request->province,
            'city'                => $request->city,
            'district'            => $request->district,
            'postal_code'         => $request->postal_code,
            'address'             => $request->address,
            'latitude'            => $request->latitude,
            'longitude'           => $request->longitude,
            'bank_name'           => $request->bank_name,
            'bank_account_number' => $request->bank_account_number,
            'bank_account_holder' => $request->bank_account_holder,
        ];

        if ($request->hasFile('logo')) {
            if ($store->logo && Storage::disk('public')->exists($store->logo)) {
                Storage::disk('public')->delete($store->logo);
            }
            $data['logo'] = $request->file('logo')->store('stores/logos', 'public');
        }

        if ($request->hasFile('banner')) {
            if ($store->banner && Storage::disk('public')->exists($store->banner)) {
                Storage::disk('public')->delete($store->banner);
            }
            $data['banner'] = $request->file('banner')->store('stores/banners', 'public');
        }

        $store->update($data);

        return redirect()->route('seller.settings.edit')->with('success', 'Pengaturan alamat toko dan profil berhasil disimpan!');
    }
}
