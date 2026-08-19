<x-admin-layout>
    <x-slot name="title">
        Moderasi Produk - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-2">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Moderasi & Kontrol Produk</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pantau dan kelola seluruh etalase produk toko di platform BelanjaIn.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-card border border-slate-200/80 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3 bg-slate-50/50">
            <div>
                <h3 class="font-bold text-xs sm:text-sm text-slate-900 uppercase tracking-wider">Katalog Produk Terdaftar</h3>
                <p class="text-xs text-slate-400 mt-0.5">Filter berdasarkan nama produk, seller, atau ID</p>
            </div>
            <form action="{{ route('admin.products.index') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ $search }}" class="input text-xs pl-8 pr-4 h-8.5 rounded-md w-full sm:w-64" placeholder="Cari produk / toko...">
                <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 text-xs"></i>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 font-semibold">Produk</th>
                        <th class="px-5 py-3.5 font-semibold">Harga & Stok</th>
                        <th class="px-5 py-3.5 font-semibold">Merchant / Toko</th>
                        <th class="px-5 py-3.5 font-semibold">Kategori</th>
                        <th class="px-5 py-3.5 font-semibold text-center">Status</th>
                        <th class="px-5 py-3.5 font-semibold text-center">Aksi Moderasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $product)
                    <tr class="hover:bg-slate-50/80 transition-colors {{ !$product->is_active ? 'bg-rose-50/30' : '' }}">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-md bg-slate-100 border border-slate-200 overflow-hidden shrink-0">
                                    @if($product->image)
                                        <img src="{{ $product->image_url }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-300 text-base">
                                            <i class="fa-solid fa-box"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-900 truncate max-w-[180px]">{{ $product->name }}</p>
                                    <p class="text-[10px] text-slate-400 font-mono mt-0.5">ID: #{{ $product->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="font-bold text-slate-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Stok: {{ $product->stock }} pcs</p>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-1.5">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($product->store->name ?? 'Toko') }}&background=0891b2&color=fff&size=50" class="w-5 h-5 rounded object-cover" alt="Store">
                                <span class="text-slate-700 font-medium truncate max-w-[130px]">{{ $product->store->name ?? 'Toko' }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $product->category->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($product->is_active)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Takedown
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-center">
                                <form action="{{ route('admin.products.toggle_status', $product) }}" method="POST">
                                    @csrf
                                    @if($product->is_active)
                                        <button type="submit" class="px-2.5 py-1 bg-rose-50 text-rose-700 hover:bg-rose-100 rounded-md transition-colors border border-rose-200 text-[11px] font-semibold flex items-center gap-1" title="Nonaktifkan (Takedown)" onclick="return confirm('Anda yakin ingin memblokir/takedown produk ini?')">
                                            <i class="fa-solid fa-ban text-[10px]"></i>
                                            Takedown
                                        </button>
                                    @else
                                        <button type="submit" class="px-2.5 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-md transition-colors border border-emerald-200 text-[11px] font-semibold flex items-center gap-1" title="Aktifkan Kembali" onclick="return confirm('Anda yakin ingin mengaktifkan kembali produk ini?')">
                                            <i class="fa-solid fa-check text-[10px]"></i>
                                            Aktifkan
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                            Tidak ada produk yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 bg-white">
            {{ $products->links('pagination::tailwind') }}
        </div>
    </div>
</x-admin-layout>
