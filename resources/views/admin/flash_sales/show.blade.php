<x-admin-layout>
    <x-slot name="title">
        Kelola Produk Flash Sale: {{ $flashSale->title ?? $flashSale->name }} - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Kelola Produk Flash Sale
    </x-slot>

    <div class="mb-3">
        <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.flash_sales.index') : route('admin.flash_sales.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-blue-700 transition-colors font-medium">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
            </svg>
            Kembali ke Daftar Flash Sale
        </a>
    </div>

    <!-- HERO / EVENT STATUS BANNER -->
    <div class="bg-[#0F172A] rounded-2xl p-5 text-white mb-6 relative overflow-hidden shadow-sm border border-slate-800">
        <div class="flex flex-col lg:flex-row justify-between lg:items-center gap-4 relative z-10">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $flashSale->is_running ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : ($flashSale->is_upcoming ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' : 'bg-slate-800 text-slate-400') }}">
                        <svg class="w-3 h-3 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                        </svg>
                        {{ $flashSale->status_badge['label'] }}
                    </span>
                    <span class="text-xs text-slate-400">ID: #{{ $flashSale->id }}</span>
                </div>
                <h1 class="text-xl font-bold text-white tracking-tight">{{ $flashSale->title ?? $flashSale->name }}</h1>
                <p class="text-xs text-slate-300 mt-1 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <span>{{ $flashSale->start_time->translatedFormat('d M Y, H:i') }}</span>
                    <span>&mdash;</span>
                    <span>{{ $flashSale->end_time->translatedFormat('d M Y, H:i') }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.flash_sales.edit', $flashSale) : route('admin.flash_sales.edit', $flashSale) }}" class="text-xs h-8.5 px-3 rounded-lg bg-white/10 text-white hover:bg-white/20 border border-white/10 flex items-center gap-1.5 font-semibold transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                    </svg>
                    Edit Jadwal
                </a>
                <form action="{{ auth()->user()->role === 'super_admin' ? route('super_admin.flash_sales.toggle', $flashSale) : route('admin.flash_sales.toggle', $flashSale) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-xs h-8.5 px-3 rounded-lg font-semibold transition-colors flex items-center gap-1.5 cursor-pointer {{ $flashSale->is_active ? 'bg-amber-500/20 text-amber-300 hover:bg-amber-500/30 border border-amber-500/30' : 'bg-blue-600 text-white hover:bg-blue-700' }}">
                        {{ $flashSale->is_active ? 'Nonaktifkan Event' : 'Aktifkan Event' }}
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-5 pt-4 border-t border-slate-800/80">
            <div>
                <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Total Produk</p>
                <p class="text-base font-bold text-white mt-0.5">{{ $flashSale->items->count() }} Item</p>
            </div>
            <div>
                <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Total Kuota Stok</p>
                <p class="text-base font-bold text-white mt-0.5">{{ $flashSale->items->sum('stock_allocated') }} Pcs</p>
            </div>
            <div>
                <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Total Terjual</p>
                <p class="text-base font-bold text-blue-400 mt-0.5">{{ $flashSale->items->sum('stock_sold') }} Pcs</p>
            </div>
            <div>
                <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Sisa Waktu</p>
                <p class="text-base font-bold text-amber-400 mt-0.5">
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

    <!-- MAIN INTERFACE -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Add Product to Flash Sale -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl p-5 shadow-xs border border-slate-200/90 sticky top-6"
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
                <div class="flex items-center gap-2 mb-3 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs border border-blue-200/60 shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-xs text-slate-900">Tambah Produk ke Flash Sale</h3>
                        <p class="text-[11px] text-slate-400">Tetapkan kuota dan harga promo khusus</p>
                    </div>
                </div>

                @if($availableProducts->count() > 0)
                <form action="{{ auth()->user()->role === 'super_admin' ? route('super_admin.flash_sales.items.add', $flashSale) : route('admin.flash_sales.items.add', $flashSale) }}" method="POST" class="space-y-3.5">
                    @csrf

                    <div>
                        <label for="product_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Pilih Produk <span class="text-rose-500">*</span>
                        </label>
                        <select id="product_id" name="product_id" required @change="onProductChange($event)" x-model="productId"
                            class="w-full h-9 px-2.5 text-xs rounded-lg border border-slate-200 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">-- Pilih Produk Tersedia --</option>
                            @foreach($availableProducts as $prod)
                                <option value="{{ $prod->id }}" data-price="{{ $prod->price }}" data-stock="{{ $prod->stock }}">
                                    {{ $prod->name }} &mdash; [{{ $prod->store->name ?? '-' }}] (Rp {{ number_format($prod->price, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <template x-if="originalPrice > 0">
                        <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-200 text-xs">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-slate-500">Harga Normal:</span>
                                <span class="font-bold text-slate-800" x-text="formatRupiah(originalPrice)"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Stok Toko:</span>
                                <span class="font-bold text-slate-800" x-text="productStock + ' Pcs'"></span>
                            </div>
                        </div>
                    </template>

                    <div>
                        <label for="flash_sale_price" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Harga Flash Sale (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" id="flash_sale_price" name="flash_sale_price" x-model="flashPrice" @input="calcDiscount()" required placeholder="Contoh: 199000" min="1"
                            class="w-full h-9 px-3 rounded-lg border border-slate-200 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500">

                        <template x-if="discountPct > 0">
                            <div class="mt-1.5 p-2 bg-emerald-50 rounded-lg border border-emerald-200 flex items-center justify-between text-xs">
                                <span class="text-emerald-800 font-semibold flex items-center gap-1">
                                    Diskon: <span x-text="discountPct + '%'"></span>
                                </span>
                                <span class="text-emerald-700 font-medium" x-text="'Hemat ' + formatRupiah(savings)"></span>
                            </div>
                        </template>
                    </div>

                    <div>
                        <label for="stock_allocated" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Alokasi Kuota Stok <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" id="stock_allocated" name="stock_allocated" x-model="stockAlloc" required placeholder="10" min="1"
                            class="w-full h-9 px-3 rounded-lg border border-slate-200 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <p class="text-[11px] text-slate-400 mt-1">Batas kuota yang dapat dibeli pembeli pada sesi ini.</p>
                    </div>

                    <div class="pt-1">
                        <button type="submit" class="w-full h-9 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs flex items-center justify-center gap-1.5 shadow-xs transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Daftarkan ke Flash Sale
                        </button>
                    </div>
                </form>
                @else
                <div class="py-8 text-center text-slate-400">
                    <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <p class="text-xs text-slate-700 font-medium">Semua produk aktif telah didaftarkan ke sesi ini.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Products in Flash Sale -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/90 overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <h3 class="font-bold text-xs sm:text-sm text-slate-900 uppercase tracking-wider">Produk Terdaftar ({{ $flashSale->items->count() }})</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Produk yang akan ditampilkan di etalase Flash Sale platform</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[11px] tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-3.5">Produk</th>
                                <th class="px-4 py-3.5">Harga Normal</th>
                                <th class="px-4 py-3.5">Harga Flash Sale</th>
                                <th class="px-4 py-3.5 text-center">Diskon</th>
                                <th class="px-4 py-3.5 text-center">Progress Stok</th>
                                <th class="px-4 py-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($flashSale->items as $item)
                            <tr class="hover:bg-slate-50/70 transition-colors" x-data="{ isEditing: false, flashPrice: '{{ (int)$item->flash_sale_price }}', stockAlloc: '{{ $item->stock_allocated }}' }">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 overflow-hidden shrink-0">
                                            <img src="{{ $item->product->image_url ?? asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('product.show', $item->product) }}" target="_blank" class="font-semibold text-slate-900 hover:text-blue-600 transition-colors truncate block max-w-xs text-xs">
                                                {{ $item->product->name }}
                                            </a>
                                            <p class="text-[11px] text-slate-400 mt-0.5">
                                                Toko: <span class="text-slate-600 font-medium">{{ $item->product->store->name ?? '-' }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-slate-400 line-through text-xs">
                                    Rp {{ number_format($item->product->price, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4">
                                    <template x-if="!isEditing">
                                        <div>
                                            <span class="text-blue-700 font-bold text-xs">Rp {{ number_format($item->flash_sale_price, 0, ',', '.') }}</span>
                                            <p class="text-[10px] text-slate-500 font-normal">Hemat Rp {{ number_format($item->product->price - $item->flash_sale_price, 0, ',', '.') }}</p>
                                        </div>
                                    </template>
                                    <template x-if="isEditing">
                                        <input type="number" x-model="flashPrice" class="w-28 h-8 px-2 rounded-lg border border-slate-300 text-xs">
                                    </template>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                        -{{ $item->discount_percentage }}%
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <template x-if="!isEditing">
                                        <div class="w-28 mx-auto">
                                            <div class="flex justify-between text-[10px] text-slate-500 mb-0.5">
                                                <span>{{ $item->stock_sold }} terjual</span>
                                                <span>{{ $item->stock_allocated }} kuota</span>
                                            </div>
                                            <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full transition-all {{ $item->is_sold_out ? 'bg-slate-400' : 'bg-blue-600' }}"
                                                     style="width:{{ $item->sold_percentage }}%;"></div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="isEditing">
                                        <div class="w-20 mx-auto">
                                            <input type="number" x-model="stockAlloc" class="w-full h-8 px-2 rounded-lg border border-slate-300 text-xs">
                                        </div>
                                    </template>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <template x-if="!isEditing">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button" @click="isEditing = true" class="p-1.5 bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-slate-600 rounded-lg transition-colors border border-slate-200 shadow-2xs" title="Edit Harga/Kuota">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                                                </svg>
                                            </button>
                                            <form action="{{ auth()->user()->role === 'super_admin' ? route('super_admin.flash_sales.items.remove', [$flashSale, $item]) : route('admin.flash_sales.items.remove', [$flashSale, $item]) }}" method="POST" onsubmit="return confirm('Hapus produk \'{{ $item->product->name }}\' dari Flash Sale?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 rounded-lg transition-colors border border-slate-200 shadow-2xs cursor-pointer" title="Hapus dari Flash Sale">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </template>
                                    <template x-if="isEditing">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <form action="{{ auth()->user()->role === 'super_admin' ? route('super_admin.flash_sales.items.update', [$flashSale, $item]) : route('admin.flash_sales.items.update', [$flashSale, $item]) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="flash_sale_price" :value="flashPrice">
                                                <input type="hidden" name="stock_allocated" :value="stockAlloc">
                                                <button type="submit" class="p-1.5 bg-blue-600 text-white hover:bg-blue-700 rounded-lg text-xs" title="Simpan">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <polyline points="20 6 9 17 4 12"/>
                                                    </svg>
                                                </button>
                                            </form>
                                            <button type="button" @click="isEditing = false" class="p-1.5 bg-slate-200 text-slate-700 hover:bg-slate-300 rounded-lg text-xs" title="Batal">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-5 py-14 text-center text-slate-400">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-700 block">Belum Ada Produk Terdaftar</span>
                                    <p class="text-xs text-slate-400 mt-1">Tambahkan produk dari form di samping untuk mengaktifkan promo ini.</p>
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
