<x-admin-layout>
    <x-slot name="title">
        Kelola Kategori Produk - {{ config('app.name', 'NitipDong') }}
    </x-slot>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-2">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-tags text-cyan-700"></i>
                Kelola Kategori Produk
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola seluruh kategori produk untuk klasifikasi storefront dan navigasi pembeli.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.categories.create') }}" class="btn-primary text-xs h-9 px-4 rounded-md bg-cyan-700 hover:bg-cyan-800 flex items-center gap-1.5">
                <i class="fa-solid fa-plus text-[10px]"></i>
                Tambah Kategori
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-card flex items-center gap-4">
            <div class="w-11 h-11 rounded-lg bg-cyan-50 text-cyan-700 flex items-center justify-center text-lg border border-cyan-200 shrink-0">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Total Kategori</p>
                <h4 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ number_format($totalCategories, 0, ',', '.') }} Kategori</h4>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-card flex items-center gap-4">
            <div class="w-11 h-11 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg border border-emerald-200 shrink-0">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Kategori Aktif (Ada Produk)</p>
                <h4 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ number_format($categoriesWithProducts, 0, ',', '.') }} Kategori</h4>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-card flex items-center gap-4">
            <div class="w-11 h-11 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center text-lg border border-amber-200 shrink-0">
                <i class="fa-solid fa-folder-open"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Kategori Kosong</p>
                <h4 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ number_format($totalCategories - $categoriesWithProducts, 0, ',', '.') }} Kategori</h4>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-card border border-slate-200/80 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3 bg-slate-50/50">
            <div>
                <h3 class="font-bold text-xs sm:text-sm text-slate-900 uppercase tracking-wider">Daftar Semua Kategori</h3>
                <p class="text-xs text-slate-400 mt-0.5">Klasifikasi produk yang aktif di marketplace NitipDong</p>
            </div>
            <form action="{{ route('admin.categories.index') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ $search }}" class="input text-xs pl-8 pr-4 h-8.5 rounded-md w-full sm:w-64" placeholder="Cari nama atau slug...">
                <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 text-xs"></i>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 font-semibold">Ikon</th>
                        <th class="px-5 py-3.5 font-semibold">Nama Kategori</th>
                        <th class="px-5 py-3.5 font-semibold">Slug URL</th>
                        <th class="px-5 py-3.5 font-semibold text-center">Jumlah Produk</th>
                        <th class="px-5 py-3.5 font-semibold">Tanggal Dibuat</th>
                        <th class="px-5 py-3.5 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $category)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="w-8 h-8 rounded-md bg-cyan-50 text-cyan-800 border border-cyan-200 flex items-center justify-center text-sm shadow-xs">
                                <i class="{{ $category->icon ?: 'fa-solid fa-tag' }}"></i>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="font-bold text-slate-900 text-xs block">{{ $category->name }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">ID: #{{ $category->id }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="font-mono text-[11px] text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                /products?category={{ $category->slug }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $category->products_count > 0 ? 'bg-cyan-50 text-cyan-800 border border-cyan-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                <i class="fa-solid fa-box text-[9px]"></i>
                                {{ $category->products_count }} Produk
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-slate-400">
                            {{ $category->created_at ? $category->created_at->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="p-1.5 bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 text-slate-600 rounded-md transition-colors border border-slate-200" title="Edit Kategori">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori \'{{ $category->name }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 rounded-md transition-colors border border-slate-200" title="Hapus Kategori">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                            Tidak ada kategori yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-slate-100 bg-white">
            {{ $categories->links('pagination::tailwind') }}
        </div>
    </div>
</x-admin-layout>
