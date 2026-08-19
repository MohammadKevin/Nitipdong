<x-seller-layout>
    <x-slot name="title">
        Edit Produk: {{ $product->name }} - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="mb-3">
        <a href="{{ route('seller.products.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-cyan-700 transition-colors font-medium">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            Kembali ke Katalog Produk
        </a>
    </div>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-5">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-cyan-700"></i>
                Edit Produk: {{ $product->name }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Perbarui rincian, harga, stok, atau status keaktifan listing toko.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-card border border-slate-200/80 overflow-hidden">
        <form action="{{ route('seller.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <div class="flex items-center gap-2 pb-2.5 mb-4 border-b border-slate-100">
                    <span class="w-1 h-4 rounded-full bg-cyan-700 inline-block"></span>
                    <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Informasi Dasar Produk</h3>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Produk <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required
                            placeholder="Contoh: Laptop Asus Zenbook OLED 14 Inch"
                            class="input text-xs rounded-md">
                        @error('name')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5"
                         x-data="{
                            price: {{ old('price', $product->seller_price) }},
                            discount: {{ old('discount_percentage', $product->discount_percentage ?? 0) }},
                            get markupPrice() {
                                return Math.round(this.price * 1.05);
                            },
                            get finalPrice() {
                                if (this.discount > 0 && this.markupPrice > 0) {
                                    return Math.round(this.markupPrice * (1 - (this.discount / 100)));
                                }
                                return this.markupPrice;
                            },
                            formatRupiah(num) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
                            }
                         }">
                        <div>
                            <label for="category_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Kategori <span class="text-rose-500">*</span>
                            </label>
                            <select id="category_id" name="category_id" required
                                class="input text-xs rounded-md">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="price" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Harga Dasar Toko (Rp) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                <input type="number" id="price" name="price" x-model.number="price" value="{{ old('price', $product->seller_price) }}" required min="0"
                                    placeholder="150000"
                                    class="input text-xs pl-8 rounded-md">
                            </div>
                            @error('price')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="discount_percentage" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Diskon Promosi (%)
                            </label>
                            <div class="relative">
                                <input type="number" id="discount_percentage" name="discount_percentage" x-model.number="discount" value="{{ old('discount_percentage', $product->discount_percentage ?? 0) }}" min="0" max="99"
                                    placeholder="0"
                                    class="input text-xs pr-7 rounded-md">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">%</span>
                            </div>
                            @error('discount_percentage')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="stock" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Jumlah Stok <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" required min="0"
                                placeholder="10"
                                class="input text-xs rounded-md">
                            @error('stock')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2 lg:col-span-4" x-show="price > 0">
                            <div class="p-3 bg-cyan-50/70 rounded-md border border-cyan-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 text-xs">
                                <div>
                                    <span class="text-cyan-900 font-semibold flex items-center gap-1.5">
                                        <i class="fa-solid fa-calculator text-cyan-700 text-[11px]"></i>
                                        Harga Tayang Pembeli: <span class="text-xs font-bold text-cyan-800" x-text="formatRupiah(markupPrice)"></span>
                                        <span class="text-[10px] text-cyan-700 font-medium">(Termasuk Komisi Platform 5%: <span x-text="formatRupiah(markupPrice - price)"></span>)</span>
                                    </span>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Pendapatan bersih yang diterima toko: <strong class="text-slate-800" x-text="formatRupiah(price)"></strong></p>
                                </div>
                                <template x-if="discount > 0">
                                    <span class="text-emerald-700 font-semibold px-2 py-0.5 bg-emerald-100 rounded text-[11px]" x-text="'Harga Diskon Konsumen: ' + formatRupiah(finalPrice) + ' (Hemat ' + discount + '%)'"></span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Deskripsi Produk <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="description" name="description" rows="5" required
                            placeholder="Tuliskan spesifikasi lengkap, keunggulan, garansi, dan kelengkapan produk..."
                            class="input text-xs rounded-md">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-1">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                                class="w-4 h-4 text-cyan-600 rounded border-slate-300 focus:ring-cyan-500">
                            <div>
                                <span class="text-xs font-semibold text-slate-800">Status Produk Aktif (Tampil di Etalase)</span>
                                <p class="text-[11px] text-slate-400">Jika dinonaktifkan, produk tidak akan muncul pada hasil pencarian pembeli.</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div>
                <div class="flex items-center gap-2 pb-2.5 mb-4 border-b border-slate-100">
                    <span class="w-1 h-4 rounded-full bg-cyan-700 inline-block"></span>
                    <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Foto Produk</h3>
                </div>

                <div x-data="{
                    mainPreview: null,
                    extraPreviews: [],
                    deletedImages: [],
                    handleExtra(event) {
                        const files = Array.from(event.target.files);
                        files.forEach(file => {
                            const r = new FileReader();
                            r.onload = e => this.extraPreviews.push(e.target.result);
                            r.readAsDataURL(file);
                        });
                    },
                    markDelete(path) {
                        this.deletedImages.push(path);
                    }
                }">
                    <p class="text-xs text-slate-400 mb-3">Format didukung: JPG, PNG, WEBP (Maks. 2MB). Pilih foto baru untuk mengganti foto utama.</p>

                    {{-- Foto Utama --}}
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Foto Utama</label>
                        <div class="flex flex-col sm:flex-row items-center gap-4 p-4 rounded-md bg-slate-50 border border-slate-200">
                            <div class="w-20 h-20 rounded-md border border-slate-200 bg-white overflow-hidden flex items-center justify-center shrink-0 shadow-xs">
                                <template x-if="mainPreview">
                                    <img :src="mainPreview" class="w-full h-full object-cover" alt="Preview Baru">
                                </template>
                                <template x-if="!mainPreview">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                                    @else
                                        <div class="text-center text-slate-300">
                                            <i class="fa-solid fa-image text-xl"></i>
                                            <p class="text-[9px] mt-0.5 text-slate-400">Belum ada foto</p>
                                        </div>
                                    @endif
                                </template>
                            </div>
                            <div class="flex-1 w-full">
                                <input type="file" id="image" name="image" accept="image/*"
                                    @change="const file = $event.target.files[0]; if(file) { const r = new FileReader(); r.onload = e => mainPreview = e.target.result; r.readAsDataURL(file); }"
                                    class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-800 hover:file:bg-cyan-100 cursor-pointer">
                                @error('image')
                                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Foto Tambahan yang sudah ada --}}
                    @if($product->images && count($product->images))
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Foto Tambahan Saat Ini</label>
                        <div class="flex flex-wrap gap-2 p-3 rounded-md bg-slate-50 border border-slate-200">
                            @foreach($product->images as $imgPath)
                            @php
                                $extraUrl = str_starts_with($imgPath, 'http')
                                    ? $imgPath
                                    : (str_starts_with($imgPath, 'img/') ? asset($imgPath) : asset('storage/' . $imgPath));
                            @endphp
                            <div class="relative w-16 h-16 rounded-md border border-slate-200 overflow-hidden bg-white group"
                                 x-data="{ deleted: false }" x-show="!deleted">
                                <img src="{{ $extraUrl }}" class="w-full h-full object-cover" alt="Foto produk">
                                <button type="button"
                                    @click="deleted = true; markDelete('{{ $imgPath }}')"
                                    class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                    <i class="fa-solid fa-trash text-white text-xs"></i>
                                </button>
                            </div>
                            @endforeach
                            {{-- Hidden inputs untuk foto yang dihapus --}}
                            <template x-for="path in deletedImages" :key="path">
                                <input type="hidden" name="delete_images[]" :value="path">
                            </template>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Hover foto dan klik ikon sampah untuk menghapus.</p>
                    </div>
                    @endif

                    {{-- Upload Foto Tambahan Baru --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tambah Foto Baru <span class="text-slate-400 font-normal">(opsional)</span></label>
                        <div class="p-4 rounded-md bg-slate-50 border border-slate-200 space-y-3">
                            <div class="flex flex-wrap gap-2" x-show="extraPreviews.length > 0">
                                <template x-for="(src, i) in extraPreviews" :key="i">
                                    <div class="relative w-16 h-16 rounded-md border border-slate-200 overflow-hidden bg-white group">
                                        <img :src="src" class="w-full h-full object-cover">
                                        <button type="button" @click="extraPreviews.splice(i, 1)"
                                            class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                            <i class="fa-solid fa-trash text-white text-xs"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                            <input type="file" name="images[]" accept="image/*" multiple
                                @change="handleExtra($event)"
                                class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                            @error('images.*')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <a href="{{ route('seller.products.index') }}" class="btn-secondary text-xs h-9 px-4 rounded-md">
                    Batal
                </a>
                <button type="submit" class="btn-primary text-xs h-9 px-5 rounded-md bg-cyan-700 hover:bg-cyan-800 flex items-center gap-1.5">
                    <i class="fa-solid fa-check text-[10px]"></i>
                    Simpan Perubahan Produk
                </button>
            </div>
        </form>
    </div>
</x-seller-layout>
