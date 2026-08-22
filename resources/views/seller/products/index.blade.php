<x-seller-layout>
    <x-slot name="title">
        Katalog Produk Toko - {{ config('app.name', 'NitipDong') }}
    </x-slot>

    <div x-data="{
        selected: [],
        allIds: {{ json_encode($products->pluck('id')) }},
        showDiscountModal: false,
        bulkDiscount: 10,
        toggleSelectAll() {
            if (this.selected.length === this.allIds.length) {
                this.selected = [];
            } else {
                this.selected = [...this.allIds];
            }
        },
        isSelected(id) {
            return this.selected.includes(id);
        },
        toggleSelect(id) {
            if (this.isSelected(id)) {
                this.selected = this.selected.filter(i => i !== id);
            } else {
                this.selected.push(id);
            }
        }
    }">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-3">
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-boxes-stacked text-cyan-700"></i>
                    Katalog Produk Toko
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Kelola seluruh produk, atur harga dasar, diskon promosi, dan status etalase toko.</p>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('seller.products.create') }}" class="btn-primary text-xs h-9 px-4 rounded-xl bg-cyan-700 hover:bg-cyan-800 flex items-center gap-1.5 shadow-xs font-semibold">
                    <i class="fa-solid fa-plus text-[11px]"></i>
                    Tambah Produk
                </a>
            </div>
        </div>

        {{-- Sticky Floating Bulk Action Bar --}}
        <div x-show="selected.length > 0" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="mb-3.5 p-3 sm:px-4 sm:py-3 bg-slate-900 text-white rounded-2xl shadow-xl flex flex-wrap items-center justify-between gap-3 border border-slate-800">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-cyan-500/20 text-cyan-300 font-bold text-xs border border-cyan-500/30">
                    <i class="fa-solid fa-circle-check text-cyan-400 text-xs"></i>
                    <span x-text="selected.length"></span> Produk Dipilih
                </span>
                <span class="text-slate-400 text-xs hidden md:inline">Pilih aksi masal:</span>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                {{-- Form Aktifkan Masal --}}
                <form action="{{ route('seller.products.bulk_action') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="action" value="activate">
                    <template x-for="id in selected" :key="'act-' + id">
                        <input type="hidden" name="product_ids[]" :value="id">
                    </template>
                    <button type="submit" class="h-8 px-3 rounded-lg bg-emerald-600/90 hover:bg-emerald-600 text-white text-xs font-semibold flex items-center gap-1.5 transition-colors">
                        <i class="fa-solid fa-eye text-[11px]"></i>
                        Aktifkan
                    </button>
                </form>

                {{-- Form Nonaktifkan Masal --}}
                <form action="{{ route('seller.products.bulk_action') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="action" value="deactivate">
                    <template x-for="id in selected" :key="'deact-' + id">
                        <input type="hidden" name="product_ids[]" :value="id">
                    </template>
                    <button type="submit" class="h-8 px-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center gap-1.5 transition-colors border border-slate-700">
                        <i class="fa-solid fa-eye-slash text-[11px]"></i>
                        Nonaktifkan
                    </button>
                </form>

                {{-- Modal Trigger Ubah Diskon Masal --}}
                <button type="button" @click="showDiscountModal = true" class="h-8 px-3 rounded-lg bg-amber-500/90 hover:bg-amber-500 text-slate-950 text-xs font-bold flex items-center gap-1.5 transition-colors">
                    <i class="fa-solid fa-percent text-[11px]"></i>
                    Set Diskon
                </button>

                {{-- Form Hapus Masal --}}
                <form action="{{ route('seller.products.bulk_action') }}" method="POST" class="inline"
                      @submit="if(!confirm('Apakah Anda yakin ingin menghapus ' + selected.length + ' produk terpilih secara permanen?')) $event.preventDefault()">
                    @csrf
                    <input type="hidden" name="action" value="delete">
                    <template x-for="id in selected" :key="'del-' + id">
                        <input type="hidden" name="product_ids[]" :value="id">
                    </template>
                    <button type="submit" class="h-8 px-3 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold flex items-center gap-1.5 transition-colors">
                        <i class="fa-solid fa-trash-can text-[11px]"></i>
                        Hapus Masal
                    </button>
                </form>

                <button type="button" @click="selected = []" class="h-8 px-2.5 rounded-lg bg-transparent hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-medium transition-colors" title="Batal Pilih">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>

        {{-- Modal Popup Set Diskon Masal --}}
        <div x-show="showDiscountModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div @click.outside="showDiscountModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-sm w-full p-6 text-slate-800">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-tags"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-slate-900">Set Diskon Masal</h3>
                            <p class="text-[11px] text-slate-400" x-text="selected.length + ' produk akan diperbarui'"></p>
                        </div>
                    </div>
                    <button @click="showDiscountModal = false" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form action="{{ route('seller.products.bulk_action') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="action" value="set_discount">
                    <template x-for="id in selected" :key="'disc-' + id">
                        <input type="hidden" name="product_ids[]" :value="id">
                    </template>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Persentase Diskon (%)
                        </label>
                        <div class="relative">
                            <input type="number" name="discount" x-model.number="bulkDiscount" min="0" max="99" required
                                placeholder="Contoh: 15"
                                class="input text-sm pr-8 font-bold text-cyan-800 rounded-xl">
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 font-bold text-xs text-slate-400">%</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Masukkan 0 untuk menghapus diskon promosi.</p>
                    </div>

                    <div class="pt-2 flex items-center justify-end gap-2">
                        <button type="button" @click="showDiscountModal = false" class="btn-secondary text-xs h-9 px-4 rounded-xl">
                            Batal
                        </button>
                        <button type="submit" class="btn-primary text-xs h-9 px-5 rounded-xl bg-cyan-700 hover:bg-cyan-800 font-semibold shadow-xs">
                            Terapkan Diskon
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-card border border-slate-200/80 overflow-hidden mb-12">
            <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3 bg-slate-50/50">
                <form method="GET" action="{{ route('seller.products.index') }}" class="flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                    <div class="relative flex-1 max-w-sm">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama produk..."
                            class="input text-xs pl-8 pr-4 h-8.5 rounded-lg w-full">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 text-xs"></i>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="btn-secondary text-xs h-8.5 px-3 rounded-lg font-medium">
                            Filter
                        </button>
                        @if(request('search'))
                            <a href="{{ route('seller.products.index') }}" class="text-xs text-slate-500 hover:text-slate-800 px-2 py-1">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
                <div class="text-xs text-slate-500 flex items-center gap-2">
                    <span>Total: <strong class="text-slate-800">{{ count($products) }}</strong> Produk</span>
                </div>
            </div>

            <div class="overflow-x-auto max-h-[calc(100vh-260px)] min-h-[500px] overflow-y-auto border-t border-slate-100">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 sticky top-0 z-10 shadow-xs">
                        <tr>
                            <th class="pl-4 pr-2 py-3.5 bg-slate-50 w-10 text-center">
                                <input type="checkbox"
                                    :checked="selected.length > 0 && selected.length === allIds.length"
                                    :indeterminate="selected.length > 0 && selected.length < allIds.length"
                                    @click="toggleSelectAll()"
                                    class="w-4 h-4 text-cyan-600 rounded border-slate-300 focus:ring-cyan-500 cursor-pointer"
                                    title="Pilih Semua">
                            </th>
                            <th class="px-3 py-3.5 bg-slate-50">Produk</th>
                            <th class="px-4 py-3.5 bg-slate-50">Kategori</th>
                            <th class="px-4 py-3.5 bg-slate-50">Harga Dasar Toko</th>
                            <th class="px-4 py-3.5 bg-slate-50">Harga Tayang Konsumen (+5%)</th>
                            <th class="px-4 py-3.5 text-center bg-slate-50">Stok</th>
                            <th class="px-4 py-3.5 text-center bg-slate-50">Status</th>
                            <th class="px-4 py-3.5 text-center bg-slate-50">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($products as $product)
                        <tr class="hover:bg-slate-50/80 transition-colors" :class="isSelected({{ $product->id }}) ? 'bg-cyan-50/50' : ''">
                            <td class="pl-4 pr-2 py-3.5 text-center">
                                <input type="checkbox"
                                    :checked="isSelected({{ $product->id }})"
                                    @click="toggleSelect({{ $product->id }})"
                                    class="w-4 h-4 text-cyan-600 rounded border-slate-300 focus:ring-cyan-500 cursor-pointer">
                            </td>
                            <td class="px-3 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 overflow-hidden shrink-0 shadow-2xs">
                                        @if($product->image_url)
                                            <img src="{{ $product->image_url }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-400 text-sm">
                                                <i class="fa-solid fa-box"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('product.show', $product) }}" target="_blank" class="font-bold text-slate-900 text-xs hover:text-cyan-700 transition-colors truncate block max-w-xs">
                                            {{ $product->name }}
                                        </a>
                                        <span class="text-[10px] text-slate-400 font-mono">Kode: {{ $product->getRouteKey() }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $product->category->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-bold text-slate-900">Rp {{ number_format($product->seller_price, 0, ',', '.') }}</span>
                                <span class="block text-[10px] text-slate-400">100% diterima toko</span>
                            </td>
                            <td class="px-4 py-3.5">
                                @if($product->discount_percentage > 0)
                                    <div class="flex items-baseline gap-1.5">
                                        <span class="font-bold text-cyan-800">Rp {{ number_format($product->final_price, 0, ',', '.') }}</span>
                                        <span class="text-[10px] text-slate-400 line-through">Rp {{ number_format($product->customer_base_price, 0, ',', '.') }}</span>
                                    </div>
                                    <span class="inline-block text-[9px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-1 rounded mt-0.5">Diskon {{ $product->discount_percentage }}%</span>
                                @else
                                    <span class="font-bold text-cyan-800">Rp {{ number_format($product->customer_base_price, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $product->stock > 5 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ $product->stock }} Unit
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $product->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $product->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('seller.products.edit', $product) }}" class="p-1.5 bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 text-slate-600 rounded-lg transition-colors border border-slate-200" title="Edit Produk">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    <form action="{{ route('seller.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 rounded-lg transition-colors border border-slate-200" title="Hapus Produk">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-slate-400">
                                Belum ada produk di katalog toko Anda.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-seller-layout>
