<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VoucherController extends Controller
{
    public function index(Request $request): View
    {
        $query = Voucher::with('store');

        if ($request->filled('type_filter')) {
            if ($request->type_filter === 'platform') {
                $query->whereNull('store_id');
            } elseif ($request->type_filter === 'store') {
                $query->whereNotNull('store_id');
            }
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $vouchers = $query->latest()->paginate(15)->withQueryString();

        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create(): View
    {
        return view('admin.vouchers.create');
    }

    public function store(Request $request): RedirectResponse
    {
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
            'store_id'     => null,
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

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher platform BelanjaIn berhasil dibuat!');
    }

    public function edit(Voucher $voucher): View
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher): RedirectResponse
    {
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

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher berhasil diperbarui!');
    }

    public function destroy(Voucher $voucher): RedirectResponse
    {
        $voucher->delete();

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher berhasil dihapus!');
    }

    public function toggle(Voucher $voucher): RedirectResponse
    {
        $voucher->update(['is_active' => !$voucher->is_active]);

        $status = $voucher->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Voucher {$voucher->code} berhasil {$status}.");
    }
}
