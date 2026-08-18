<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VoucherController extends Controller
{
    public function index(Request $request): View
    {
        $store = Auth::user()->store;
        abort_if(!$store, 403, 'Anda belum memiliki toko aktif.');

        $query = Voucher::where('store_id', $store->id);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $vouchers = $query->latest()->paginate(10)->withQueryString();

        return view('seller.vouchers.index', compact('store', 'vouchers'));
    }

    public function create(): View
    {
        $store = Auth::user()->store;
        abort_if(!$store, 403, 'Anda belum memiliki toko aktif.');

        return view('seller.vouchers.create', compact('store'));
    }

    public function store(Request $request): RedirectResponse
    {
        $store = Auth::user()->store;
        abort_if(!$store, 403, 'Anda belum memiliki toko aktif.');

        $validated = $request->validate([
            'code'         => ['required', 'string', 'max:50', 'alpha_num', 'unique:vouchers,code'],
            'name'         => ['required', 'string', 'max:150'],
            'description'  => ['nullable', 'string', 'max:500'],
            'type'         => ['required', 'in:percent,fixed'],
            'amount'       => ['required', 'numeric', 'min:1'],
            'min_spend'    => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'quota'        => ['required', 'integer', 'min:1'],
            'expires_at'   => ['nullable', 'date', 'after:today'],
            'is_active'    => ['nullable', 'boolean'],
        ]);

        if ($validated['type'] === 'percent' && $validated['amount'] > 100) {
            return back()->withInput()->withErrors(['amount' => 'Diskon persentase tidak boleh melebihi 100%.']);
        }

        Voucher::create([
            'store_id'     => $store->id,
            'code'         => strtoupper($validated['code']),
            'name'         => $validated['name'],
            'description'  => $validated['description'] ?? null,
            'type'         => $validated['type'],
            'amount'       => $validated['amount'],
            'min_spend'    => $validated['min_spend'] ?? 0,
            'max_discount' => $validated['type'] === 'percent' ? ($validated['max_discount'] ?? null) : null,
            'quota'        => $validated['quota'],
            'is_active'    => $request->has('is_active'),
            'expires_at'   => $validated['expires_at'] ?? null,
        ]);

        return redirect()->route('seller.vouchers.index')->with('success', 'Voucher toko berhasil dibuat!');
    }

    public function edit(Voucher $voucher): View
    {
        $store = Auth::user()->store;
        abort_if(!$store || $voucher->store_id !== $store->id, 403, 'Akses tidak diizinkan.');

        return view('seller.vouchers.edit', compact('store', 'voucher'));
    }

    public function update(Request $request, Voucher $voucher): RedirectResponse
    {
        $store = Auth::user()->store;
        abort_if(!$store || $voucher->store_id !== $store->id, 403, 'Akses tidak diizinkan.');

        $validated = $request->validate([
            'code'         => ['required', 'string', 'max:50', 'alpha_num', 'unique:vouchers,code,' . $voucher->id],
            'name'         => ['required', 'string', 'max:150'],
            'description'  => ['nullable', 'string', 'max:500'],
            'type'         => ['required', 'in:percent,fixed'],
            'amount'       => ['required', 'numeric', 'min:1'],
            'min_spend'    => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'quota'        => ['required', 'integer', 'min:1'],
            'expires_at'   => ['nullable', 'date'],
            'is_active'    => ['nullable', 'boolean'],
        ]);

        if ($validated['type'] === 'percent' && $validated['amount'] > 100) {
            return back()->withInput()->withErrors(['amount' => 'Diskon persentase tidak boleh melebihi 100%.']);
        }

        $voucher->update([
            'code'         => strtoupper($validated['code']),
            'name'         => $validated['name'],
            'description'  => $validated['description'] ?? null,
            'type'         => $validated['type'],
            'amount'       => $validated['amount'],
            'min_spend'    => $validated['min_spend'] ?? 0,
            'max_discount' => $validated['type'] === 'percent' ? ($validated['max_discount'] ?? null) : null,
            'quota'        => $validated['quota'],
            'is_active'    => $request->has('is_active'),
            'expires_at'   => $validated['expires_at'] ?? null,
        ]);

        return redirect()->route('seller.vouchers.index')->with('success', 'Voucher toko berhasil diperbarui!');
    }

    public function destroy(Voucher $voucher): RedirectResponse
    {
        $store = Auth::user()->store;
        abort_if(!$store || $voucher->store_id !== $store->id, 403, 'Akses tidak diizinkan.');

        $voucher->delete();

        return redirect()->route('seller.vouchers.index')->with('success', 'Voucher toko berhasil dihapus!');
    }

    public function toggle(Voucher $voucher): RedirectResponse
    {
        $store = Auth::user()->store;
        abort_if(!$store || $voucher->store_id !== $store->id, 403, 'Akses tidak diizinkan.');

        $voucher->update(['is_active' => !$voucher->is_active]);

        $status = $voucher->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Voucher {$voucher->code} berhasil {$status}.");
    }
}
