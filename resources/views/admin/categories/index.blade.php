<x-admin-layout>
    <x-slot name="title">
        Kelola Kategori Produk - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Kategori Produk
    </x-slot>

    <!-- HEADER / ACTION BAR -->
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 pb-1">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                Kelola Kategori Produk
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola seluruh kategori produk untuk klasifikasi storefront dan navigasi pembeli.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.categories.create') : route('admin.categories.create') }}" class="h-9 px-3.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs flex items-center gap-1.5 shadow-xs transition-colors shrink-0 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                <span>Tambah Kategori</span>
            </a>
        </div>
    </div>

    <!-- 3 SUMMARY CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-200/70 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Total Kategori</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 truncate">{{ number_format($totalCategories, 0, ',', '.') }} Kategori</h4>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200/70 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Kategori Aktif (Ada Produk)</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 truncate">{{ number_format($categoriesWithProducts, 0, ',', '.') }} Kategori</h4>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-200/70 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Kategori Kosong</p>
                <h4 class="text-xl font-bold text-slate-900 mt-0.5 truncate">{{ number_format($totalCategories - $categoriesWithProducts, 0, ',', '.') }} Kategori</h4>
            </div>
        </div>
    </div>

    <!-- CATEGORIES TABLE -->
    <div class="bg-white rounded-xl shadow-xs border border-slate-200/90 overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3 bg-slate-50/50">
            <div>
                <h3 class="font-bold text-sm text-slate-900 uppercase tracking-wider">Daftar Semua Kategori</h3>
                <p class="text-xs text-slate-400 mt-0.5">Klasifikasi produk yang aktif di marketplace NitipDong</p>
            </div>
            <form action="{{ auth()->user()->role === 'super_admin' ? route('super_admin.categories.index') : route('admin.categories.index') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ $search }}" class="w-full sm:w-64 h-9 pl-9 pr-3 text-xs rounded-lg border border-slate-200 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder:text-slate-400" placeholder="Cari nama atau slug...">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[11px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5">Ikon</th>
                        <th class="px-5 py-3.5">Nama Kategori</th>
                        <th class="px-5 py-3.5">Slug URL</th>
                        <th class="px-5 py-3.5 text-center">Jumlah Produk</th>
                        <th class="px-5 py-3.5">Tanggal Dibuat</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $category)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-5 py-4">
                            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 border border-blue-200/60 flex items-center justify-center text-sm shadow-2xs">
                                <i class="{{ $category->icon ?: 'fa-solid fa-tag' }}"></i>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="font-bold text-slate-900 text-xs block">{{ $category->name }}</span>
                            <span class="text-[11px] text-slate-400">ID: #{{ $category->id }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-xs text-slate-600 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200 inline-block max-w-[200px] truncate" title="/products?category={{ $category->slug }}">
                                /products?category={{ $category->slug }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $category->products_count > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                                </svg>
                                {{ $category->products_count }} Produk
                            </span>
                        </td>
                        <td class="px-5 py-4 text-slate-500 text-xs">
                            {{ $category->created_at ? $category->created_at->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="px-5 py-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.categories.edit', $category) : route('admin.categories.edit', $category) }}" class="p-2 bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-slate-600 rounded-lg transition-colors border border-slate-200 shadow-2xs" title="Edit Kategori">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                                    </svg>
                                </a>
                                <form action="{{ auth()->user()->role === 'super_admin' ? route('super_admin.categories.destroy', $category) : route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori \'{{ $category->name }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 rounded-lg transition-colors border border-slate-200 shadow-2xs cursor-pointer" title="Hapus Kategori">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-14 text-center text-slate-400">
                            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-semibold text-slate-700 block">Tidak Ada Kategori Ditemukan</span>
                            <p class="text-xs text-slate-400 mt-1">Belum ada kategori produk yang ditambahkan ke sistem.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($categories->hasPages())
        <div class="p-4 border-t border-slate-100 bg-white">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
</x-admin-layout>
