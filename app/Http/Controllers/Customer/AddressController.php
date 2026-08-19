<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function index(): View|JsonResponse
    {
        $addresses = UserAddress::where('user_id', Auth::id())
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        if (request()->wantsJson()) {
            return response()->json($addresses);
        }

        return view('customer.address.index', compact('addresses'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'label'          => ['required', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:100'],
            'phone'          => ['required', 'string', 'max:20'],
            'full_address'   => ['required', 'string', 'max:1000'],
            'city'           => ['nullable', 'string', 'max:100'],
            'province'       => ['nullable', 'string', 'max:100'],
            'postal_code'    => ['nullable', 'string', 'max:10'],
            'is_default'     => ['nullable', 'boolean'],
        ]);

        $userId = Auth::id();
        $isFirst = ! UserAddress::where('user_id', $userId)->exists();
        $isDefault = $request->boolean('is_default') || $isFirst;

        if ($isDefault) {
            UserAddress::where('user_id', $userId)->update(['is_default' => false]);
        }

        $address = UserAddress::create([
            'user_id'        => $userId,
            'label'          => $validated['label'],
            'recipient_name' => $validated['recipient_name'],
            'phone'          => $validated['phone'],
            'full_address'   => $validated['full_address'],
            'city'           => $validated['city'] ?? null,
            'province'       => $validated['province'] ?? null,
            'postal_code'    => $validated['postal_code'] ?? null,
            'is_default'     => $isDefault,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'data' => $address], 201);
        }

        return back()->with('success', 'Alamat pengiriman baru berhasil disimpan.');
    }

    public function update(Request $request, UserAddress $address): RedirectResponse|JsonResponse
    {
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Akses tidak sah.');
        }

        $validated = $request->validate([
            'label'          => ['required', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:100'],
            'phone'          => ['required', 'string', 'max:20'],
            'full_address'   => ['required', 'string', 'max:1000'],
            'city'           => ['nullable', 'string', 'max:100'],
            'province'       => ['nullable', 'string', 'max:100'],
            'postal_code'    => ['nullable', 'string', 'max:10'],
            'is_default'     => ['nullable', 'boolean'],
        ]);

        $isDefault = $request->boolean('is_default');

        if ($isDefault && ! $address->is_default) {
            UserAddress::where('user_id', Auth::id())->update(['is_default' => false]);
        }

        $address->update([
            'label'          => $validated['label'],
            'recipient_name' => $validated['recipient_name'],
            'phone'          => $validated['phone'],
            'full_address'   => $validated['full_address'],
            'city'           => $validated['city'] ?? null,
            'province'       => $validated['province'] ?? null,
            'postal_code'    => $validated['postal_code'] ?? null,
            'is_default'     => $isDefault ? true : $address->is_default,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'data' => $address]);
        }

        return back()->with('success', 'Alamat berhasil diperbarui.');
    }

    public function destroy(UserAddress $address): RedirectResponse|JsonResponse
    {
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Akses tidak sah.');
        }

        $wasDefault = $address->is_default;
        $address->delete();

        // Jika alamat default dihapus, set salah satu alamat tersisa sebagai default
        if ($wasDefault) {
            $nextAddress = UserAddress::where('user_id', Auth::id())->latest()->first();
            $nextAddress?->update(['is_default' => true]);
        }

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Alamat dihapus.']);
        }

        return back()->with('success', 'Alamat pengiriman berhasil dihapus.');
    }

    public function setDefault(UserAddress $address): RedirectResponse|JsonResponse
    {
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Akses tidak sah.');
        }

        UserAddress::where('user_id', Auth::id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Alamat utama berhasil diperbarui.']);
        }

        return back()->with('success', 'Alamat default berhasil diubah.');
    }
}
