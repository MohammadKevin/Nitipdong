<x-app-layout>
    <div class="bg-[#f5f5f5] min-h-screen pb-28 pt-4">
        <div class="max-w-[1200px] mx-auto px-4">

            {{-- Flash --}}
            @if(session('success'))
                <div class="mb-3 p-3 bg-[#ecfeff] border border-[#06b6d4]/30 text-[#06b6d4] rounded-sm flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-3 p-3 bg-red-50 border border-red-200 text-red-700 rounded-sm flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            @if($carts->count() > 0)
                {{-- Table Header --}}
                <div class="bg-white shadow-sm mb-2 px-5 py-3.5 hidden md:flex items-center text-sm text-slate-500 rounded-sm">
                    <div class="w-[46%] flex items-center gap-3">
                        <input type="checkbox" id="check-all" class="w-4 h-4 text-[#06b6d4] focus:ring-[#06b6d4] border-slate-300 rounded-sm cursor-pointer">
                        <label for="check-all" class="cursor-pointer select-none">Pilih Semua</label>
                    </div>
                    <div class="w-[16%] text-center">Harga Satuan</div>
                    <div class="w-[18%] text-center">Jumlah</div>
                    <div class="w-[14%] text-center">Subtotal</div>
                    <div class="w-[6%] text-center">Aksi</div>
                </div>

                @php
                    $groupedCarts = $carts->groupBy(fn($item) => $item->product->store->name ?? 'Toko');
                @endphp

                @foreach($groupedCarts as $storeName => $items)
                    <div class="bg-white shadow-sm rounded-sm mb-3">
                        {{-- Store Header --}}
                        <div class="flex items-center gap-3 px-5 py-3 border-b border-slate-100">
                            <input type="checkbox" class="store-check w-4 h-4 text-[#06b6d4] focus:ring-[#06b6d4] border-slate-300 rounded-sm cursor-pointer">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            <span class="font-semibold text-slate-800 text-sm">{{ $storeName }}</span>
                            <a href="#" class="text-[#06b6d4] ml-1 hover:opacity-80">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            </a>
                        </div>

                        {{-- Items --}}
                        @foreach($items as $item)
                            <div class="flex flex-col md:flex-row md:items-center px-5 py-4 gap-4 border-b border-slate-50 last:border-b-0">
                                {{-- Checkbox + Product --}}
                                <div class="flex items-start gap-3 md:w-[46%]">
                                    <input type="checkbox" class="item-check w-4 h-4 text-[#06b6d4] focus:ring-[#06b6d4] border-slate-300 rounded-sm cursor-pointer mt-1 shrink-0">
                                    <a href="{{ route('product.show', $item->product) }}" class="flex gap-3 group flex-1 min-w-0">
                                        <div class="w-16 h-16 bg-slate-100 shrink-0 border border-slate-100 overflow-hidden">
                                            @if($item->product->image)
                                                <img src="{{ asset('storage/' . $item->product->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="{{ $item->product->name }}">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-300 text-xs">N/A</div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-slate-800 line-clamp-2 group-hover:text-[#06b6d4] transition-colors leading-snug">{{ $item->product->name }}</p>
                                            <p class="text-xs text-slate-400 mt-1">Variasi: Default</p>
                                        </div>
                                    </a>
                                </div>

                                {{-- Price (mobile show inline) --}}
                                <div class="md:w-[16%] md:text-center">
                                    <span class="md:hidden text-xs text-slate-500 mr-1">Harga:</span>
                                    <span class="text-sm text-slate-700">Rp{{ number_format($item->product->price, 0, ',', '.') }}</span>
                                </div>

                                {{-- Quantity --}}
                                <div class="md:w-[18%] flex md:justify-center items-center gap-2">
                                    <span class="md:hidden text-xs text-slate-500 mr-1">Jumlah:</span>
                                    <form action="{{ route('customer.cart.update', $item) }}" method="POST" x-data class="flex items-center border border-slate-200 rounded-sm overflow-hidden" @submit.prevent>
                                        @csrf @method('PATCH')
                                        <button type="button"
                                            @click="const inp = $el.closest('form').querySelector('input'); if(inp.value > 1) { inp.value--; $el.closest('form').submit(); }"
                                            class="w-7 h-8 flex items-center justify-center text-slate-500 hover:bg-slate-100 border-r border-slate-200 text-lg font-light transition-colors">−</button>
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                            class="w-10 h-8 text-center border-none focus:ring-0 text-slate-700 text-sm p-0" readonly>
                                        <button type="button"
                                            @click="const inp = $el.closest('form').querySelector('input'); inp.value++; $el.closest('form').submit();"
                                            class="w-7 h-8 flex items-center justify-center text-slate-500 hover:bg-slate-100 border-l border-slate-200 text-lg font-light transition-colors">+</button>
                                        <button type="submit" class="hidden">Update</button>
                                    </form>
                                </div>

                                {{-- Subtotal --}}
                                <div class="md:w-[14%] md:text-center">
                                    <span class="md:hidden text-xs text-slate-500 mr-1">Subtotal:</span>
                                    <span class="text-sm font-semibold text-[#06b6d4]">Rp{{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</span>
                                </div>

                                {{-- Delete --}}
                                <div class="md:w-[6%] flex md:justify-center">
                                    <form action="{{ route('customer.cart.destroy', $item) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-[#06b6d4] transition-colors p-1" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach

                        {{-- Voucher Row --}}
                        <div class="px-5 py-3 border-t border-dashed border-slate-200 flex items-center gap-3 text-sm text-slate-500 bg-[#fafafa]">
                            <svg class="w-4 h-4 text-[#06b6d4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                            <span>Voucher Toko</span>
                            <button class="ml-auto text-[#06b6d4] hover:opacity-80 font-medium text-xs">Masukkan Kode</button>
                        </div>
                    </div>
                @endforeach

                {{-- Promo / Coupon --}}
                <div class="bg-white shadow-sm rounded-sm p-4 flex items-center gap-3 text-sm">
                    <svg class="w-5 h-5 text-[#06b6d4] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    <span class="text-slate-700 font-medium">Voucher BelanjaIn</span>
                    <input type="text" placeholder="Masukkan Kode Voucher" class="ml-auto flex-1 max-w-xs border border-slate-300 rounded-sm px-3 py-1.5 text-sm focus:border-[#06b6d4] focus:ring-1 focus:ring-[#06b6d4]">
                    <button class="px-4 py-1.5 border border-[#06b6d4] text-[#06b6d4] rounded-sm hover:bg-[#ecfeff] font-medium text-sm transition-colors">Gunakan</button>
                </div>

            @else
                {{-- Empty Cart --}}
                <div class="bg-white shadow-sm rounded-sm p-16 text-center">
                    <div class="w-24 h-24 mx-auto mb-4 text-slate-200">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="font-semibold text-slate-700 mb-1">Keranjangmu masih kosong</h3>
                    <p class="text-slate-400 text-sm mb-6">Tambahkan produk impianmu sekarang dan dapatkan penawaran terbaik!</p>
                    <a href="/" class="inline-block px-10 py-2.5 bg-[#06b6d4] hover:bg-[#0891b2] text-white font-semibold rounded-sm transition-colors">Belanja Sekarang</a>
                </div>
            @endif

        </div>
    </div>

    {{-- Sticky Bottom Bar --}}
    @if($carts->count() > 0)
        <div class="fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-slate-200 shadow-[0_-2px_12px_rgba(0,0,0,0.06)]">
            <div class="max-w-[1200px] mx-auto px-5 h-[72px] flex items-center justify-between">
                <div class="flex items-center gap-5 text-sm">
                    <label class="flex items-center gap-2.5 cursor-pointer select-none font-medium text-slate-700">
                        <input type="checkbox" id="bottom-check-all" class="w-4 h-4 text-[#06b6d4] focus:ring-[#06b6d4] border-slate-300 rounded-sm">
                        <span>Pilih Semua <span class="font-normal text-slate-500">({{ $carts->count() }} item)</span></span>
                    </label>
                    <button class="text-[#06b6d4] hover:opacity-80 font-medium">Hapus</button>
                </div>

                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-slate-500">Total <span class="text-slate-700">({{ $carts->sum('quantity') }} Produk):</span></span>
                            <span class="text-2xl font-bold text-[#06b6d4]">Rp{{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-xs text-emerald-600 font-medium text-right">Gratis ongkir untuk semua produk</p>
                    </div>
                    <a href="{{ route('customer.order.checkout') }}"
                        class="px-10 h-12 bg-[#06b6d4] hover:bg-[#0891b2] text-white font-semibold rounded-sm text-base flex items-center justify-center transition-colors shadow-sm">
                        Checkout ({{ $carts->count() }})
                    </a>
                </div>
            </div>
        </div>
    @endif

    <script>
        // Simple checkbox select-all sync
        const sync = (master, items) => {
            document.getElementById(master)?.addEventListener('change', function() {
                document.querySelectorAll(items).forEach(cb => cb.checked = this.checked);
            });
        };
        sync('check-all', '.item-check, .store-check');
        sync('bottom-check-all', '.item-check, .store-check');
    </script>
</x-app-layout>
