<x-admin-layout>
    <x-slot name="title">
        Voucher &amp; Promo Platform - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Voucher &amp; Promo Platform
    </x-slot>

    @php
        $isSuperAdmin = auth()->user()->role === 'super_admin';
        $createRoute = $isSuperAdmin ? route('super_admin.vouchers.create') : route('admin.vouchers.create');
        $indexRoute = $isSuperAdmin ? route('super_admin.vouchers.index') : route('admin.vouchers.index');
    @endphp

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 pb-1">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                    Voucher &amp; Kupon Promo
                </h1>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                    Promosi Platform
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Kelola kupon diskon platform dan pantau seluruh voucher promosi di marketplace NitipDong.</p>
        </div>
        
        <div class="flex items-center gap-3 self-start sm:self-auto">
            <a href="{{ $createRoute }}" class="h-9 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs flex items-center gap-2 shadow-xs transition-colors shrink-0 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                <span>Buat Voucher Baru</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center gap-2.5 shadow-2xs animate-fade-up">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="flex-1">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 border border-blue-200/70 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Total Voucher Terdaftar</p>
                <h4 class="text-xl font-black text-slate-900 mt-0.5">{{ $vouchers->total() }} Voucher</h4>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200/70 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Voucher Aktif</p>
                <h4 class="text-xl font-black text-slate-900 mt-0.5">{{ $vouchers->where('is_active', true)->count() }} Aktif</h4>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 border border-amber-200/70 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Total Kuota Tersedia</p>
                <h4 class="text-xl font-black text-slate-900 mt-0.5">{{ number_format($vouchers->sum('quota'), 0, ',', '.') }} Klaim</h4>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/90 overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3 bg-slate-50/50">
            <div>
                <h3 class="font-bold text-xs sm:text-sm text-slate-900 uppercase tracking-wider">Daftar Voucher &amp; Promo</h3>
                <p class="text-xs text-slate-400 mt-0.5">Filter dan pantau voucher promo aktif untuk pembeli</p>
            </div>

            <form action="{{ $indexRoute }}" method="GET" class="flex flex-wrap items-center gap-2">
                <select name="type_filter" onchange="this.form.submit()" class="h-9 px-3 text-xs bg-white border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-slate-700 cursor-pointer">
                    <option value="">Semua Tipe (Platform &amp; Toko)</option>
                    <option value="platform" {{ request('type_filter') === 'platform' ? 'selected' : '' }}>Khusus Platform NitipDong</option>
                    <option value="store" {{ request('type_filter') === 'store' ? 'selected' : '' }}>Voucher Toko / Merchant</option>
                </select>

                <div class="relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode atau nama..." class="h-9 pl-8 pr-3 text-xs bg-white border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-slate-800 w-44 sm:w-56">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </div>
            </form>
        </div>

        @if($vouchers->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 border border-blue-200/70 mx-auto flex items-center justify-center text-2xl mb-4">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-800">Belum Ada Voucher</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Voucher promo masih 0. Klik tombol di bawah untuk membuat voucher diskon baru (contoh: Diskon 20% Min 500k Pengguna Baru).</p>
                <a href="{{ $createRoute }}" class="inline-flex items-center gap-1.5 h-9 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs mt-4 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Buat Voucher Baru
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">Voucher</th>
                            <th class="py-3 px-4">Tipe &amp; Penerbit</th>
                            <th class="py-3 px-4">Diskon / Potongan</th>
                            <th class="py-3 px-4">Min. Belanja</th>
                            <th class="py-3 px-4">Sisa Kuota</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-600">
                        @foreach($vouchers as $voucher)
                            @php
                                $editRoute = $isSuperAdmin ? route('super_admin.vouchers.edit', $voucher) : route('admin.vouchers.edit', $voucher);
                                $toggleRoute = $isSuperAdmin ? route('super_admin.vouchers.toggle', $voucher) : route('admin.vouchers.toggle', $voucher);
                                $deleteRoute = $isSuperAdmin ? route('super_admin.vouchers.destroy', $voucher) : route('admin.vouchers.destroy', $voucher);
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 font-mono font-bold text-xs border border-blue-200">
                                            {{ $voucher->code }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-900 truncate">{{ $voucher->name }}</p>
                                            @if($voucher->expires_at)
                                                <p class="text-[10px] text-slate-400">Exp: {{ $voucher->expires_at->format('d M Y') }}</p>
                                            @else
                                                <p class="text-[10px] text-emerald-600 font-semibold">Tanpa Batas Waktu</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($voucher->store)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-cyan-50 text-cyan-700 border border-cyan-200">
                                            Toko: {{ $voucher->store->name }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Platform NitipDong
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-extrabold text-slate-900">
                                        @if($voucher->type === 'percent')
                                            {{ $voucher->amount }}%
                                            @if($voucher->max_discount)
                                                <span class="text-[10px] text-slate-400 font-normal block">(Maks. Rp {{ number_format($voucher->max_discount, 0, ',', '.') }})</span>
                                            @endif
                                        @else
                                            Rp {{ number_format($voucher->amount, 0, ',', '.') }}
                                        @endif
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-700 font-medium">
                                    {{ $voucher->min_spend > 0 ? 'Rp ' . number_format($voucher->min_spend, 0, ',', '.') : 'Tanpa Min.' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold {{ $voucher->quota > 0 ? 'text-slate-800' : 'text-rose-600' }}">
                                        {{ number_format($voucher->quota, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <form action="{{ $toggleRoute }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-bold border transition-all cursor-pointer {{ $voucher->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200' }}">
                                            {{ $voucher->is_active ? '● Aktif' : '○ Nonaktif' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ $editRoute }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 flex items-center justify-center transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>
                                        <form action="{{ $deleteRoute }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus voucher {{ $voucher->code }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 flex items-center justify-center transition-colors cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($vouchers->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $vouchers->links() }}
                </div>
            @endif
        @endif
    </div>
</x-admin-layout>
