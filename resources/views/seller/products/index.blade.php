<x-seller-layout>
    <x-slot name="title">
        Katalog Produk Toko - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-2">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-cyan-700"></i>
                Katalog Produk Toko
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola seluruh produk, atur harga, dan pantau ketersediaan stok barang.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('seller.products.create') }}" class="btn-primary text-xs h-9 px-4 rounded-md bg-cyan-700 hover:bg-cyan-800 flex items-center gap-1.5 shadow-xs">
                <i class="fa-solid fa-plus text-[10px]"></i>
                Tambah Produk
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-card border border-slate-200/80 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3 bg-slate-50/50">
            <form method="GET" action="{{ route('seller.products.index') }}" class="flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                <div class="relative flex-1 max-w-sm">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama produk..."
                        class="input text-xs pl-8 pr-4 h-8.5 rounded-md w-full">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 text-xs"></i>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="btn-secondary text-xs h-8.5 px-3 rounded-md">
                        Filter
                    </button>
                    @if(request('search'))
                        <a href="{{ route('seller.products.index') }}" class="text-xs text-slate-500 hover:text-slate-800 px-2 py-1">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
            <div class="text-xs text-slate-500">
                Total: <span class="font-bold text-slate-800">{{ $products->total() }}</span> Produk
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5">Produk</th>
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5">Harga Dasar Toko</th>
                        <th class="px-5 py-3.5">Harga Tayang Konsumen (+5%)</th>
                        <th class="px-5 py-3.5 text-center">Stok</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($products as $product)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-10 h-10 rounded-md bg-slate-100 border border-slate-200 overflow-hidden shrink-0">
                                    @if($product->image)
                                        <img src="{{ $product->image_url }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-400 text-sm">
                                            <i class="fa-solid fa-box"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('product.show', $product) }}" target="_blank" class="font-bold text-slate-900 text-xs hover:text-cyan-700 transition-colors truncate block max-w-xs">
                                        {{ $product->name }}
                                    </a>
                                    <span class="text-[10px] text-slate-400 font-mono">Kode: {{ $product->getRouteKey() }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $product->category->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="font-bold text-slate-900">Rp {{ number_format($product->seller_price, 0, ',', '.') }}</span>
                            <span class="block text-[10px] text-slate-400">100% diterima toko</span>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($product->discount_percentage > 0)
                                <div class="flex items-baseline gap-1.5">
                                    <span class="font-bold text-cyan-800">Rp {{ number_format($product->final_price, 0, ',', '.') }}</span>
                                    <span class="text-[10px] text-slate-400 line-through">Rp {{ number_format($product->customer_base_price, 0, ',', '.') }}</span>
                                </div>
                                <span class="inline-block text-[9px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-1 rounded mt-0.5">Diskon {{ $product->discount_percentage }}%</span>
                            @else
                                <span class="font-bold text-cyan-800">Rp {{ number_format($product->customer_base_price, 0, ',', '.') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $product->stock > 5 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                {{ $product->stock }} Unit
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $product->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $product->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('seller.products.edit', $product) }}" class="p-1.5 bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 text-slate-600 rounded-md transition-colors border border-slate-200" title="Edit Produk">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <form action="{{ route('seller.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 rounded-md transition-colors border border-slate-200" title="Hapus Produk">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                            Belum ada produk di katalog toko Anda.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-seller-layout>
