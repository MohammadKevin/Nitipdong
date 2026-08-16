<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Dashboard Seller - ') }} {{ $store->name ?? 'Toko Saya' }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Ringkasan Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xl">
                        📦
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total Produk</p>
                        <h4 class="text-2xl font-bold text-slate-800">{{ $products->count() }}</h4>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xl">
                        🛒
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total Pesanan</p>
                        <h4 class="text-2xl font-bold text-slate-800">0</h4>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xl">
                        ⭐
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Rating Toko</p>
                        <h4 class="text-2xl font-bold text-slate-800">5.0 / 5.0</h4>
                    </div>
                </div>
            </div>

            <!-- Daftar Produk Toko -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-bold text-slate-800">Daftar Produk Toko</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-slate-500 font-medium border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4">Nama Produk</th>
                                <th class="px-6 py-4">Kategori</th>
                                <th class="px-6 py-4">Harga</th>
                                <th class="px-6 py-4">Stok</th>
                                <th class="px-6 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($products as $product)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-slate-800">{{ $product->name }}</td>
                                    <td class="px-6 py-4"><span class="px-2.5 py-1 bg-slate-100 text-slate-700 text-xs rounded-lg">{{ $product->category->name }}</span></td>
                                    <td class="px-6 py-4 font-medium text-slate-900">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">{{ $product->stock }} unit</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">Aktif</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                        Belum ada produk di toko ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>