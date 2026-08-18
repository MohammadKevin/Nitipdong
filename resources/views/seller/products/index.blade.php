<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-cyan-50/20">
        <div class="max-w-[1400px] mx-auto px-4 md:px-6 py-8">

            {{-- Header Section --}}
            <div class="mb-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900 mb-2" style="font-family:'Outfit',sans-serif;">
                            📦 Kelola Produk
                        </h1>
                        <p class="text-slate-500">Kelola semua produk tokomu dengan mudah</p>
                    </div>
                    <a href="{{ route('seller.products.create') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-cyan-500 to-blue-500 text-white font-semibold rounded-2xl shadow-lg shadow-cyan-500/30 hover:shadow-xl hover:shadow-cyan-500/40 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Produk Baru
                    </a>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
            <div class="mb-6 flex items-center gap-3 px-5 py-4 rounded-2xl text-sm font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 shadow-sm">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
            @endif

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-500 flex items-center justify-center text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Total Produk</p>
                            <p class="text-2xl font-bold text-slate-900" style="font-family:'Outfit',sans-serif;">{{ $products->total() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 flex items-center justify-center text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Produk Aktif</p>
                            <p class="text-2xl font-bold text-slate-900" style="font-family:'Outfit',sans-serif;">{{ $products->where('is_active', true)->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Stok Rendah</p>
                            <p class="text-2xl font-bold text-slate-900" style="font-family:'Outfit',sans-serif;">{{ $products->where('stock', '<', 10)->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-rose-500 to-pink-500 flex items-center justify-center text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Nilai Stok</p>
                            <p class="text-2xl font-bold text-slate-900" style="font-family:'Outfit',sans-serif;">
                                Rp{{ number_format($products->sum(fn($p) => $p->price * $p->stock) / 1000, 0) }}K
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Products Table --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                {{-- Search & Filter --}}
                <div class="p-6 border-b border-slate-100">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1 relative">
                            <input type="text" placeholder="Cari produk..."
                                class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-cyan-300 focus:ring-2 focus:ring-cyan-100 outline-none transition-all">
                            <svg class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <select class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-cyan-300 focus:ring-2 focus:ring-cyan-100 outline-none transition-all">
                            <option>Semua Kategori</option>
                            <option>Elektronik</option>
                            <option>Fashion</option>
                            <option>Makanan</option>
                        </select>
                        <select class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-cyan-300 focus:ring-2 focus:ring-cyan-100 outline-none transition-all">
                            <option>Semua Status</option>
                            <option>Aktif</option>
                            <option>Nonaktif</option>
                        </select>
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-600 font-medium">
                            <tr class="border-b border-slate-100">
                                <th class="px-6 py-4 w-16">
                                    <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                                </th>
                                <th class="px-6 py-4">Produk</th>
                                <th class="px-6 py-4">Kategori</th>
                                <th class="px-6 py-4">Harga</th>
                                <th class="px-6 py-4">Stok</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($products as $product)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-16 h-16 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900 line-clamp-1">{{ $product->name }}</p>
                                            <p class="text-xs text-slate-400 mt-0.5">SKU: {{ $product->slug }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-3 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700">
                                        {{ $product->category->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-900">
                                    Rp{{ number_format($product->price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-medium {{ $product->stock < 10 ? 'text-rose-600' : 'text-slate-700' }}">
                                        {{ $product->stock }} unit
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($product->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('seller.products.edit', $product) }}"
                                            class="p-2 text-cyan-600 hover:bg-cyan-50 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('seller.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-20 h-20 rounded-3xl bg-slate-100 flex items-center justify-center mb-4">
                                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                        </div>
                                        <h3 class="font-bold text-slate-700 text-lg mb-2" style="font-family:'Outfit',sans-serif;">
                                            Belum ada produk
                                        </h3>
                                        <p class="text-slate-400 text-sm mb-6">Mulai tambahkan produk pertamamu sekarang!</p>
                                        <a href="{{ route('seller.products.create') }}"
                                            class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-cyan-500 to-blue-500 text-white font-semibold rounded-xl shadow-lg shadow-cyan-500/30 hover:shadow-xl transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Tambah Produk
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($products->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $products->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
