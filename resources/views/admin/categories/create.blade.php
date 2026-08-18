<x-admin-layout>
    <x-slot name="title">
        Tambah Kategori Baru - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-[#12A57F] transition-colors font-medium">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali ke Daftar Kategori
        </a>
    </div>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-[#14213D] flex items-center gap-2" style="font-family:'Poppins',sans-serif;">
                <i class="fa-solid fa-folder-plus text-[#12A57F]"></i>
                Tambah Kategori Baru
            </h1>
            <p class="text-xs text-[#8A93A6] mt-0.5">Daftarkan kategori produk baru untuk mengelompokkan etalase toko.</p>
        </div>
    </div>

    <div class="max-w-2xl bg-white rounded-2xl shadow-sm border border-[#EFEBDF] overflow-hidden" x-data="{ 
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
        <form action="{{ route('admin.categories.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="name" name="name" x-model="name" @input="if(!slug) { $refs.slugInput.value = slugify(name); }" required placeholder="Contoh: Gadget & Elektronik" value="{{ old('name') }}"
                    class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] text-sm text-[#14213D] focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] transition-all">
                @error('name')
                    <p class="text-rose-500 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                    Slug URL (Opsional)
                </label>
                <input type="text" id="slug" name="slug" x-ref="slugInput" x-model="slug" placeholder="otomatis dibuat jika dikosongkan (contoh: gadget-elektronik)" value="{{ old('slug') }}"
                    class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] text-sm font-mono text-slate-600 bg-slate-50/50 focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] transition-all">
                <p class="text-[11px] text-slate-400 mt-1.5">Digunakan sebagai parameter tautan URL halaman produk.</p>
                @error('slug')
                    <p class="text-rose-500 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="icon" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                    Icon Font Awesome Class (Opsional)
                </label>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-[#12A57F] border border-emerald-100 flex items-center justify-center text-xl shrink-0 shadow-sm">
                        <i :class="icon || 'fa-solid fa-tags'"></i>
                    </div>
                    <input type="text" id="icon" name="icon" x-model="icon" placeholder="Contoh: fa-solid fa-laptop" value="{{ old('icon', 'fa-solid fa-tags') }}"
                        class="flex-1 px-4 py-3 rounded-xl border border-[#E7E3D8] text-sm text-[#14213D] focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] transition-all">
                </div>
                @error('icon')
                    <p class="text-rose-500 text-xs mt-1.5">{{ $message }}</p>
                @enderror

                <div class="bg-slate-50/70 p-3.5 rounded-xl border border-slate-100">
                    <p class="text-[11px] text-slate-500 font-medium mb-2">Pilihan Icon Cepat:</p>
                    <div class="flex flex-wrap gap-2">
                        @php
                        $presetIcons = [
                            ['label' => 'Gadget/Laptop', 'class' => 'fa-solid fa-laptop'],
                            ['label' => 'Pakaian/Fashion', 'class' => 'fa-solid fa-shirt'],
                            ['label' => 'Makanan/Kuliner', 'class' => 'fa-solid fa-burger'],
                            ['label' => 'Kecantikan', 'class' => 'fa-solid fa-wand-magic-sparkles'],
                            ['label' => 'Olahraga', 'class' => 'fa-solid fa-futbol'],
                            ['label' => 'Rumah Tangga', 'class' => 'fa-solid fa-house'],
                            ['label' => 'Otomotif', 'class' => 'fa-solid fa-car'],
                            ['label' => 'Buku & Alat Tulis', 'class' => 'fa-solid fa-book'],
                            ['label' => 'Kesehatan', 'class' => 'fa-solid fa-heart-pulse'],
                            ['label' => 'Game & Hobi', 'class' => 'fa-solid fa-gamepad'],
                        ];
                        @endphp
                        @foreach($presetIcons as $pi)
                            <button type="button" @click="icon = '{{ $pi['class'] }}'"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-[11px] text-slate-600 hover:border-[#12A57F] hover:text-[#12A57F] transition-colors">
                                <i class="{{ $pi['class'] }}"></i>
                                {{ $pi['label'] }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.categories.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#12A57F] hover:bg-[#0f8b6a] text-white text-xs font-semibold shadow-md shadow-[#12A57F]/20 transition-all">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
