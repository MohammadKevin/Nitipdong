<x-admin-layout>
    <x-slot name="title">
        Tambah Kategori Baru - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Tambah Kategori Baru
    </x-slot>

    <div class="mb-3">
        <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.categories.index') : route('admin.categories.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-blue-700 transition-colors font-medium">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
            </svg>
            Kembali ke Daftar Kategori
        </a>
    </div>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                Tambah Kategori Baru
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftarkan kategori produk baru untuk mengelompokkan etalase toko.</p>
        </div>
    </div>

    <div class="max-w-2xl bg-white rounded-xl shadow-xs border border-slate-200/90 overflow-hidden" x-data="{ 
        name: '{{ old('name') }}', 
        slug: '{{ old('slug') }}', 
        icon: '{{ old('icon', 'fa-solid fa-tags') }}',
        slugify(text) {
            return text.toString().toLowerCase().trim()
                .replace(/\s+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-');
        }
    }">
        <form action="{{ auth()->user()->role === 'super_admin' ? route('super_admin.categories.store') : route('admin.categories.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="name" name="name" x-model="name" @input="if(!slug) { $refs.slugInput.value = slugify(name); }" required placeholder="Contoh: Gadget & Elektronik" value="{{ old('name') }}"
                    class="w-full h-9 px-3 rounded-lg border border-slate-200 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                @error('name')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Slug URL (Opsional)
                </label>
                <input type="text" id="slug" name="slug" x-ref="slugInput" x-model="slug" placeholder="otomatis dibuat jika dikosongkan (contoh: gadget-elektronik)" value="{{ old('slug') }}"
                    class="w-full h-9 px-3 rounded-lg border border-slate-200 text-xs bg-slate-50 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                <p class="text-[11px] text-slate-400 mt-1">Digunakan sebagai parameter tautan URL halaman produk.</p>
                @error('slug')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="icon" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Kelas Ikon FontAwesome (Opsional)
                </label>
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-700 border border-blue-200/60 flex items-center justify-center text-sm shrink-0">
                        <i :class="icon || 'fa-solid fa-tag'"></i>
                    </div>
                    <input type="text" id="icon" name="icon" x-model="icon" placeholder="fa-solid fa-laptop" value="{{ old('icon', 'fa-solid fa-tags') }}"
                        class="flex-1 h-9 px-3 rounded-lg border border-slate-200 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Gunakan kelas ikon FontAwesome (misal: <code>fa-solid fa-shirt</code>, <code>fa-solid fa-mobile-screen</code>).</p>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.categories.index') : route('admin.categories.index') }}" class="px-4 py-2 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold text-xs transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition-colors shadow-xs cursor-pointer">
                    Simpan Kategori Baru
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
