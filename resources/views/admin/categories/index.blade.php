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
            <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.categories.create') : route('admin.categories.create') }}" class="h-8.5 px-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs flex items-center gap-1.5 shadow-xs transition-colors shrink-0">
                <i class="fa-solid fa-plus text-[10px]"></i>
                <span>Tambah Kategori</span>
            </a>
        </div>
    </div>

    <!-- 3 SUMMARY CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-4">
        <div class="bg-white rounded-lg p-4 sm:p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-700 border border-blue-200/60 flex items-center justify-center text-base shrink-0 font-mono-num">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono-num truncate">Total Kategori</p>
                <h4 class="text-lg sm:text-xl font-bold text-slate-900 mt-0.5 font-mono-num truncate">{{ number_format($totalCategories, 0, ',', '.') }} Kategori</h4>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 sm:p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/60 flex items-center justify-center text-base shrink-0 font-mono-num">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono-num truncate">Kategori Aktif (Ada Produk)</p>
                <h4 class="text-lg sm:text-xl font-bold text-slate-900 mt-0.5 font-mono-num truncate">{{ number_format($categoriesWithProducts, 0, ',', '.') }} Kategori</h4>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 sm:p-4.5 border border-slate-200/90 shadow-xs flex items-center gap-3.5 hover:border-slate-300 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-700 border border-amber-200/60 flex items-center justify-center text-base shrink-0 font-mono-num">
                <i class="fa-solid fa-folder-open"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono-num truncate">Kategori Kosong</p>
                <h4 class="text-lg sm:text-xl font-bold text-slate-900 mt-0.5 font-mono-num truncate">{{ number_format($totalCategories - $categoriesWithProducts, 0, ',', '.') }} Kategori</h4>
            </div>
        </div>
    </div>

    <!-- CATEGORIES LEDGER TABLE -->
    <div class="bg-white rounded-lg shadow-xs border border-slate-200/90 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3 bg-slate-50/50">
            <div>
                <h3 class="font-bold text-xs sm:text-sm text-slate-900 uppercase tracking-wider font-mono-num">Daftar Semua Kategori</h3>
                <p class="text-xs text-slate-400 mt-0.5">Klasifikasi produk yang aktif di marketplace NitipDong</p>
            </div>
            <form action="{{ auth()->user()->role === 'super_admin' ? route('super_admin.categories.index') : route('admin.categories.index') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ $search }}" class="w-full sm:w-60 h-8.5 pl-8 pr-3 text-xs rounded-lg border border-slate-200 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono-num transition-colors placeholder:text-slate-400" placeholder="Cari nama atau slug...">
                <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 text-xs"></i>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100 font-mono-num">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Ikon</th>
                        <th class="px-5 py-3 font-semibold">Nama Kategori</th>
                        <th class="px-5 py-3 font-semibold">Slug URL</th>
                        <th class="px-5 py-3 font-semibold text-center">Jumlah Produk</th>
                        <th class="px-5 py-3 font-semibold">Tanggal Dibuat</th>
                        <th class="px-5 py-3 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $category)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 border border-blue-200/60 flex items-center justify-center text-sm shadow-2xs font-mono-num">
                                <i class="{{ $category->icon ?: 'fa-solid fa-tag' }}"></i>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="font-bold text-slate-900 text-xs block">{{ $category->name }}</span>
                            <span class="text-[10px] text-slate-400 font-mono-num">ID: #{{ $category->id }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="font-mono-num text-[11px] text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200 inline-block max-w-[200px] truncate" title="/products?category={{ $category->slug }}">
                                /products?category={{ $category->slug }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold font-mono-num {{ $category->products_count > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200/60' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                <i class="fa-solid fa-box text-[9px]"></i>
                                {{ $category->products_count }} Produk
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-slate-400 font-mono-num text-[11px]">
                            {{ $category->created_at ? $category->created_at->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.categories.edit', $category) : route('admin.categories.edit', $category) }}" class="p-1.5 bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-slate-600 rounded-md transition-colors border border-slate-200 shadow-2xs" title="Edit Kategori">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <form action="{{ auth()->user()->role === 'super_admin' ? route('super_admin.categories.destroy', $category) : route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori \'{{ $category->name }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 rounded-md transition-colors border border-slate-200 shadow-2xs cursor-pointer" title="Hapus Kategori">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                            <i class="fa-solid fa-tags text-2xl text-slate-300 mb-2 block"></i>
                            Tidak ada kategori yang ditemukan.
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
