<x-seller-layout>
    <x-slot name="title">
        Tambah Produk Baru - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="mb-2">
        <a href="{{ route('seller.products.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-[#12A57F] transition-colors font-medium">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali ke Katalog Produk
        </a>
    </div>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-2">
        <div>
            <h1 class="text-xl font-bold text-[#14213D] flex items-center gap-2" style="font-family:'Poppins',sans-serif;">
                <i class="fa-solid fa-plus-circle text-[#12A57F]"></i>
                Tambah Produk Baru
            </h1>
            <p class="text-xs text-[#8A93A6] mt-0.5">Isi rincian informasi produk yang ingin Anda jual di BelanjaIn.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-[#EFEBDF] overflow-hidden">
        <form action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf

            <div>
                <div class="flex items-center gap-2 pb-3 mb-5 border-b border-slate-100">
                    <span class="w-1 h-5 rounded-full bg-[#12A57F] inline-block"></span>
                    <h3 class="font-bold text-slate-800 text-sm" style="font-family:'Poppins',sans-serif;">Informasi Produk</h3>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Nama Produk <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            placeholder="Contoh: Laptop Asus Zenbook OLED 14 Inch"
                            class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] text-sm text-[#14213D] focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] transition-all">
                        @error('name')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4"
                         x-data="{ 
                            price: {{ old('price', 0) }}, 
                            discount: {{ old('discount_percentage', 0) }},
                            get finalPrice() {
                                if (this.discount > 0 && this.price > 0) {
                                    return Math.round(this.price * (1 - (this.discount / 100)));
                                }
                                return this.price;
                            },
                            formatRupiah(num) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
                            }
                         }">
                        <div>
                            <label for="category_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                                Kategori <span class="text-rose-500">*</span>
                            </label>
                            <select id="category_id" name="category_id" required
                                class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] text-sm text-[#14213D] focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] transition-all">
                                <option value="" disabled selected>Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="price" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                                Harga Normal (Rp) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                <input type="number" id="price" name="price" x-model.number="price" value="{{ old('price') }}" required min="0"
                                    placeholder="150000"
                                    class="w-full pl-10 pr-4 py-3 rounded-xl border border-[#E7E3D8] text-sm text-[#14213D] focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] transition-all">
                            </div>
                            @error('price')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="discount_percentage" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                                Diskon Promosi (%)
                            </label>
                            <div class="relative">
                                <input type="number" id="discount_percentage" name="discount_percentage" x-model.number="discount" value="{{ old('discount_percentage', 0) }}" min="0" max="99"
                                    placeholder="0"
                                    class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] text-sm text-[#14213D] focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] transition-all">
                                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">%</span>
                            </div>
                            @error('discount_percentage')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="stock" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                                Jumlah Stok <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" id="stock" name="stock" value="{{ old('stock', 10) }}" required min="0"
                                placeholder="10"
                                class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] text-sm text-[#14213D] focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] transition-all">
                            @error('stock')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2 lg:col-span-4" x-show="discount > 0">
                            <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 flex items-center justify-between text-xs">
                                <span class="text-emerald-800 font-semibold flex items-center gap-1.5">
                                    <i class="fa-solid fa-tags"></i>
                                    Harga Jual Konsumen: <span class="text-sm font-bold" x-text="formatRupiah(finalPrice)"></span>
                                </span>
                                <span class="text-emerald-700 font-medium" x-text="'Potongan ' + discount + '% (Hemat ' + formatRupiah(price - finalPrice) + ')'"></span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Deskripsi Produk <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="description" name="description" rows="5" required
                            placeholder="Tuliskan spesifikasi lengkap, keunggulan, garansi, dan kelengkapan produk..."
                            class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] text-sm text-[#14213D] focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] transition-all">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div>
                <div class="flex items-center gap-2 pb-3 mb-5 border-b border-slate-100">
                    <span class="w-1 h-5 rounded-full bg-[#12A57F] inline-block"></span>
                    <h3 class="font-bold text-slate-800 text-sm" style="font-family:'Poppins',sans-serif;">Foto Produk</h3>
                </div>

                <div x-data="{ preview: null }">
                    <p class="text-xs text-slate-400 mb-3">Format didukung: JPG, JPEG, PNG, WEBP (Maks. 2MB). Gunakan foto produk beresolusi tinggi.</p>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200/70">
                        <div class="w-24 h-24 rounded-xl border border-slate-200 bg-white overflow-hidden flex items-center justify-center shrink-0 shadow-sm">
                            <template x-if="preview">
                                <img :src="preview" class="w-full h-full object-cover" alt="Preview">
                            </template>
                            <template x-if="!preview">
                                <div class="text-center text-slate-300">
                                    <i class="fa-solid fa-image text-2xl"></i>
                                    <p class="text-[9px] mt-1 text-slate-400">Belum ada foto</p>
                                </div>
                            </template>
                        </div>
                        <div class="flex-1">
                            <input type="file" id="image" name="image" accept="image/*"
                                @change="const file = $event.target.files[0]; if(file) { const r = new FileReader(); r.onload = e => preview = e.target.result; r.readAsDataURL(file); }"
                                class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#12A57F]/10 file:text-[#12A57F] hover:file:bg-[#12A57F]/20 cursor-pointer">
                            @error('image')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('seller.products.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#12A57F] hover:bg-[#0f8b6a] text-white text-xs font-semibold shadow-md shadow-[#12A57F]/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    Simpan & Terbitkan Produk
                </button>
            </div>
        </form>
    </div>
</x-seller-layout>
