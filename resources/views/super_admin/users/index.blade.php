<x-super-admin-layout>
    <x-slot name="title">
        Daftar Pengguna - {{ config('app.name', 'NitipDong') }}
    </x-slot>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Manajemen Pengguna Platform</h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftar seluruh akun terdaftar dan hak akses di NitipDong.</p>
        </div>

        <form action="{{ route('super_admin.users.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-72">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau email..."
                       class="input text-xs pl-8 h-9 rounded-md">
                <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 text-xs"></i>
            </div>
            <button type="submit" class="btn-primary text-xs h-9 px-4 rounded-md bg-cyan-700 hover:bg-cyan-800">Cari</button>
            @if($search)
                <a href="{{ route('super_admin.users.index') }}" class="btn-secondary text-xs h-9 px-3 rounded-md">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200/80 shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-5 font-semibold">ID</th>
                        <th class="py-3.5 px-5 font-semibold">Nama Pengguna</th>
                        <th class="py-3.5 px-5 font-semibold">Alamat Email</th>
                        <th class="py-3.5 px-5 font-semibold">Peran Akun</th>
                        <th class="py-3.5 px-5 font-semibold">Tanggal Registrasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-5 font-mono text-slate-400">#{{ $user->id }}</td>
                        <td class="py-3.5 px-5">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0891b2&color=fff&size=50" class="w-7 h-7 rounded-full object-cover shrink-0 border border-slate-200" alt="Avatar">
                                <div>
                                    <h4 class="font-semibold text-slate-900">{{ $user->name }}</h4>
                                    <p class="text-[11px] text-slate-400 truncate max-w-[150px]">{{ $user->phone ?? 'No. telp belum ada' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-5 text-slate-600 font-mono">{{ $user->email }}</td>
                        <td class="py-3.5 px-5">
                            @if($user->role === 'super_admin')
                                <span class="px-2.5 py-0.5 rounded-full bg-cyan-900 text-cyan-200 font-semibold text-[10px] border border-cyan-700 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span> Super Admin
                                </span>
                            @elseif($user->role === 'admin')
                                <span class="px-2.5 py-0.5 rounded-full bg-purple-50 text-purple-700 font-semibold text-[10px] border border-purple-200 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span> Admin
                                </span>
                            @elseif($user->role === 'seller')
                                <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 font-semibold text-[10px] border border-amber-200 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Seller
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-semibold text-[10px] border border-slate-200 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Customer
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-5 text-slate-400">{{ $user->created_at->translatedFormat('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-400">Tidak ada pengguna ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
        <div class="p-4 border-t border-slate-100 bg-white">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</x-super-admin-layout>
