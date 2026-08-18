<x-admin-layout>
    <x-slot name="title">
        Kelola Kategori Produk - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-2">
        <div>
            <h1 class="text-xl font-bold text-[#14213D] flex items-center gap-2" style="font-family:'Poppins',sans-serif;">
                <i class="fa-solid fa-tags text-[#12A57F]"></i>
                Kelola Kategori Produk
            </h1>
            <p class="text-xs text-[#8A93A6] mt-0.5">Kelola seluruh kategori produk untuk klasifikasi storefront dan navigasi pembeli.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-2 bg-[#12A57F] hover:bg-[#0f8b6a] text-white px-4 py-2.5 rounded-xl font-semibold text-xs transition-all shadow-md shadow-[#12A57F]/20">
                <i class="fa-solid fa-plus"></i>
                Tambah Kategori
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-[#EFEBDF] relative overflow-hidden">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400">Total Kategori</p>
                    <h4 class="text-2xl font-bold text-slate-800 font-display mt-0.5">{{ number_format($totalCategories, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-[#EFEBDF] relative overflow-hidden">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400">Kategori Aktif (Ada Produk)</p>
                    <h4 class="text-2xl font-bold text-slate-800 font-display mt-0.5">{{ number_format($categoriesWithProducts, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-[#EFEBDF] relative overflow-hidden">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400">Kategori Tanpa Produk</p>
                    <h4 class="text-2xl font-bold text-slate-800 font-display mt-0.5">{{ number_format($totalCategories - $categoriesWithProducts, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-[#EFEBDF] overflow-hidden">
        <div class="p-5 border-b border-[#F0EEE6] flex flex-col sm:flex-row justify-between sm:items-center gap-4 bg-slate-50/50">
            <h3 class="font-bold text-sm text-[#14213D]" style="font-family:'Poppins',sans-serif;">Daftar Semua Kategori</h3>
            <form action="{{ route('admin.categories.index') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ $search }}" class="bg-white border border-[#E7E3D8] rounded-xl pl-10 pr-4 py-2 text-xs focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] shadow-sm w-full sm:w-64" placeholder="Cari nama atau slug...">
                <button type="submit" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#12A57F]">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </button>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-[#FAF9F5] text-[#8A93A6] font-semibold border-b border-[#F0EEE6]">
                    <tr>
                        <th class="px-6 py-3.5">Icon</th>
                        <th class="px-6 py-3.5">Nama Kategori</th>
                        <th class="px-6 py-3.5">Slug URL</th>
                        <th class="px-6 py-3.5 text-center">Jumlah Produk</th>
                        <th class="px-6 py-3.5">Tanggal Dibuat</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F5F3EE]">
                    @forelse($categories as $category)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-[#12A57F] border border-emerald-100 flex items-center justify-center text-lg shadow-sm">
                                <i class="{{ $category->icon ?: 'fa-solid fa-icons' }}"></i>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-slate-800 text-sm block">{{ $category->name }}</span>
                            <span class="text-[10px] text-slate-400">ID: #{{ $category->id }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-mono text-xs text-slate-500 bg-slate-100 px-2.5 py-1 rounded-md">
                                /products?category={{ $category->slug }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold {{ $category->products_count > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">
                                <i class="fa-solid fa-box text-[10px]"></i>
                                {{ $category->products_count }} Produk
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-500">
                            {{ $category->created_at ? $category->created_at->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg transition-colors shadow-sm" title="Edit Kategori">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori \'{{ $category->name }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-lg transition-colors shadow-sm" title="Hapus Kategori">
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
                                    <i class="fa-solid fa-tags"></i>
                                </div>
                                <p class="font-medium text-slate-600 text-sm">Tidak ada kategori ditemukan</p>
                                <p class="text-xs text-slate-400 mt-1">Coba kata kunci lain atau tambahkan kategori baru.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-[#F0EEE6]">
            {{ $categories->links('pagination::tailwind') }}
        </div>
    </div>
</x-admin-layout>
