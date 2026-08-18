<x-admin-layout>
    <x-slot name="title">
        Kelola Produk Flash Sale: {{ $flashSale->title }} - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.flash_sales.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-[#12A57F] transition-colors font-medium">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali ke Daftar Flash Sale
        </a>
    </div>

    <div class="bg-gradient-to-r from-[#152238] to-[#1E293B] rounded-2xl p-6 text-white mb-6 relative overflow-hidden shadow-lg border border-slate-800">
        <div class="flex flex-col lg:flex-row justify-between lg:items-center gap-6 relative z-10">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $flashSale->is_running ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : ($flashSale->is_upcoming ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' : 'bg-slate-700 text-slate-300') }}">
                        <i class="fa-solid fa-bolt text-amber-400"></i>
                        {{ $flashSale->status_badge['label'] }}
                    </span>
                    <span class="text-xs text-slate-400 font-mono">ID: #{{ $flashSale->id }}</span>
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight font-display">{{ $flashSale->title }}</h1>
                <p class="text-xs text-slate-300 mt-1 flex items-center gap-2">
                    <i class="fa-regular fa-clock text-amber-400"></i>
                    <span>{{ $flashSale->start_time->translatedFormat('d M Y, H:i') }}</span>
                    <span>&mdash;</span>
                    <span>{{ $flashSale->end_time->translatedFormat('d M Y, H:i') }}</span>
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.flash_sales.edit', $flashSale) }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-semibold transition-all flex items-center gap-1.5 border border-white/10">
                    <i class="fa-solid fa-pen-to-square"></i>
                    Edit Jadwal
                </a>
                <form action="{{ route('admin.flash_sales.toggle', $flashSale) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-1.5 {{ $flashSale->is_active ? 'bg-amber-500/20 text-amber-300 hover:bg-amber-500/30 border border-amber-500/30' : 'bg-[#12A57F] text-white hover:bg-[#0f8b6a]' }}">
                        <i class="fa-solid {{ $flashSale->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                        {{ $flashSale->is_active ? 'Nonaktifkan' : 'Aktifkan Event' }}
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t border-slate-700/60">
            <div>
                <p class="text-[11px] text-slate-400 font-medium">Total Produk</p>
                <p class="text-xl font-bold text-white mt-0.5">{{ $flashSale->items->count() }} Item</p>
            </div>
            <div>
                <p class="text-[11px] text-slate-400 font-medium">Total Kuota Stok</p>
                <p class="text-xl font-bold text-white mt-0.5">{{ $flashSale->items->sum('stock_allocated') }} Pcs</p>
            </div>
            <div>
                <p class="text-[11px] text-slate-400 font-medium">Total Terjual</p>
                <p class="text-xl font-bold text-emerald-400 mt-0.5">{{ $flashSale->items->sum('stock_sold') }} Pcs</p>
            </div>
            <div>
                <p class="text-[11px] text-slate-400 font-medium">Sisa Waktu</p>
                <p class="text-xl font-bold text-amber-400 mt-0.5">
                    @if($flashSale->is_running)
                        {{ $flashSale->end_time->diffForHumans(['parts' => 2, 'short' => true]) }}
                    @elseif($flashSale->is_upcoming)
                        Mulai {{ $flashSale->start_time->diffForHumans(['parts' => 2, 'short' => true]) }}
                    @else
                        Selesai
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#EFEBDF] sticky top-6"
                 x-data="{
                     productId: '',
                     originalPrice: 0,
                     productStock: 0,
                     flashPrice: '',
                     stockAlloc: 10,
                     discountPct: 0,
                     savings: 0,
                     onProductChange(event) {
                         const select = event.target;
                         const option = select.options[select.selectedIndex];
                         if (option && option.dataset.price) {
                             this.originalPrice = parseFloat(option.dataset.price);
                             this.productStock = parseInt(option.dataset.stock) || 1;
                             this.calcDiscount();
                         } else {
                             this.originalPrice = 0;
                             this.productStock = 0;
                             this.discountPct = 0;
                             this.savings = 0;
                         }
                     },
                     calcDiscount() {
                         let fp = parseFloat(this.flashPrice);
                         if (this.originalPrice > 0 && fp > 0 && fp < this.originalPrice) {
                             this.discountPct = Math.round(((this.originalPrice - fp) / this.originalPrice) * 100);
                             this.savings = this.originalPrice - fp;
                         } else {
                             this.discountPct = 0;
                             this.savings = 0;
                         }
                     },
                     formatRupiah(num) {
                         return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
                     }
                 }">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-[#14213D]" style="font-family:'Poppins',sans-serif;">Tambah Produk ke Flash Sale</h3>
                        <p class="text-[11px] text-slate-400">Pilih produk etalase dan tetapkan harga promo khusus.</p>
                    </div>
                </div>

                @if($availableProducts->count() > 0)
                <form action="{{ route('admin.flash_sales.items.add', $flashSale) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="product_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Pilih Produk <span class="text-rose-500">*</span>
                        </label>
                        <select id="product_id" name="product_id" required @change="onProductChange($event)" x-model="productId"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-[#E7E3D8] text-xs text-[#14213D] focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F]">
                            <option value="">-- Pilih Produk Tersedia --</option>
                            @foreach($availableProducts as $prod)
                                <option value="{{ $prod->id }}" data-price="{{ $prod->price }}" data-stock="{{ $prod->stock }}">
                                    {{ $prod->name }} &mdash; [Toko: {{ $prod->store->name ?? '-' }}] (Rp {{ number_format($prod->price, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <template x-if="originalPrice > 0">
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/60 text-xs">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-slate-500">Harga Normal:</span>
                                <span class="font-bold text-slate-800" x-text="formatRupiah(originalPrice)"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Stok Toko Tersedia:</span>
                                <span class="font-bold text-slate-800" x-text="productStock + ' Pcs'"></span>
                            </div>
                        </div>
                    </template>

                    <div>
                        <label for="flash_sale_price" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Harga Flash Sale (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" id="flash_sale_price" name="flash_sale_price" x-model="flashPrice" @input="calcDiscount()" required placeholder="Contoh: 199000" min="1"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-[#E7E3D8] text-xs text-[#14213D] focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F]">
                        
                        <template x-if="discountPct > 0">
                            <div class="mt-2 p-2.5 bg-emerald-50 rounded-lg border border-emerald-200 flex items-center justify-between text-xs">
                                <span class="text-emerald-800 font-semibold flex items-center gap-1">
                                    <i class="fa-solid fa-tags"></i> Diskon: <span x-text="discountPct + '%'"></span>
                                </span>
                                <span class="text-emerald-700 font-medium" x-text="'Hemat ' + formatRupiah(savings)"></span>
                            </div>
                        </template>
                    </div>

                    <div>
                        <label for="stock_allocated" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Alokasi Kuota Stok Flash Sale <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" id="stock_allocated" name="stock_allocated" x-model="stockAlloc" required placeholder="10" min="1"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-[#E7E3D8] text-xs text-[#14213D] focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F]">
                        <p class="text-[10px] text-slate-400 mt-1">Batas jumlah unit yang boleh dibeli pembeli dengan harga promo Flash Sale.</p>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-2.5 rounded-xl bg-[#12A57F] hover:bg-[#0f8b6a] text-white text-xs font-semibold shadow-md shadow-[#12A57F]/20 transition-all flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-plus"></i>
                            Daftarkan ke Flash Sale
                        </button>
                    </div>
                </form>
                @else
                <div class="py-8 text-center text-slate-400">
                    <i class="fa-solid fa-check-circle text-2xl text-emerald-500 mb-2"></i>
                    <p class="text-xs text-slate-600 font-medium">Semua produk etalase telah didaftarkan ke sesi ini.</p>
                </div>
                @endif
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-[#EFEBDF] overflow-hidden">
                <div class="p-5 border-b border-[#F0EEE6] flex items-center justify-between bg-slate-50/50">
                    <div>
                        <h3 class="font-bold text-sm text-[#14213D]" style="font-family:'Poppins',sans-serif;">Daftar Produk Terdaftar ({{ $flashSale->items->count() }})</h3>
                        <p class="text-[11px] text-slate-400">Produk yang akan ditampilkan di blok Flash Sale beranda & detail produk.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-[#FAF9F5] text-[#8A93A6] font-semibold border-b border-[#F0EEE6]">
                            <tr>
                                <th class="px-5 py-3.5">Produk</th>
                                <th class="px-4 py-3.5">Harga Normal</th>
                                <th class="px-4 py-3.5">Harga Flash Sale</th>
                                <th class="px-4 py-3.5 text-center">Diskon</th>
                                <th class="px-4 py-3.5 text-center">Progress Stok</th>
                                <th class="px-4 py-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F5F3EE]">
                            @forelse($flashSale->items as $item)
                            <tr class="hover:bg-slate-50/80 transition-colors" x-data="{ isEditing: false, flashPrice: '{{ (int)$item->flash_sale_price }}', stockAlloc: '{{ $item->stock_allocated }}', isActive: {{ $item->is_active ? 'true' : 'false' }} }">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-11 h-11 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0">
                                            @if($item->product->image)
                                                <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-400 text-base">
                                                    <i class="fa-solid fa-box"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('product.show', $item->product) }}" target="_blank" class="font-bold text-slate-800 hover:text-[#12A57F] transition-colors truncate block max-w-xs text-xs">
                                                {{ $item->product->name }}
                                            </a>
                                            <p class="text-[10px] text-slate-400 mt-0.5">
                                                Toko: <span class="text-slate-600 font-medium">{{ $item->product->store->name ?? '-' }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-slate-400 line-through">
                                    Rp {{ number_format($item->product->price, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 font-bold text-brand-600">
                                    <template x-if="!isEditing">
                                        <div>
                                            <span class="text-emerald-700 font-bold text-xs">Rp {{ number_format($item->flash_sale_price, 0, ',', '.') }}</span>
                                            <p class="text-[10px] text-emerald-600 font-normal">Hemat Rp {{ number_format($item->product->price - $item->flash_sale_price, 0, ',', '.') }}</p>
                                        </div>
                                    </template>
                                    <template x-if="isEditing">
                                        <input type="number" x-model="flashPrice" class="w-28 px-2 py-1 text-xs border rounded-lg focus:ring-1 focus:ring-[#12A57F]">
                                    </template>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                        -{{ $item->discount_percentage }}%
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <template x-if="!isEditing">
                                        <div class="w-32 mx-auto">
                                            <div class="flex justify-between text-[10px] text-slate-500 mb-1">
                                                <span>Terjual: {{ $item->stock_sold }}</span>
                                                <span>Kuota: {{ $item->stock_allocated }}</span>
                                            </div>
                                            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full transition-all {{ $item->is_sold_out ? 'bg-slate-400' : 'bg-gradient-to-r from-amber-500 to-rose-500' }}"
                                                     style="width:{{ $item->sold_percentage }}%;"></div>
                                            </div>
                                            <span class="text-[9px] text-slate-400 mt-0.5 block">{{ $item->sold_percentage }}% Terjual</span>
                                        </div>
                                    </template>
                                    <template x-if="isEditing">
                                        <div class="w-24 mx-auto">
                                            <label class="text-[10px] text-slate-500">Kuota:</label>
                                            <input type="number" x-model="stockAlloc" class="w-full px-2 py-1 text-xs border rounded-lg focus:ring-1 focus:ring-[#12A57F]">
                                        </div>
                                    </template>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <template x-if="!isEditing">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button" @click="isEditing = true" class="p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg transition-colors" title="Edit Harga/Kuota">
                                                <i class="fa-solid fa-pen text-xs"></i>
                                            </button>
                                            <form action="{{ route('admin.flash_sales.items.remove', [$flashSale, $item]) }}" method="POST" onsubmit="return confirm('Hapus produk \'{{ $item->product->name }}\' dari Flash Sale?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-lg transition-colors" title="Hapus dari Flash Sale">
                                                    <i class="fa-solid fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </template>
                                    <template x-if="isEditing">
                                        <div class="flex items-center justify-center gap-1">
                                            <form action="{{ route('admin.flash_sales.items.update', [$flashSale, $item]) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="flash_sale_price" :value="flashPrice">
                                                <input type="hidden" name="stock_allocated" :value="stockAlloc">
                                                <button type="submit" class="p-1.5 bg-emerald-600 text-white hover:bg-emerald-700 rounded-lg text-xs" title="Simpan">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                            </form>
                                            <button type="button" @click="isEditing = false" class="p-1.5 bg-slate-200 text-slate-700 hover:bg-slate-300 rounded-lg text-xs" title="Batal">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </div>
                                    </template>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center text-xl mb-2">
                                            <i class="fa-solid fa-bolt"></i>
                                        </div>
                                        <p class="font-medium text-slate-700 text-xs">Belum ada produk di Flash Sale ini</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Gunakan form di sebelah kiri untuk menambahkan produk etalase pertama Anda.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
