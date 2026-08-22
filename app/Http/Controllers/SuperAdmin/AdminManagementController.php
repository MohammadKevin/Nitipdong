<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminManagementController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $query = User::where('role', 'admin')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $admins = $query->paginate(10)->withQueryString();
        $totalAdmins = User::where('role', 'admin')->count();

        return view('super_admin.admins.index', compact('admins', 'search', 'totalAdmins'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'password.min' => 'Password minimal harus 6 karakter.',
        ]);

        User::create([
            'uuid'              => (string) Str::uuid(),
            'name'              => $request->name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'password'          => Hash::make($request->password),
            'role'              => 'admin',
            'email_verified_at' => now(),
        ]);

        return redirect()->route('super_admin.admins.index')
            ->with('success', "Admin operasional '{$request->name}' berhasil ditambahkan!");
    }

    public function update(Request $request, User $admin): RedirectResponse
    {
        if ($admin->role !== 'admin') {
            abort(403, 'Akses tidak sah.');
        }

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $admin->id],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return redirect()->route('super_admin.admins.index')
            ->with('success', "Data Admin '{$admin->name}' berhasil diperbarui!");
    }

    public function destroy(User $admin): RedirectResponse
    {
        if ($admin->role !== 'admin') {
            abort(403, 'Hanya akun Admin Operasional yang dapat dihapus melalui menu ini.');
        }

        if ($admin->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $name = $admin->name;
        $admin->delete();

        return redirect()->route('super_admin.admins.index')
            ->with('success', "Akun Admin '{$name}' berhasil dihapus dari sistem.");
    }
}
