<x-seller-layout>
    <x-slot name="title">
        Voucher Promosi Toko - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-2">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-ticket text-cyan-700"></i>
                Voucher Promosi Toko
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola kupon diskon khusus untuk menarik pembeli berbelanja di toko Anda.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('seller.vouchers.create') }}" class="btn-primary text-xs h-9 px-4 rounded-md bg-cyan-700 hover:bg-cyan-800 flex items-center gap-1.5">
                <i class="fa-solid fa-plus text-xs"></i>
                Buat Voucher Baru
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-card flex items-center gap-4">
            <div class="w-11 h-11 rounded-lg bg-cyan-50 text-cyan-700 flex items-center justify-center text-lg border border-cyan-200 shrink-0">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Total Voucher Toko</p>
                <h4 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $vouchers->total() }} Kupon</h4>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-card flex items-center gap-4">
            <div class="w-11 h-11 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg border border-emerald-200 shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Voucher Aktif</p>
                <h4 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $vouchers->where('is_active', true)->count() }} Aktif</h4>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-card flex items-center gap-4">
            <div class="w-11 h-11 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center text-lg border border-amber-200 shrink-0">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Total Kuota Tersedia</p>
                <h4 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $vouchers->sum('quota') }} Klaim</h4>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-card border border-slate-200/80 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3 bg-slate-50/50">
            <div>
                <h3 class="font-bold text-xs sm:text-sm text-slate-900 uppercase tracking-wider">Daftar Voucher Toko</h3>
                <p class="text-xs text-slate-400 mt-0.5">Kupon promo yang dapat diklaim pembeli pada proses checkout</p>
            </div>
            <form method="GET" action="{{ route('seller.vouchers.index') }}" class="flex items-center gap-2">
                <div class="relative">
                    <input type="text" name="q" value="{{ request('q') }}" class="input text-xs pl-8 pr-4 h-8.5 rounded-md w-full sm:w-60" placeholder="Cari kode promo...">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 text-xs"></i>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 font-semibold">Kode & Nama Promo</th>
                        <th class="px-5 py-3.5 font-semibold">Potongan Diskon</th>
                        <th class="px-5 py-3.5 font-semibold">Minimal Belanja</th>
                        <th class="px-5 py-3.5 font-semibold text-center">Sisa Kuota</th>
                        <th class="px-5 py-3.5 font-semibold">Periode Berlaku</th>
                        <th class="px-5 py-3.5 font-semibold text-center">Status</th>
                        <th class="px-5 py-3.5 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($vouchers as $voucher)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <span class="px-2.5 py-1 rounded-md font-mono font-bold text-xs bg-cyan-50 text-cyan-800 border border-cyan-200">
                                    {{ $voucher->code }}
                                </span>
                                <div>
                                    <p class="font-bold text-slate-900 text-xs">{{ $voucher->name }}</p>
                                    <p class="text-[10px] text-slate-400 line-clamp-1 max-w-xs">{{ $voucher->description ?? 'Promo Toko' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 font-bold text-cyan-800">
                            @if($voucher->type === 'percentage')
                                Diskon {{ $voucher->value }}%
                                @if($voucher->max_discount)
                                    <span class="block text-[10px] font-normal text-slate-400">Maks. Rp {{ number_format($voucher->max_discount, 0, ',', '.') }}</span>
                                @endif
                            @else
                                Potongan Rp {{ number_format($voucher->value, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-slate-700">
                            Rp {{ number_format($voucher->min_spend, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3.5 text-center font-bold text-slate-900">
                            {{ $voucher->quota }}
                        </td>
                        <td class="px-5 py-3.5 text-slate-500">
                            <p class="text-slate-800 font-medium">{{ $voucher->start_date ? $voucher->start_date->translatedFormat('d M Y') : '-' }}</p>
                            <p class="text-[10px] text-slate-400">s/d {{ $voucher->end_date ? $voucher->end_date->translatedFormat('d M Y') : 'Selamanya' }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $voucher->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $voucher->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                {{ $voucher->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('seller.vouchers.edit', $voucher) }}" class="p-1.5 bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 text-slate-600 rounded-md transition-colors border border-slate-200" title="Edit Voucher">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <form action="{{ route('seller.vouchers.destroy', $voucher) }}" method="POST" onsubmit="return confirm('Hapus voucher \'{{ $voucher->code }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 rounded-md transition-colors border border-slate-200" title="Hapus Voucher">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                            Belum ada voucher promo toko.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 bg-white">
            {{ $vouchers->links('pagination::tailwind') }}
        </div>
    </div>
</x-seller-layout>
