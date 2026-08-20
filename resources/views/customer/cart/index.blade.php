<x-app-layout>
    <div class="page-container py-5">
        <div class="mb-4">
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Keranjang Belanja Anda</h1>
            <p class="text-xs text-slate-400 mt-0.5">Tinjau daftar produk pilihan Anda sebelum menyelesaikan pesanan</p>
        </div>

        @if(session('success'))
            <div class="mb-4 flex items-center gap-2.5 px-4 py-3 bg-cyan-50 border border-cyan-200 text-cyan-900 rounded-lg text-xs font-semibold animate-fade-up">
                <i class="fa-solid fa-circle-check text-cyan-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 flex items-center gap-2.5 px-4 py-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-lg text-xs font-semibold animate-fade-up">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($carts->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
            <div class="lg:col-span-8 space-y-4">
                @php
                    $groupedCarts = $carts->groupBy(fn($item) => $item->product->store->name ?? 'Official Store SakserShop');
                @endphp

                @foreach($groupedCarts as $storeName => $items)
                <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden shadow-card">
                    <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/70 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-store text-cyan-700 text-xs"></i>
                            <span class="font-bold text-xs text-slate-800">{{ $storeName }}</span>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-cyan-50 text-cyan-800 border border-cyan-200">Verified Seller</span>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @foreach($items as $item)
                        <div class="p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3.5 min-w-0 flex-1">
                                <div class="w-16 h-16 rounded-lg bg-slate-100 border border-slate-200 overflow-hidden shrink-0">
                                    @if($item->product->image)
                                        <img src="{{ $item->product->image_url }}" class="w-full h-full object-cover" alt="{{ $item->product->name }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-300 text-xl">
                                            <i class="fa-solid fa-box"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('product.show', $item->product) }}" class="text-xs sm:text-sm font-medium text-slate-800 hover:text-cyan-700 line-clamp-2 transition-colors">
                                        {{ $item->product->name }}
                                    </a>
                                    @if($item->variant)
                                        <div class="mt-1">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-cyan-50/80 text-cyan-800 text-[10px] font-semibold border border-cyan-200">
                                                <i class="fa-solid fa-sliders text-[9px]"></i> {{ $item->variant }}
                                            </span>
                                        </div>
                                    @endif
                                    <span class="text-sm font-bold text-slate-900 block mt-1">
                                        Rp {{ number_format($item->product->final_price, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto pt-2.5 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                                <form action="{{ route('customer.cart.update', $item) }}" method="POST" class="inline-flex items-center rounded-md border border-slate-300 bg-white">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" name="quantity" value="{{ max(1, $item->quantity - 1) }}" class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-slate-100 text-xs">
                                        <i class="fa-solid fa-minus text-[10px]"></i>
                                    </button>
                                    <span class="w-8 text-center text-xs font-bold text-slate-800">{{ $item->quantity }}</span>
                                    <button type="submit" name="quantity" value="{{ min($item->product->stock, $item->quantity + 1) }}" class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-slate-100 text-xs">
                                        <i class="fa-solid fa-plus text-[10px]"></i>
                                    </button>
                                </form>

                                <form action="{{ route('customer.cart.destroy', $item) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 transition-colors" title="Hapus Barang">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <div class="lg:col-span-4 space-y-4">
                {{-- Ringkasan Tagihan --}}
                <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-card space-y-4 sticky top-20">
                    <h3 class="font-bold text-xs text-slate-900 pb-3 border-b border-slate-100 uppercase tracking-wider">Ringkasan Tagihan</h3>

                    @php
                        $subtotal = $carts->sum(fn($c) => $c->product->final_price * $c->quantity);
                        $voucherDiscount = 0;

                        if (session('applied_voucher')) {
                            $voucherCode = session('applied_voucher');
                            $appliedVoucher = \App\Models\Voucher::where('code', $voucherCode)->first();
                            if ($appliedVoucher) {
                                if ($appliedVoucher->is_store_voucher) {
                                    $storeItems = $carts->filter(fn($item) => $item->product && $item->product->store_id == $appliedVoucher->store_id);
                                    $applicableSubtotal = $storeItems->sum(fn($item) => $item->product->final_price * $item->quantity);
                                } else {
                                    $applicableSubtotal = $subtotal;
                                }
                                $voucherDiscount = $appliedVoucher->calculateDiscount($applicableSubtotal);
                            }
                        }

                        $finalTotal = max(0, $subtotal - $voucherDiscount);
                    @endphp

                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between text-slate-500">
                            <span>Total Harga ({{ $carts->sum('quantity') }} unit)</span>
                            <span class="font-medium text-slate-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>

                        @if($voucherDiscount > 0)
                        <div class="flex items-center justify-between text-slate-500">
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-ticket text-rose-500 text-[10px]"></i>
                                Diskon Voucher
                            </span>
                            <span class="font-semibold text-rose-600">-Rp {{ number_format($voucherDiscount, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        <div class="flex items-center justify-between text-slate-500">
                            <span>Ekstra Bebas Ongkir</span>
                            <span class="font-semibold text-cyan-700">Rp 0 (Gratis)</span>
                        </div>
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="font-bold text-xs sm:text-sm text-slate-900">Total Pembayaran</span>
                            <span class="font-extrabold text-base sm:text-lg text-slate-900">Rp {{ number_format($finalTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <a href="{{ route('customer.order.checkout') }}" class="w-full h-10 text-xs flex items-center justify-center gap-2 bg-cyan-700 hover:bg-cyan-800 text-white rounded-lg font-semibold transition-colors">
                        <span>Konfirmasi & Buat Pesanan</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>
        </div>
        @else
        {{-- Empty Cart Premium Showcase --}}
        <div class="space-y-10 py-4">

            {{-- Main Empty State Hero Card --}}
            <div class="bg-gradient-to-b from-white via-white to-cyan-50/40 rounded-3xl border border-slate-200/90 p-8 sm:p-12 text-center max-w-2xl mx-auto shadow-xl relative overflow-hidden">
                {{-- Decorative Soft Blobs --}}
                <div class="absolute -top-16 -left-16 w-40 h-40 bg-cyan-200/30 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute -bottom-16 -right-16 w-40 h-40 bg-amber-200/30 rounded-full blur-2xl pointer-events-none"></div>

                {{-- Animated Cart Graphic --}}
                <div class="relative w-24 h-24 sm:w-28 sm:h-28 mx-auto mb-5">
                    <div class="w-full h-full rounded-3xl bg-gradient-to-tr from-cyan-600 to-cyan-400 text-white flex items-center justify-center text-4xl shadow-lg shadow-cyan-600/30 transform -rotate-3 hover:rotate-0 transition-transform duration-300">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <div class="absolute -top-2 -right-2 w-8 h-8 rounded-full bg-amber-400 text-amber-950 flex items-center justify-center text-sm shadow-md animate-bounce">
                        <i class="fa-solid fa-sparkles"></i>
                    </div>
                </div>

                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    Wah, Keranjang Belanjamu Masih Kosong!
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto mt-2 leading-relaxed">
                    Yuk, temukan jutaan produk original impianmu dengan harga termurah, promo flash sale harian, dan voucher gratis ongkir Rp0 ke seluruh Indonesia.
                </p>

                {{-- CTA Actions --}}
                <div class="mt-7 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ url('/products') }}"
                       class="h-11 px-6 bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-cyan-700/25 flex items-center gap-2 hover:scale-105 active:scale-95 transition-all">
                        <i class="fa-solid fa-bag-shopping text-sm"></i>
                        <span>Mulai Belanja Sekarang</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                    <a href="{{ url('/products?flash_sale=1') }}"
                       class="h-11 px-5 bg-amber-500 hover:bg-amber-600 text-amber-950 font-bold text-xs sm:text-sm rounded-xl shadow-sm flex items-center gap-2 hover:scale-105 active:scale-95 transition-all">
                        <i class="fa-solid fa-bolt text-xs"></i>
                        <span>Lihat Promo Flash Sale</span>
                    </a>
                </div>

                {{-- Trust Value Highlights --}}
                <div class="mt-8 pt-6 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-3 text-left">
                    <div class="flex items-center gap-2.5 p-2 rounded-xl bg-white border border-slate-100 shadow-2xs">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs shrink-0">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-slate-800 leading-none">100% Original</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Garansi Toko Resmi</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5 p-2 rounded-xl bg-white border border-slate-100 shadow-2xs">
                        <div class="w-8 h-8 rounded-lg bg-cyan-50 text-cyan-600 flex items-center justify-center text-xs shrink-0">
                            <i class="fa-solid fa-truck-fast"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-slate-800 leading-none">Gratis Ongkir</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Seluruh Indonesia</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5 p-2 rounded-xl bg-white border border-slate-100 shadow-2xs">
                        <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-xs shrink-0">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-slate-800 leading-none">Transaksi Aman</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">QRIS & VA Duitku</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recommended Products Carousel / Grid --}}
            @if(isset($recommendedProducts) && $recommendedProducts->count() > 0)
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-5 bg-cyan-600 rounded-full"></div>
                        <h3 class="font-bold text-base text-slate-900 tracking-tight">Rekomendasi Produk Pilihan Untuk Anda</h3>
                    </div>
                    <a href="{{ url('/products') }}" class="text-xs font-bold text-cyan-700 hover:text-cyan-800 flex items-center gap-1 hover:underline">
                        <span>Lihat Semua</span>
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3.5 sm:gap-4">
                    @foreach($recommendedProducts as $prod)
                    <div class="bg-white rounded-2xl border border-slate-200/90 overflow-hidden shadow-xs hover:shadow-card hover:border-cyan-300 transition-all group flex flex-col justify-between">
                        <a href="{{ route('product.show', $prod) }}" class="block">
                            <div class="relative aspect-square overflow-hidden bg-slate-100">
                                <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.src='/img/icon.jpg'">
                                @if($prod->has_discount)
                                    <div class="absolute top-2 left-2 bg-rose-600 text-white text-[10px] font-black px-1.5 py-0.5 rounded-md shadow-xs">
                                        -{{ $prod->discount_percentage_effective }}%
                                    </div>
                                @endif
                                <div class="absolute bottom-2 left-2 px-1.5 py-0.5 rounded text-[9px] font-bold bg-white/90 backdrop-blur-xs text-slate-700 shadow-2xs flex items-center gap-1">
                                    <i class="fa-solid fa-store text-[8px] text-cyan-600"></i>
                                    <span class="truncate max-w-[90px]">{{ $prod->store->name ?? 'Official Store' }}</span>
                                </div>
                            </div>
                            <div class="p-3">
                                <h4 class="text-xs font-semibold text-slate-800 line-clamp-2 group-hover:text-cyan-700 transition-colors leading-snug">
                                    {{ $prod->name }}
                                </h4>
                                <div class="mt-2">
                                    <span class="font-extrabold text-sm text-cyan-800 block">
                                        Rp {{ number_format($prod->final_price, 0, ',', '.') }}
                                    </span>
                                    @if($prod->has_discount)
                                        <span class="text-[10px] text-slate-400 line-through">
                                            Rp {{ number_format($prod->price, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between text-[10px] text-slate-400 mt-2 pt-2 border-t border-slate-100">
                                    <span class="flex items-center gap-0.5 text-amber-500 font-bold">
                                        <i class="fa-solid fa-star text-[9px]"></i> 4.8
                                    </span>
                                    <span>Terjual {{ $prod->formatted_sold_count }}</span>
                                </div>
                            </div>
                        </a>
                        <div class="p-3 pt-0">
                            <a href="{{ route('product.show', $prod) }}"
                               class="w-full h-8 rounded-xl bg-cyan-50 hover:bg-cyan-700 text-cyan-700 hover:text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-2xs">
                                <i class="fa-solid fa-cart-plus text-[11px]"></i>
                                <span>Lihat Produk</span>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
        @endif
    </div>
</x-app-layout>

{{-- Cache bust: 20260820113027 --}}
