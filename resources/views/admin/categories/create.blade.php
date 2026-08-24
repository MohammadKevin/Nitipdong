<x-admin-layout>
    <x-slot name="title">
        Tambah Kategori Baru - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Tambah Kategori Baru
    </x-slot>

    <div class="mb-3">
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-blue-700 transition-colors font-medium">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
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

    <div class="max-w-2xl bg-white rounded-lg shadow-xs border border-slate-200/90 overflow-hidden" x-data="{ 
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
        <form action="{{ route('admin.categories.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5 font-mono-num">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="name" name="name" x-model="name" @input="if(!slug) { $refs.slugInput.value = slugify(name); }" required placeholder="Contoh: Gadget & Elektronik" value="{{ old('name') }}"
                    class="w-full h-8.5 px-3 rounded-lg border border-slate-200 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                @error('name')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5 font-mono-num">
                    Slug URL (Opsional)
                </label>
                <input type="text" id="slug" name="slug" x-ref="slugInput" x-model="slug" placeholder="otomatis dibuat jika dikosongkan (contoh: gadget-elektronik)" value="{{ old('slug') }}"
                    class="w-full h-8.5 px-3 rounded-lg border border-slate-200 text-xs font-mono-num bg-slate-50 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                <p class="text-[11px] text-slate-400 mt-1">Digunakan sebagai parameter tautan URL halaman produk.</p>
                @error('slug')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="icon" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5 font-mono-num">
                    Ikon Font Awesome (Opsional)
                </label>
                <div class="flex items-center gap-2.5 mb-2.5">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-700 border border-blue-200/60 flex items-center justify-center text-sm shrink-0 shadow-2xs font-mono-num">
                        <i :class="icon || 'fa-solid fa-tags'"></i>
                    </div>
                    <input type="text" id="icon" name="icon" x-model="icon" placeholder="Contoh: fa-solid fa-laptop" value="{{ old('icon', 'fa-solid fa-tags') }}"
                        class="w-full h-8.5 px-3 rounded-lg border border-slate-200 text-xs font-mono-num focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                </div>
                @error('icon')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror

                <div class="bg-slate-50 p-3 rounded-lg border border-slate-200">
                    <p class="text-[11px] text-slate-500 font-semibold mb-2">Preset Ikon Cepat:</p>
                    <div class="flex flex-wrap gap-1.5">
                        @php
                        $presetIcons = [
                            ['label' => 'Gadget', 'class' => 'fa-solid fa-laptop'],
                            ['label' => 'Fashion', 'class' => 'fa-solid fa-shirt'],
                            ['label' => 'Kuliner', 'class' => 'fa-solid fa-burger'],
                            ['label' => 'Kecantikan', 'class' => 'fa-solid fa-wand-magic-sparkles'],
                            ['label' => 'Olahraga', 'class' => 'fa-solid fa-futbol'],
                            ['label' => 'Rumah Tangga', 'class' => 'fa-solid fa-house'],
                            ['label' => 'Otomotif', 'class' => 'fa-solid fa-car'],
                            ['label' => 'Buku', 'class' => 'fa-solid fa-book'],
                            ['label' => 'Kesehatan', 'class' => 'fa-solid fa-heart-pulse'],
                            ['label' => 'Gaming', 'class' => 'fa-solid fa-gamepad'],
                        ];
                        @endphp
                        @foreach($presetIcons as $pi)
                            <button type="button" @click="icon = '{{ $pi['class'] }}'"
                                class="inline-flex items-center gap-1 px-2 py-0.5 bg-white border border-slate-200 rounded text-[11px] text-slate-600 hover:border-blue-600 hover:text-blue-700 transition-colors cursor-pointer">
                                <i class="{{ $pi['class'] }} text-[10px]"></i>
                                {{ $pi['label'] }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <a href="{{ route('admin.categories.index') }}" class="h-8.5 px-3.5 flex items-center justify-center rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-semibold">
                    Batal
                </a>
                <button type="submit" class="h-8.5 px-4 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-xs transition-colors cursor-pointer">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
