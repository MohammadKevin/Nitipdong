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
                    $groupedCarts = $carts->groupBy(fn($item) => $item->product->store->name ?? 'Official Store BelanjaIn');
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
                                        <img src="{{ asset('storage/' . $item->product->image) }}" class="w-full h-full object-cover" alt="{{ $item->product->name }}">
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
                <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-card space-y-4 sticky top-20">
                    <h3 class="font-bold text-xs text-slate-900 pb-3 border-b border-slate-100 uppercase tracking-wider">Ringkasan Tagihan</h3>

                    @php
                        $subtotal = $carts->sum(fn($c) => $c->product->final_price * $c->quantity);
                    @endphp

                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between text-slate-500">
                            <span>Total Harga ({{ $carts->sum('quantity') }} unit)</span>
                            <span class="font-medium text-slate-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-500">
                            <span>Ekstra Bebas Ongkir</span>
                            <span class="font-semibold text-cyan-700">Rp 0 (Gratis)</span>
                        </div>
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="font-bold text-xs sm:text-sm text-slate-900">Total Pembayaran</span>
                            <span class="font-extrabold text-base sm:text-lg text-slate-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <a href="{{ route('customer.order.checkout') }}" class="w-full btn-primary h-10 text-xs flex items-center justify-center gap-2 bg-cyan-700 hover:bg-cyan-800">
                        <span>Lanjut ke Checkout</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>
        </div>
        @else
        <div class="bg-white rounded-xl border border-slate-200/80 p-12 text-center max-w-md mx-auto shadow-card">
            <div class="w-14 h-14 bg-cyan-50 border border-cyan-100 rounded-full flex items-center justify-center text-cyan-700 mx-auto mb-3 text-xl">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <h2 class="text-sm sm:text-base font-bold text-slate-900">Keranjang Belanja Masih Kosong</h2>
            <p class="text-xs text-slate-500 mt-1">Pilih produk esensial favorit Anda dari katalog BelanjaIn.</p>
            <a href="{{ url('/products') }}" class="mt-4 inline-block btn-primary text-xs h-9 px-5 bg-cyan-700 hover:bg-cyan-800">
                Mulai Belanja Sekarang
            </a>
        </div>
        @endif
    </div>
</x-app-layout>
