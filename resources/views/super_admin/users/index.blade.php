<x-super-admin-layout>
    <x-slot name="title">
        Daftar Pengguna Platform - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Daftar Pengguna Platform
    </x-slot>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 pb-1">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                Manajemen Pengguna Platform
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola seluruh akun terdaftar, hak akses, kontrol suspensi, dan moderasi akun pengguna di NitipDong.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg p-4 border border-slate-200/90 shadow-xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-700 border border-blue-200/60 flex items-center justify-center text-base shrink-0 font-mono-num">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono-num">Total Pengguna</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 font-mono-num">{{ number_format($totalUsers ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 border border-slate-200/90 shadow-xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/60 flex items-center justify-center text-base shrink-0 font-mono-num">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono-num">Member Pembeli</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 font-mono-num">{{ number_format($customerCount ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 border border-slate-200/90 shadow-xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-700 border border-amber-200/60 flex items-center justify-center text-base shrink-0 font-mono-num">
                <i class="fa-solid fa-shop"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono-num">Seller Toko</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 font-mono-num">{{ number_format($sellerCount ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 border border-slate-200/90 shadow-xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-700 border border-rose-200/60 flex items-center justify-center text-base shrink-0 font-mono-num">
                <i class="fa-solid fa-user-slash"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono-num">Akun Disuspend / Banned</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 font-mono-num">{{ number_format($bannedCount ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-slate-200/90 shadow-xs overflow-hidden">

        <div class="p-4 border-b border-slate-100 flex flex-col lg:flex-row justify-between lg:items-center gap-3 bg-slate-50/50">

            <div class="flex items-center gap-1 overflow-x-auto pb-1 lg:pb-0 font-mono-num">
                <a href="{{ route('super_admin.users.index', array_merge(request()->except(['status', 'page']))) }}" 
                   class="px-3 py-1 rounded-md text-xs font-semibold transition-colors {{ empty($status) ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    Semua Status
                </a>
                <a href="{{ route('super_admin.users.index', array_merge(request()->except(['status', 'page']), ['status' => 'active'])) }}" 
                   class="px-3 py-1 rounded-md text-xs font-semibold transition-colors {{ ($status ?? '') === 'active' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    Aktif ({{ number_format(($totalUsers ?? 0) - ($bannedCount ?? 0), 0, ',', '.') }})
                </a>
                <a href="{{ route('super_admin.users.index', array_merge(request()->except(['status', 'page']), ['status' => 'banned'])) }}" 
                   class="px-3 py-1 rounded-md text-xs font-semibold transition-colors {{ ($status ?? '') === 'banned' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    Banned / Suspend ({{ number_format($bannedCount ?? 0, 0, ',', '.') }})
                </a>
            </div>

            <form action="{{ route('super_admin.users.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-2">
                @if(!empty($status))
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                
                <select name="role" onchange="this.form.submit()" class="w-full sm:w-auto h-8.5 px-2.5 text-xs rounded-lg border border-slate-200 bg-white font-mono-num focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">-- Semua Role --</option>
                    <option value="customer" {{ ($role ?? '') === 'customer' ? 'selected' : '' }}>Customer</option>
                    <option value="seller" {{ ($role ?? '') === 'seller' ? 'selected' : '' }}>Seller Toko</option>
                    <option value="admin" {{ ($role ?? '') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="super_admin" {{ ($role ?? '') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                </select>

                <div class="relative w-full sm:w-60">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, no telp..."
                           class="w-full h-8.5 pl-8 pr-3 text-xs rounded-lg bg-white border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder:text-slate-400 font-mono-num">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 text-xs"></i>
                </div>

                <button type="submit" class="w-full sm:w-auto h-8.5 px-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition-colors shadow-xs">Cari</button>
                @if($search || $role || $status)
                    <a href="{{ route('super_admin.users.index') }}" class="w-full sm:w-auto h-8.5 px-3 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors border border-slate-200">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100 font-mono-num">
                    <tr>
                        <th class="py-3 px-5 font-semibold">ID</th>
                        <th class="py-3 px-5 font-semibold">Profil Pengguna</th>
                        <th class="py-3 px-5 font-semibold">Alamat Email</th>
                        <th class="py-3 px-5 font-semibold">Peran Akun</th>
                        <th class="py-3 px-5 font-semibold text-center">Status Akun</th>
                        <th class="py-3 px-5 font-semibold">Registrasi</th>
                        <th class="py-3 px-5 font-semibold text-center">Aksi Kontrol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/70 transition-colors {{ $user->is_banned ? 'bg-rose-50/20' : '' }}">
                        <td class="py-3.5 px-5 font-mono-num font-semibold text-slate-400">#{{ $user->id }}</td>
                        
                        <td class="py-3.5 px-5">
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0f172a&color=fff&size=60' }}" class="w-8 h-8 rounded-full object-cover shrink-0 border border-slate-200 shadow-2xs" alt="Avatar">
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <h4 class="font-semibold text-slate-900">{{ $user->name }}</h4>
                                        @if($user->id === auth()->id())
                                            <span class="px-1.5 py-0.2 rounded bg-blue-100 text-blue-800 text-[9px] font-bold font-mono-num">Anda</span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-400 font-mono-num truncate max-w-[150px]">
                                        <i class="fa-solid fa-phone text-[9px] mr-1 text-slate-300"></i>{{ $user->phone ?? 'Belum ada no. telp' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="py-3.5 px-5 text-slate-600 font-mono-num">
                            <a href="mailto:{{ $user->email }}" class="hover:text-blue-600 transition-colors">
                                {{ $user->email }}
                            </a>
                        </td>

                        <td class="py-3.5 px-5">
                            @if($user->role === 'super_admin')
                                <span class="px-2 py-0.5 rounded bg-blue-900 text-blue-200 font-bold text-[10px] border border-blue-700 inline-flex items-center gap-1.5 font-mono-num shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span> Super Admin
                                </span>
                            @elseif($user->role === 'admin')
                                <span class="px-2 py-0.5 rounded bg-purple-50 text-purple-700 font-bold text-[10px] border border-purple-200 inline-flex items-center gap-1.5 font-mono-num shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span> Admin
                                </span>
                            @elseif($user->role === 'seller')
                                <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-800 font-bold text-[10px] border border-amber-200 inline-flex items-center gap-1.5 font-mono-num shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Seller
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-bold text-[10px] border border-slate-200 inline-flex items-center gap-1.5 font-mono-num shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Customer
                                </span>
                            @endif
                        </td>

                        <td class="py-3.5 px-5 text-center">
                            @if($user->is_banned)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 font-mono-num">
                                    <i class="fa-solid fa-ban text-[9px]"></i> Disuspend / Banned
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 font-mono-num">
                                    <i class="fa-solid fa-circle-check text-[9px]"></i> Aktif
                                </span>
                            @endif
                        </td>

                        <td class="py-3.5 px-5 text-slate-400 font-mono-num text-[11px]">
                            {{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-' }}
                        </td>

                        <td class="py-3.5 px-5 text-center">
                            @if($user->role !== 'super_admin' && $user->id !== auth()->id())
                                <div class="flex items-center justify-center gap-1.5">

                                    <form action="{{ route('super_admin.users.toggle_ban', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin {{ $user->is_banned ? 'mengaktifkan kembali' : 'memblokir / mensuspend' }} akun pengguna \'{{ $user->name }}\'?')">
                                        @csrf
                                        <button type="submit" 
                                                class="px-2.5 py-1 rounded text-xs font-semibold transition-colors border shadow-2xs cursor-pointer inline-flex items-center gap-1 {{ $user->is_banned ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-amber-50 text-amber-800 border-amber-200 hover:bg-amber-100' }}"
                                                title="{{ $user->is_banned ? 'Aktifkan Akun' : 'Banned / Suspend User' }}">
                                            <i class="fa-solid {{ $user->is_banned ? 'fa-user-check' : 'fa-user-slash' }} text-[10px]"></i>
                                            <span>{{ $user->is_banned ? 'Aktifkan' : 'Banned' }}</span>
                                        </button>
                                    </form>

                                    <form action="{{ route('super_admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin MENGHAPUS PERMANEN akun pengguna \'{{ $user->name }}\' ({{ $user->email }})?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-1.5 rounded bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 shadow-2xs transition-colors cursor-pointer"
                                                title="Hapus Pengguna">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-[10px] text-slate-400 font-mono-num italic">Dilindungi</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <i class="fa-solid fa-users text-2xl text-slate-300 mb-2 block"></i>
                            Tidak ada pengguna yang sesuai kriteria pencarian.
                        </td>
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
