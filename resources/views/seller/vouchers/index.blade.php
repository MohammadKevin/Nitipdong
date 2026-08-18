<x-seller-layout>
    <x-slot name="title">
        Voucher Promosi Toko - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-2">
        <div>
            <h1 class="text-xl font-bold text-[#14213D] flex items-center gap-2" style="font-family:'Poppins',sans-serif;">
                <i class="fa-solid fa-ticket text-[#12A57F]"></i>
                Voucher Promosi Toko
            </h1>
            <p class="text-xs text-[#8A93A6] mt-0.5">Kelola kupon diskon khusus untuk menarik pembeli berbelanja di toko Anda.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('seller.vouchers.create') }}" class="inline-flex items-center gap-2 bg-[#12A57F] hover:bg-[#0f8b6a] text-white px-4 py-2.5 rounded-xl font-semibold text-xs transition-all shadow-md shadow-[#12A57F]/20">
                <i class="fa-solid fa-plus"></i>
                Buat Voucher Baru
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-[#EFEBDF] relative overflow-hidden">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-[#12A57F] flex items-center justify-center text-xl">
                    <i class="fa-solid fa-ticket"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400">Total Voucher Toko</p>
                    <h4 class="text-2xl font-bold text-slate-800 font-display mt-0.5">{{ $vouchers->total() }}</h4>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-[#EFEBDF] relative overflow-hidden">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400">Voucher Aktif</p>
                    <h4 class="text-2xl font-bold text-slate-800 font-display mt-0.5">{{ $vouchers->where('is_active', true)->count() }}</h4>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-[#EFEBDF] relative overflow-hidden">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400">Total Kuota Tersedia</p>
                    <h4 class="text-2xl font-bold text-slate-800 font-display mt-0.5">{{ $vouchers->sum('quota') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-[#EFEBDF] overflow-hidden">
        <div class="p-5 border-b border-[#F0EEE6] flex flex-col sm:flex-row justify-between sm:items-center gap-4 bg-slate-50/50">
            <h3 class="font-bold text-sm text-[#14213D]" style="font-family:'Poppins',sans-serif;">Daftar Voucher Toko</h3>
            <form method="GET" action="{{ route('seller.vouchers.index') }}" class="flex items-center gap-2">
                <div class="relative">
                    <input type="text" name="q" value="{{ request('q') }}" class="bg-white border border-[#E7E3D8] rounded-xl pl-10 pr-4 py-2 text-xs focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] shadow-sm w-full sm:w-60" placeholder="Cari kode promo...">
                    <button type="submit" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#12A57F]">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-[#FAF9F5] text-[#8A93A6] font-semibold border-b border-[#F0EEE6]">
                    <tr>
                        <th class="px-6 py-3.5">Kode & Nama Promo</th>
                        <th class="px-6 py-3.5">Potongan Diskon</th>
                        <th class="px-6 py-3.5">Minimal Belanja</th>
                        <th class="px-6 py-3.5 text-center">Sisa Kuota</th>
                        <th class="px-6 py-3.5">Periode Berlaku</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F5F3EE]">
                    @forelse ($vouchers as $voucher)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1.5 rounded-xl font-mono font-bold text-xs bg-emerald-50 text-[#12A57F] border border-emerald-200">
                                    {{ $voucher->code }}
                                </span>
                                <div>
                                    <p class="font-bold text-slate-800 text-xs">{{ $voucher->name }}</p>
                                    <p class="text-[10px] text-slate-400 line-clamp-1 max-w-xs">{{ $voucher->description ?? 'Promo Toko' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($voucher->type === 'percent')
                                <span class="font-bold text-slate-800 text-sm">Diskon {{ $voucher->amount }}%</span>
                                @if($voucher->max_discount)
                                    <span class="block text-[10px] text-slate-400">Maks. Rp {{ number_format($voucher->max_discount, 0, ',', '.') }}</span>
                                @endif
                            @else
                                <span class="font-bold text-slate-800 text-sm">Rp {{ number_format($voucher->amount, 0, ',', '.') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600">
                            @if($voucher->min_spend > 0)
                                Rp {{ number_format($voucher->min_spend, 0, ',', '.') }}
                            @else
                                <span class="text-slate-400">Tanpa Min. Belanja</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center font-semibold text-slate-700">
                            {{ $voucher->quota }} Klaim
                        </td>
                        <td class="px-6 py-4 text-slate-500">
                            <div class="flex flex-col text-[11px]">
                                <span>{{ $voucher->start_date ? $voucher->start_date->format('d M Y') : 'Sekarang' }}</span>
                                <span class="text-[10px] text-slate-400">s/d {{ $voucher->end_date ? $voucher->end_date->format('d M Y') : 'Selamanya' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('seller.vouchers.toggle', $voucher) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $voucher->is_active ? 'bg-[#12A57F]' : 'bg-slate-200' }}" title="Toggle Status Aktif">
                                    <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $voucher->is_active ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('seller.vouchers.edit', $voucher) }}" class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg transition-colors shadow-sm" title="Edit Voucher">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <form action="{{ route('seller.vouchers.destroy', $voucher) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus voucher \'{{ $voucher->code }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-lg transition-colors shadow-sm" title="Hapus Voucher">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-400 mb-3 text-2xl">
                                    <i class="fa-solid fa-ticket"></i>
                                </div>
                                <p class="font-medium text-slate-600 text-sm">Belum ada voucher toko</p>
                                <p class="text-xs text-slate-400 mt-1">Buat voucher promo untuk meningkatkan penjualan toko Anda.</p>
                                <a href="{{ route('seller.vouchers.create') }}" class="mt-4 inline-flex items-center gap-2 bg-[#12A57F] text-white px-4 py-2 rounded-xl text-xs font-semibold hover:bg-[#0f8b6a] transition-all">
                                    <i class="fa-solid fa-plus"></i>
                                    Buat Voucher Sekarang
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-[#F0EEE6]">
            {{ $vouchers->links('pagination::tailwind') }}
        </div>
    </div>
</x-seller-layout>
