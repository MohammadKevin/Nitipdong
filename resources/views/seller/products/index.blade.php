<x-seller-layout>
    <x-slot name="title">
        Katalog Produk Toko - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-2">
        <div>
            <h1 class="text-xl font-bold text-[#14213D] flex items-center gap-2" style="font-family:'Poppins',sans-serif;">
                <i class="fa-solid fa-boxes-stacked text-[#12A57F]"></i>
                Katalog Produk Toko
            </h1>
            <p class="text-xs text-[#8A93A6] mt-0.5">Kelola daftar produk, sesuaikan stok, ubah harga, dan buat diskon promosi.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('seller.products.create') }}" class="inline-flex items-center gap-2 bg-[#12A57F] hover:bg-[#0f8b6a] text-white px-4 py-2.5 rounded-xl font-semibold text-xs transition-all shadow-md shadow-[#12A57F]/20">
                <i class="fa-solid fa-plus"></i>
                Tambah Produk Baru
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-[#EFEBDF] relative overflow-hidden">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-[#12A57F] flex items-center justify-center text-xl">
                    <i class="fa-solid fa-box"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400">Total Produk</p>
                    <h4 class="text-2xl font-bold text-slate-800 font-display mt-0.5">{{ $products->total() }}</h4>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-[#EFEBDF] relative overflow-hidden">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400">Produk Aktif</p>
                    <h4 class="text-2xl font-bold text-slate-800 font-display mt-0.5">{{ $products->where('is_active', true)->count() }}</h4>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-[#EFEBDF] relative overflow-hidden">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400">Total Unit Stok</p>
                    <h4 class="text-2xl font-bold text-slate-800 font-display mt-0.5">{{ $products->sum('stock') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-[#EFEBDF] overflow-hidden">
        <div class="p-5 border-b border-[#F0EEE6] flex flex-col sm:flex-row justify-between sm:items-center gap-4 bg-slate-50/50">
            <h3 class="font-bold text-sm text-[#14213D]" style="font-family:'Poppins',sans-serif;">Daftar Produk ({{ $products->total() }})</h3>
            <form method="GET" action="{{ route('seller.products.index') }}" class="relative">
                <input type="text" name="search" value="{{ request('search') }}" class="bg-white border border-[#E7E3D8] rounded-xl pl-10 pr-4 py-2 text-xs focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] shadow-sm w-full sm:w-64" placeholder="Cari nama produk...">
                <button type="submit" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#12A57F]">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </button>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-[#FAF9F5] text-[#8A93A6] font-semibold border-b border-[#F0EEE6]">
                    <tr>
                        <th class="px-6 py-3.5">Produk</th>
                        <th class="px-6 py-3.5">Kategori</th>
                        <th class="px-6 py-3.5">Harga Jual</th>
                        <th class="px-6 py-3.5 text-center">Stok</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F5F3EE]">
                    @forelse ($products as $product)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-400 text-lg">
                                            <i class="fa-solid fa-box"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('product.show', $product) }}" target="_blank" class="font-bold text-slate-800 text-xs hover:text-[#12A57F] transition-colors truncate block max-w-xs">
                                        {{ $product->name }}
                                    </a>
                                    <span class="text-[10px] text-slate-400">ID: #{{ $product->id }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-medium bg-slate-100 text-slate-700">
                                <i class="fa-solid fa-tag text-[10px] text-slate-400"></i>
                                {{ $product->category->name ?? 'Tanpa Kategori' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-800 text-sm">
                                    Rp {{ number_format($product->final_price, 0, ',', '.') }}
                                </span>
                                @if($product->has_discount)
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="text-[10px] text-slate-400 line-through">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </span>
                                        <span class="text-[9px] font-bold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded">
                                            -{{ $product->discount_percentage_effective }}%
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($product->stock > 10)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $product->stock }} Pcs
                                </span>
                            @elseif($product->stock > 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    {{ $product->stock }} Pcs (Menipis)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    Habis
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($product->is_active)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('product.show', $product) }}" target="_blank" class="p-2 bg-slate-100 text-slate-600 hover:bg-[#12A57F] hover:text-white rounded-lg transition-colors shadow-sm" title="Lihat di Storefront">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('seller.products.edit', $product) }}" class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg transition-colors shadow-sm" title="Edit Produk">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <form action="{{ route('seller.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk \'{{ $product->name }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-lg transition-colors shadow-sm" title="Hapus Produk">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-400 mb-3 text-2xl">
                                    <i class="fa-solid fa-boxes-stacked"></i>
                                </div>
                                <p class="font-medium text-slate-600 text-sm">Belum ada produk di etalase Anda</p>
                                <p class="text-xs text-slate-400 mt-1">Mulai tambahkan produk pertama untuk berjualan di BelanjaIn.</p>
                                <a href="{{ route('seller.products.create') }}" class="mt-4 inline-flex items-center gap-2 bg-[#12A57F] text-white px-4 py-2 rounded-xl text-xs font-semibold hover:bg-[#0f8b6a] transition-all">
                                    <i class="fa-solid fa-plus"></i>
                                    Tambah Produk Sekarang
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-[#F0EEE6]">
            {{ $products->links('pagination::tailwind') }}
        </div>
    </div>
</x-seller-layout>
