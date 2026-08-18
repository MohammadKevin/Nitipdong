<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-cyan-50/20 py-8">
        <div class="max-w-5xl mx-auto px-4">
            {{-- Header --}}
            <div class="mb-8">
                <a href="{{ route('seller.products.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-cyan-600 transition-colors mb-4 font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Produk
                </a>
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900" style="font-family:'Plus Jakarta Sans',sans-serif;">
                            Tambah Produk Baru
                        </h1>
                        <p class="text-slate-500 mt-1">Lengkapi informasi produk yang ingin Anda jual</p>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="productForm()">
                @csrf

                {{-- Upload Image Section --}}
                <div class="bg-white rounded-3xl shadow-lg border border-slate-100 p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-slate-900" style="font-family:'Plus Jakarta Sans',sans-serif;">Foto Produk</h3>
                            <p class="text-sm text-slate-500">Upload foto utama dan foto tambahan (maks 5 foto)</p>
                        </div>
                    </div>

                    {{-- Main Image Upload --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-slate-700 mb-3">Foto Utama <span class="text-rose-500">*</span></label>
                        <div class="relative border-2 border-dashed rounded-2xl p-8 text-center transition-all border-slate-200 hover:border-cyan-400 bg-slate-50/50 hover:bg-cyan-50/50 cursor-pointer group">
                            <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/webp" @change="handleMainImage($event)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">

                            <div x-show="!mainImagePreview" class="flex flex-col items-center">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                </div>
                                <p class="text-slate-700 font-semibold mb-1">Klik atau tarik foto ke sini</p>
                                <p class="text-slate-400 text-sm">JPG, PNG, WEBP (Maks. 2MB)</p>
                            </div>

                            <div x-show="mainImagePreview" x-cloak class="relative inline-block">
                                <img :src="mainImagePreview" class="max-h-64 rounded-xl shadow-lg">
                                <button type="button" @click.stop="removeMainImage()" class="absolute -top-3 -right-3 w-10 h-10 bg-rose-500 text-white rounded-full flex items-center justify-center hover:bg-rose-600 transition-all shadow-lg hover:scale-110">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @error('image')
                            <p class="text-rose-600 text-sm mt-2 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Additional Images --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-3">Foto Tambahan (Opsional)</label>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                            <template x-for="(preview, index) in additionalPreviews" :key="index">
                                <div class="relative aspect-square border-2 border-slate-200 rounded-xl overflow-hidden group">
                                    <img :src="preview" class="w-full h-full object-cover">
                                    <button type="button" @click="removeAdditionalImage(index)" class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <div class="w-10 h-10 bg-rose-500 rounded-full flex items-center justify-center hover:scale-110 transition-transform">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </div>
                                    </button>
                                </div>
                            </template>

                            <div x-show="additionalPreviews.length < 5" class="relative aspect-square border-2 border-dashed border-slate-300 rounded-xl hover:border-cyan-500 transition-all bg-slate-50 hover:bg-cyan-50 cursor-pointer group flex items-center justify-center">
                                <input type="file" name="additional_images[]" accept="image/jpeg,image/png,image/jpg,image/webp" @change="handleAdditionalImage($event)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" multiple>
                                <div class="text-center">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-slate-200 to-slate-300 group-hover:from-cyan-400 group-hover:to-blue-500 flex items-center justify-center mx-auto mb-2 transition-all">
                                        <svg class="w-6 h-6 text-slate-500 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs text-slate-500 font-medium" x-text="`${5 - additionalPreviews.length} lagi`"></p>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-slate-400 mt-3">💡 Tips: Upload foto dari berbagai sudut untuk hasil terbaik</p>
                    </div>
                </div>

                {{-- Product Information --}}
                <div class="bg-white rounded-3xl shadow-lg border border-slate-100 p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-slate-900" style="font-family:'Plus Jakarta Sans',sans-serif;">Informasi Produk</h3>
                            <p class="text-sm text-slate-500">Detail dan spesifikasi produk</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        {{-- Nama Produk --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Nama Produk <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full px-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:border-cyan-400 focus:ring-4 focus:ring-cyan-100 outline-none transition-all font-medium"
                                placeholder="Contoh: Sepatu Nike Air Max 90 Premium Original">
                            @error('name')
                                <p class="text-rose-600 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kategori & Badge --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                    Kategori <span class="text-rose-500">*</span>
                                </label>
                                <select name="category_id" required
                                    class="w-full px-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:border-cyan-400 focus:ring-4 focus:ring-cyan-100 outline-none transition-all font-medium">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="text-rose-600 text-sm mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Badge (Opsional)</label>
                                <select name="badge"
                                    class="w-full px-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:border-cyan-400 focus:ring-4 focus:ring-cyan-100 outline-none transition-all font-medium">
                                    <option value="">Tidak Ada</option>
                                    <option value="new" {{ old('badge') == 'new' ? 'selected' : '' }}>🆕 Produk Baru</option>
                                    <option value="sale" {{ old('badge') == 'sale' ? 'selected' : '' }}>🔥 Sale</option>
                                    <option value="hot" {{ old('badge') == 'hot' ? 'selected' : '' }}>⚡ Hot Item</option>
                                    <option value="bestseller" {{ old('badge') == 'bestseller' ? 'selected' : '' }}>⭐ Best Seller</option>
                                </select>
                            </div>
                        </div>

                        {{-- Harga & Diskon --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                    Harga <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-bold">Rp</span>
                                    <input type="number" name="price" value="{{ old('price') }}" required min="0" step="1000"
                                        class="w-full pl-14 pr-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:border-cyan-400 focus:ring-4 focus:ring-cyan-100 outline-none transition-all font-bold text-lg"
                                        placeholder="50000">
                                </div>
                                @error('price')
                                    <p class="text-rose-600 text-sm mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Diskon (%)</label>
                                <input type="number" name="discount_percentage" value="{{ old('discount_percentage', 0) }}" min="0" max="100"
                                    class="w-full px-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:border-cyan-400 focus:ring-4 focus:ring-cyan-100 outline-none transition-all font-bold text-lg"
                                    placeholder="0">
                                @error('discount_percentage')
                                    <p class="text-rose-600 text-sm mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Stok & Featured --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                    Stok <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" name="stock" value="{{ old('stock') }}" required min="0"
                                    class="w-full px-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:border-cyan-400 focus:ring-4 focus:ring-cyan-100 outline-none transition-all font-medium"
                                    placeholder="100">
                                @error('stock')
                                    <p class="text-rose-600 text-sm mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Produk Unggulan</label>
                                <label class="relative inline-flex items-center cursor-pointer mt-2">
                                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-14 h-7 bg-slate-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-cyan-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-cyan-500 peer-checked:to-blue-600"></div>
                                    <span class="ml-3 text-sm text-slate-600">Tampilkan di halaman utama</span>
                                </label>
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Deskripsi Produk <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="description" rows="8" required
                                class="w-full px-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:border-cyan-400 focus:ring-4 focus:ring-cyan-100 outline-none transition-all resize-none font-medium leading-relaxed"
                                placeholder="Jelaskan detail produk Anda:&#10;• Spesifikasi lengkap&#10;• Kondisi produk (baru/bekas)&#10;• Bahan dan ukuran&#10;• Isi paket&#10;• Keunggulan produk">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-rose-600 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="bg-gradient-to-r from-slate-50 to-cyan-50 rounded-3xl border border-slate-200 p-6">
                    <div class="flex flex-col sm:flex-row gap-4 justify-end">
                        <a href="{{ route('seller.products.index') }}"
                            class="px-8 py-3.5 bg-white border-2 border-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all text-center">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-10 py-3.5 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-bold rounded-xl shadow-xl shadow-cyan-500/30 hover:shadow-2xl hover:shadow-cyan-500/40 transition-all">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                Simpan Produk
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function productForm() {
            return {
                mainImagePreview: null,
                additionalPreviews: [],
                additionalFiles: [],

                handleMainImage(event) {
                    const file = event.target.files[0];
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.mainImagePreview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                },

                removeMainImage() {
                    this.mainImagePreview = null;
                    const input = document.querySelector('input[name="image"]');
                    if (input) input.value = '';
                },

                handleAdditionalImage(event) {
                    const files = Array.from(event.target.files);
                    const remainingSlots = 5 - this.additionalPreviews.length;

                    files.slice(0, remainingSlots).forEach(file => {
                        if (file.type.startsWith('image/')) {
                            this.additionalFiles.push(file);
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.additionalPreviews.push(e.target.result);
                            };
                            reader.readAsDataURL(file);
                        }
                    });

                    // Update the file input
                    this.updateAdditionalFilesInput();
                    event.target.value = '';
                },

                removeAdditionalImage(index) {
                    this.additionalPreviews.splice(index, 1);
                    this.additionalFiles.splice(index, 1);
                    this.updateAdditionalFilesInput();
                },

                updateAdditionalFilesInput() {
                    const input = document.querySelector('input[name="additional_images[]"]');
                    if (input && this.additionalFiles.length > 0) {
                        const dataTransfer = new DataTransfer();
                        this.additionalFiles.forEach(file => dataTransfer.items.add(file));
                        input.files = dataTransfer.files;
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
