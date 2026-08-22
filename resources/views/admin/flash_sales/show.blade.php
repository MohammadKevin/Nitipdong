<x-admin-layout>
    <x-slot name="title">
        Kelola Produk Flash Sale: {{ $flashSale->title }} - {{ config('app.name', 'NitipDong') }}
    </x-slot>

    <div class="mb-3">
        <a href="{{ route('admin.flash_sales.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-cyan-700 transition-colors font-medium">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            Kembali ke Daftar Flash Sale
        </a>
    </div>

    <div class="bg-slate-950 rounded-xl p-6 text-white mb-6 relative overflow-hidden shadow-card border border-slate-800">
        <div class="flex flex-col lg:flex-row justify-between lg:items-center gap-4 relative z-10">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $flashSale->is_running ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : ($flashSale->is_upcoming ? 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30' : 'bg-slate-800 text-slate-400') }}">
                        <i class="fa-solid fa-bolt text-amber-400 text-[9px]"></i>
                        {{ $flashSale->status_badge['label'] }}
                    </span>
                    <span class="text-[11px] text-slate-400 font-mono">ID: #{{ $flashSale->id }}</span>
                </div>
                <h1 class="text-xl font-bold text-white tracking-tight">{{ $flashSale->title }}</h1>
                <p class="text-xs text-slate-300 mt-1 flex items-center gap-1.5">
                    <i class="fa-regular fa-clock text-cyan-400"></i>
                    <span>{{ $flashSale->start_time->translatedFormat('d M Y, H:i') }}</span>
                    <span>&mdash;</span>
                    <span>{{ $flashSale->end_time->translatedFormat('d M Y, H:i') }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.flash_sales.edit', $flashSale) }}" class="btn-secondary text-xs h-8.5 px-3 rounded-md bg-white/10 text-white hover:bg-white/20 border-white/10 flex items-center gap-1.5">
                    <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                    Edit Jadwal
                </a>
                <form action="{{ route('admin.flash_sales.toggle', $flashSale) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-xs h-8.5 px-3.5 rounded-md font-semibold transition-all flex items-center gap-1.5 {{ $flashSale->is_active ? 'bg-amber-500/20 text-amber-300 hover:bg-amber-500/30 border border-amber-500/30' : 'bg-cyan-700 text-white hover:bg-cyan-800' }}">
                        <i class="fa-solid {{ $flashSale->is_active ? 'fa-pause' : 'fa-play' }} text-[10px]"></i>
                        {{ $flashSale->is_active ? 'Nonaktifkan' : 'Aktifkan Event' }}
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-5 pt-5 border-t border-slate-800">
            <div>
                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wider">Total Produk</p>
                <p class="text-lg font-bold text-white mt-0.5">{{ $flashSale->items->count() }} Item</p>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wider">Total Kuota Stok</p>
                <p class="text-lg font-bold text-white mt-0.5">{{ $flashSale->items->sum('stock_allocated') }} Pcs</p>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wider">Total Terjual</p>
                <p class="text-lg font-bold text-cyan-400 mt-0.5">{{ $flashSale->items->sum('stock_sold') }} Pcs</p>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wider">Sisa Waktu</p>
                <p class="text-lg font-bold text-amber-400 mt-0.5">
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
            <div class="bg-white rounded-xl p-5 shadow-card border border-slate-200/80 sticky top-6"
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
                    <div class="w-7 h-7 rounded-md bg-cyan-50 text-cyan-700 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-xs text-slate-900">Tambah Produk ke Flash Sale</h3>
                        <p class="text-[10px] text-slate-400">Tetapkan kuota dan harga promo khusus</p>
                    </div>
                </div>

                @if($availableProducts->count() > 0)
                <form action="{{ route('admin.flash_sales.items.add', $flashSale) }}" method="POST" class="space-y-3.5">
                    @csrf

                    <div>
                        <label for="product_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Pilih Produk <span class="text-rose-500">*</span>
                        </label>
                        <select id="product_id" name="product_id" required @change="onProductChange($event)" x-model="productId"
                            class="input text-xs rounded-md">
                            <option value="">-- Pilih Produk Tersedia --</option>
                            @foreach($availableProducts as $prod)
                                <option value="{{ $prod->id }}" data-price="{{ $prod->price }}" data-stock="{{ $prod->stock }}">
                                    {{ $prod->name }} &mdash; [{{ $prod->store->name ?? '-' }}] (Rp {{ number_format($prod->price, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <template x-if="originalPrice > 0">
                        <div class="bg-slate-50 p-2.5 rounded-md border border-slate-200 text-xs">
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
                            class="input text-xs rounded-md">

                        <template x-if="discountPct > 0">
                            <div class="mt-1.5 p-2 bg-emerald-50 rounded border border-emerald-200 flex items-center justify-between text-xs">
                                <span class="text-emerald-800 font-semibold flex items-center gap-1">
                                    <i class="fa-solid fa-tags text-[10px]"></i> Diskon: <span x-text="discountPct + '%'"></span>
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
                            class="input text-xs rounded-md">
                        <p class="text-[10px] text-slate-400 mt-1">Batas kuota yang dapat dibeli pembeli pada sesi ini.</p>
                    </div>

                    <div class="pt-1">
                        <button type="submit" class="w-full btn-primary text-xs h-9 rounded-md bg-cyan-700 hover:bg-cyan-800 flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-plus text-[10px]"></i>
                            Daftarkan ke Flash Sale
                        </button>
                    </div>
                </form>
                @else
                <div class="py-8 text-center text-slate-400">
                    <i class="fa-solid fa-circle-check text-xl text-cyan-600 mb-1.5 block"></i>
                    <p class="text-xs text-slate-700 font-medium">Semua produk telah didaftarkan ke sesi ini.</p>
                </div>
                @endif
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-card border border-slate-200/80 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <h3 class="font-bold text-xs sm:text-sm text-slate-900 uppercase tracking-wider">Produk Terdaftar ({{ $flashSale->items->count() }})</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Produk yang akan ditampilkan di etalase Flash Sale beranda</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
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
                            <tr class="hover:bg-slate-50/80 transition-colors" x-data="{ isEditing: false, flashPrice: '{{ (int)$item->flash_sale_price }}', stockAlloc: '{{ $item->stock_allocated }}', isActive: {{ $item->is_active ? 'true' : 'false' }} }">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded-md bg-slate-100 border border-slate-200 overflow-hidden shrink-0">
                                            @if($item->product->image)
                                                <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-400 text-sm">
                                                    <i class="fa-solid fa-box"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('product.show', $item->product) }}" target="_blank" class="font-bold text-slate-900 hover:text-cyan-700 transition-colors truncate block max-w-xs text-xs">
                                                {{ $item->product->name }}
                                            </a>
                                            <p class="text-[10px] text-slate-400 mt-0.5">
                                                Toko: <span class="text-slate-600 font-medium">{{ $item->product->store->name ?? '-' }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-slate-400 line-through">
                                    Rp {{ number_format($item->product->price, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3.5 font-bold">
                                    <template x-if="!isEditing">
                                        <div>
                                            <span class="text-cyan-800 font-bold text-xs">Rp {{ number_format($item->flash_sale_price, 0, ',', '.') }}</span>
                                            <p class="text-[10px] text-cyan-600 font-normal">Hemat Rp {{ number_format($item->product->price - $item->flash_sale_price, 0, ',', '.') }}</p>
                                        </div>
                                    </template>
                                    <template x-if="isEditing">
                                        <input type="number" x-model="flashPrice" class="input text-xs w-28 h-7">
                                    </template>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                        -{{ $item->discount_percentage }}%
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <template x-if="!isEditing">
                                        <div class="w-28 mx-auto">
                                            <div class="flex justify-between text-[10px] text-slate-500 mb-0.5">
                                                <span>{{ $item->stock_sold }} terjual</span>
                                                <span>{{ $item->stock_allocated }} kuota</span>
                                            </div>
                                            <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full transition-all {{ $item->is_sold_out ? 'bg-slate-400' : 'bg-cyan-600' }}"
                                                     style="width:{{ $item->sold_percentage }}%;"></div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="isEditing">
                                        <div class="w-20 mx-auto">
                                            <input type="number" x-model="stockAlloc" class="input text-xs w-full h-7">
                                        </div>
                                    </template>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <template x-if="!isEditing">
                                        <div class="flex items-center justify-center gap-1">
                                            <button type="button" @click="isEditing = true" class="p-1.5 bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 text-slate-600 rounded-md transition-colors border border-slate-200" title="Edit Harga/Kuota">
                                                <i class="fa-solid fa-pen text-xs"></i>
                                            </button>
                                            <form action="{{ route('admin.flash_sales.items.remove', [$flashSale, $item]) }}" method="POST" onsubmit="return confirm('Hapus produk \'{{ $item->product->name }}\' dari Flash Sale?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 rounded-md transition-colors border border-slate-200" title="Hapus dari Flash Sale">
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
                                                <button type="submit" class="p-1.5 bg-cyan-700 text-white hover:bg-cyan-800 rounded-md text-xs" title="Simpan">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                            </form>
                                            <button type="button" @click="isEditing = false" class="p-1.5 bg-slate-200 text-slate-700 hover:bg-slate-300 rounded-md text-xs" title="Batal">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </div>
                                    </template>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                                    Belum ada produk di Flash Sale ini.
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
