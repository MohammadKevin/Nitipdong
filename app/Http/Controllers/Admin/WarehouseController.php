<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $warehouses = Warehouse::when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('city', 'like', "%{$search}%")
                      ->orWhere('province', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $totalWarehouses = Warehouse::count();
        $activeWarehouses = Warehouse::where('is_active', true)->count();

        return view('admin.warehouses.index', compact('warehouses', 'search', 'totalWarehouses', 'activeWarehouses'));
    }

    public function create(): View
    {
        return view('admin.warehouses.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code'      => ['required', 'string', 'max:50', 'unique:warehouses,code'],
            'name'      => ['required', 'string', 'max:255'],
            'city'      => ['required', 'string', 'max:100'],
            'province'  => ['required', 'string', 'max:100'],
            'address'   => ['required', 'string', 'max:1000'],
            'lat'       => ['required', 'numeric', 'between:-90,90'],
            'lng'       => ['required', 'numeric', 'between:-180,180'],
            'phone'     => ['nullable', 'string', 'max:50'],
            'pic_name'  => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Warehouse::create($validated);

        $route = auth()->user()?->role === 'super_admin' ? 'super_admin.warehouses.index' : 'admin.warehouses.index';
        return redirect()->route($route)->with('success', 'Gudang Hub DC NitipDongExpress baru berhasil ditambahkan.');
    }

    public function edit(Warehouse $warehouse): View
    {
        return view('admin.warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse): RedirectResponse
    {
        $validated = $request->validate([
            'code'      => ['required', 'string', 'max:50', 'unique:warehouses,code,' . $warehouse->id],
            'name'      => ['required', 'string', 'max:255'],
            'city'      => ['required', 'string', 'max:100'],
            'province'  => ['required', 'string', 'max:100'],
            'address'   => ['required', 'string', 'max:1000'],
            'lat'       => ['required', 'numeric', 'between:-90,90'],
            'lng'       => ['required', 'numeric', 'between:-180,180'],
            'phone'     => ['nullable', 'string', 'max:50'],
            'pic_name'  => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $warehouse->update($validated);

        $route = auth()->user()?->role === 'super_admin' ? 'super_admin.warehouses.index' : 'admin.warehouses.index';
        return redirect()->route($route)->with('success', 'Data gudang Hub DC NDX berhasil diperbarui.');
    }

    public function toggle(Warehouse $warehouse): RedirectResponse
    {
        $warehouse->update(['is_active' => !$warehouse->is_active]);

        $statusText = $warehouse->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Gudang '{$warehouse->name}' berhasil {$statusText}.");
    }

    public function destroy(Warehouse $warehouse): RedirectResponse
    {
        if ($warehouse->orders()->whereIn('status', ['processing', 'shipped'])->count() > 0) {
            return back()->with('error', "Gudang '{$warehouse->name}' tidak dapat dihapus karena masih menangani pengiriman pesanan aktif.");
        }

        $warehouse->delete();

        $route = auth()->user()?->role === 'super_admin' ? 'super_admin.warehouses.index' : 'admin.warehouses.index';
        return redirect()->route($route)->with('success', "Gudang '{$warehouse->name}' berhasil dihapus.");
    }
}
