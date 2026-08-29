<x-seller-layout>
    <x-slot name="title">
        Edit Produk: {{ $product->name }} - {{ config('app.name', 'NitipDong') }}
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
        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('seller.products.index') }}" class="btn-secondary text-xs h-9 px-4 rounded-xl border border-slate-300 font-medium">
                Batal
            </a>
            <button type="submit" form="product-edit-form" class="btn-primary text-xs h-9 px-5 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-semibold flex items-center gap-1.5 shadow-xs">
                <i class="fa-solid fa-check text-[11px]"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-card border border-slate-200/80 overflow-hidden mb-12 max-h-[calc(100vh-230px)] min-h-[500px] overflow-y-auto">
        <form id="product-edit-form" action="{{ route('seller.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
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
                                return Math.round(this.price * 1.15);
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
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="stock" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                    Jumlah Stok <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[10px] text-slate-400 font-medium">Maks. 10.000 unit</span>
                            </div>
                            <input type="number" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" required min="0" max="10000"
                                placeholder="10"
                                class="input text-xs rounded-md">
                            @error('stock')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="max_order_quantity" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                    Maks. Beli per Pesanan
                                </label>
                                <span class="text-[10px] text-slate-400 font-medium">Batas order</span>
                            </div>
                            <input type="number" id="max_order_quantity" name="max_order_quantity" value="{{ old('max_order_quantity', $product->max_order_quantity ?? 10) }}" min="1" max="1000"
                                placeholder="10"
                                class="input text-xs rounded-md">
                            @error('max_order_quantity')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="weight" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Berat (KG)
                            </label>
                            <input type="number" step="0.01" id="weight" name="weight" value="{{ old('weight', $product->weight ?? 1.0) }}" min="0.01"
                                placeholder="1.0"
                                class="input text-xs rounded-md">
                            @error('weight')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="condition" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Kondisi Barang
                            </label>
                            <select id="condition" name="condition" class="input text-xs rounded-md">
                                <option value="new" {{ old('condition', $product->condition ?? 'new') == 'new' ? 'selected' : '' }}>Baru</option>
                                <option value="used" {{ old('condition', $product->condition ?? 'new') == 'used' ? 'selected' : '' }}>Bekas</option>
                            </select>
                            @error('condition')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2 lg:col-span-4" x-show="price > 0">
                            <div class="p-3 bg-cyan-50/70 rounded-md border border-cyan-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 text-xs">
                                <div>
                                    <span class="text-cyan-900 font-semibold flex items-center gap-1.5">
                                        <i class="fa-solid fa-calculator text-cyan-700 text-[11px]"></i>
                                        Harga Tayang Pembeli: <span class="text-xs font-bold text-cyan-800" x-text="formatRupiah(markupPrice)"></span>
                                        <span class="text-[10px] text-cyan-700 font-medium">(Termasuk Komisi Platform 15%: <span x-text="formatRupiah(markupPrice - price)"></span>)</span>
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
                        <textarea id="description" name="description" rows="8" required
                            placeholder="Tuliskan spesifikasi lengkap, keunggulan, garansi, dan kelengkapan produk..."
                            class="input text-xs rounded-lg min-h-[160px] leading-relaxed">{{ old('description', $product->description) }}</textarea>
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

                <div x-data="{ mainPreview: null }">
                    <p class="text-xs text-slate-400 mb-3">Format didukung: JPG, PNG, WEBP (Maks. 2MB). Pilih foto baru untuk mengganti foto produk utama.</p>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Foto Utama Produk</label>
                        <div class="flex flex-col sm:flex-row items-center gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="w-24 h-24 rounded-xl border border-slate-200 bg-white overflow-hidden flex items-center justify-center shrink-0 shadow-xs">
                                <template x-if="mainPreview">
                                    <img :src="mainPreview" class="w-full h-full object-cover" alt="Preview Baru">
                                </template>
                                <template x-if="!mainPreview">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                                    @else
                                        <div class="text-center text-slate-300">
                                            <i class="fa-solid fa-image text-2xl"></i>
                                            <p class="text-[10px] mt-1 text-slate-400">Belum ada foto</p>
                                        </div>
                                    @endif
                                </template>
                            </div>
                            <div class="flex-1 w-full">
                                <input type="file" id="image" name="image" accept="image/*"
                                    @change="const file = $event.target.files[0]; if(file) { const r = new FileReader(); r.onload = e => mainPreview = e.target.result; r.readAsDataURL(file); }"
                                    class="block w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-800 hover:file:bg-cyan-100 cursor-pointer">
                                <p class="text-[11px] text-slate-400 mt-1.5">Rekomendasi ukuran foto minimal 800 x 800 piksel dengan latar belakang bersih.</p>
                                @error('image')
                                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-seller-layout>
