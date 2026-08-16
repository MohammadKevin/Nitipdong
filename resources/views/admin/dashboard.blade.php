<x-admin-layout>
    <x-slot name="title">
        Admin Dashboard - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    {{-- Top bar --}}
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-2">
        <div>
            <h1 class="text-xl font-bold text-[#14213D]" style="font-family:'Poppins',sans-serif;">Selamat datang kembali, {{ explode(' ', auth()->user()->name)[0] }} 👋</h1>
            <p class="text-xs text-[#8A93A6] mt-0.5">Kelola dan tinjau pengajuan toko baru dari pengguna.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=12A57F&color=fff" class="w-10 h-10 rounded-xl border border-[#E7E3D8] object-cover shrink-0" alt="User">
        </div>
    </div>

            <!-- Metrics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Total Pending -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 relative overflow-hidden">
                    <div class="absolute right-0 bottom-0 opacity-5 transform translate-x-1/4 translate-y-1/4">
                        <svg class="w-32 h-32 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    </div>
                    <div class="flex items-center gap-4 mb-2">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500">Menunggu Review</p>
                            <h4 class="text-2xl font-bold text-slate-800">{{ number_format($pendingCount, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Total Disetujui (Bulan Ini) -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 relative overflow-hidden">
                    <div class="absolute right-0 bottom-0 opacity-5 transform translate-x-1/4 translate-y-1/4">
                        <svg class="w-32 h-32 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    </div>
                    <div class="flex items-center gap-4 mb-2">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500">Toko Disetujui</p>
                            <h4 class="text-2xl font-bold text-slate-800">{{ number_format($approvedCount, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Total Ditolak -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 relative overflow-hidden">
                    <div class="absolute right-0 bottom-0 opacity-5 transform translate-x-1/4 translate-y-1/4">
                        <svg class="w-32 h-32 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    </div>
                    <div class="flex items-center gap-4 mb-2">
                        <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500">Pengajuan Ditolak</p>
                            <h4 class="text-2xl font-bold text-slate-800">{{ number_format($rejectedCount, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Review Table -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-bold text-slate-800">Daftar Pengajuan Toko (Pending)</h3>
                    <form action="{{ route('admin.dashboard') }}" method="GET" class="relative">
                        <input type="text" name="search" value="{{ $search }}" class="bg-white border border-slate-200 rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500 shadow-sm w-64" placeholder="Cari nama toko...">
                        <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-emerald-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </form>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-slate-500 font-medium border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4">Nama Toko</th>
                                <th class="px-6 py-4">Pengaju (User)</th>
                                <th class="px-6 py-4">Deskripsi</th>
                                <th class="px-6 py-4 text-center">Aksi Cepat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($pendingStores as $store)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 text-slate-500">{{ $store->created_at->translatedFormat('d M Y') }}<br><span class="text-xs">{{ $store->created_at->format('H:i') }} WIB</span></td>
                                <td class="px-6 py-4 font-medium text-slate-800">{{ $store->name }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-200 shrink-0">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($store->user->name) }}&background=random" class="rounded-full" alt="User">
                                        </div>
                                        {{ $store->user->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500 truncate max-w-xs" title="{{ $store->description }}">
                                    {{ Str::limit($store->description, 50) }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <form action="{{ route('admin.stores.approve', $store) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="p-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-lg transition-colors shadow-sm" title="Setujui" onclick="return confirm('Apakah Anda yakin ingin menyetujui toko ini?')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.stores.reject', $store) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="p-2 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-lg transition-colors shadow-sm" title="Tolak" onclick="return confirm('Apakah Anda yakin ingin menolak toko ini?')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                    Tidak ada pengajuan toko yang menunggu review.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="p-4 border-t border-slate-100">
                    {{ $pendingStores->links('pagination::tailwind') }}
                </div>
            </div>

</x-admin-layout>
