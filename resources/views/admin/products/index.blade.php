<x-admin-layout>
    <x-slot name="title">
        Moderasi Produk - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-2">
        <div>
            <h1 class="text-xl font-bold text-[#14213D]" style="font-family:'Poppins',sans-serif;">Moderasi Produk</h1>
            <p class="text-xs text-[#8A93A6] mt-0.5">Pantau dan kelola seluruh produk yang beredar di platform.</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-4 bg-slate-50/50">
            <h3 class="font-bold text-slate-800">Daftar Semua Produk</h3>
            <form action="{{ route('admin.products.index') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ $search }}" class="bg-white border border-slate-200 rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500 shadow-sm w-full sm:w-64" placeholder="Cari nama produk / toko...">
                <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-emerald-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 font-medium border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Produk</th>
                        <th class="px-6 py-4">Harga & Stok</th>
                        <th class="px-6 py-4">Nama Toko</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi Moderasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $product)
                    <tr class="hover:bg-slate-50/80 transition-colors {{ !$product->is_active ? 'bg-rose-50/30' : '' }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 overflow-hidden shrink-0">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-800 truncate max-w-[150px]">{{ $product->name }}</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">ID: #{{ $product->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-slate-800">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Stok: {{ $product->stock }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-5 h-5 rounded bg-slate-200 shrink-0 overflow-hidden">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($product->store->name) }}&background=random" class="w-full h-full object-cover" alt="Store">
                                </div>
                                <p class="text-xs font-medium text-slate-700 truncate max-w-[120px]" title="{{ $product->store->name }}">{{ $product->store->name }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-medium bg-slate-100 text-slate-600">
                                {{ $product->category->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($product->is_active)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Dinonaktifkan
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center">
                                <form action="{{ route('admin.products.toggle_status', $product) }}" method="POST">
                                    @csrf
                                    @if($product->is_active)
                                        <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-lg transition-colors shadow-sm text-xs font-semibold" title="Nonaktifkan (Takedown)" onclick="return confirm('Anda yakin ingin memblokir/takedown produk ini?')">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                            Takedown
                                        </button>
                                    @else
                                        <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-lg transition-colors shadow-sm text-xs font-semibold" title="Aktifkan Kembali" onclick="return confirm('Anda yakin ingin mengaktifkan kembali produk ini?')">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Aktifkan
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                <p>Tidak ada data produk yang ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-slate-100">
            {{ $products->links('pagination::tailwind') }}
        </div>
    </div>
</x-admin-layout>
