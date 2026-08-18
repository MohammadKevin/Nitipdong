<x-app-layout>
    <div class="page-container py-6">
        <div class="mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Keranjang Belanja</h1>
            <p class="text-xs text-slate-400 mt-0.5">Periksa produk pilihan Anda sebelum melanjutkan pembayaran</p>
        </div>

        @if(session('success'))
            <div class="mb-4 flex items-center gap-2.5 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-xs font-semibold animate-fade-up">
                <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 flex items-center gap-2.5 px-4 py-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold animate-fade-up">
                <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($carts->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-8 space-y-4">
                @php
                    $groupedCarts = $carts->groupBy(fn($item) => $item->product->store->name ?? 'Toko Resmi');
                @endphp

                @foreach($groupedCarts as $storeName => $items)
                <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden shadow-xs">
                    <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                        <i class="fa-solid fa-store text-emerald-600 text-xs"></i>
                        <span class="font-bold text-xs text-slate-800">{{ $storeName }}</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Official Seller</span>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @foreach($items as $item)
                        <div class="p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
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
                                    <a href="{{ route('product.show', $item->product) }}" class="text-xs sm:text-sm font-semibold text-slate-800 hover:text-emerald-600 line-clamp-2 transition-colors">
                                        {{ $item->product->name }}
                                    </a>
                                    <span class="text-xs sm:text-sm font-bold text-slate-900 block mt-1">
                                        Rp {{ number_format($item->product->final_price, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                                <form action="{{ route('customer.cart.update', $item) }}" method="POST" class="inline-flex items-center rounded-lg border border-slate-300 bg-white p-1">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" name="quantity" value="{{ max(1, $item->quantity - 1) }}" class="w-6 h-6 rounded flex items-center justify-center text-slate-600 hover:bg-slate-100 text-xs">
                                        <i class="fa-solid fa-minus text-[10px]"></i>
                                    </button>
                                    <span class="w-8 text-center text-xs font-bold text-slate-800">{{ $item->quantity }}</span>
                                    <button type="submit" name="quantity" value="{{ min($item->product->stock, $item->quantity + 1) }}" class="w-6 h-6 rounded flex items-center justify-center text-slate-600 hover:bg-slate-100 text-xs">
                                        <i class="fa-solid fa-plus text-[10px]"></i>
                                    </button>
                                </form>

                                <form action="{{ route('customer.cart.destroy', $item) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 transition-colors" title="Hapus dari keranjang">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
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
                <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs space-y-4 sticky top-24">
                    <h3 class="font-bold text-sm text-slate-900 pb-3 border-b border-slate-100">Ringkasan Belanja</h3>

                    @php
                        $subtotal = $carts->sum(fn($c) => $c->product->final_price * $c->quantity);
                    @endphp

                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between text-slate-500">
                            <span>Total Harga ({{ $carts->sum('quantity') }} barang)</span>
                            <span class="font-semibold text-slate-800">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-500">
                            <span>Estimasi Biaya Ongkir</span>
                            <span class="font-semibold text-emerald-600">Gratis (Rp0)</span>
                        </div>
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="font-bold text-sm text-slate-900">Total Tagihan</span>
                            <span class="font-extrabold text-base text-slate-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <a href="{{ route('customer.order.checkout') }}" class="w-full btn-primary py-2.5 text-xs sm:text-sm font-semibold flex items-center justify-center gap-2">
                        <span>Lanjut ke Checkout</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
        @else
        <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center max-w-lg mx-auto shadow-xs">
            <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-400 mx-auto mb-4 text-2xl">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <h2 class="text-base font-bold text-slate-900">Keranjang Belanja Anda Kosong</h2>
            <p class="text-xs text-slate-500 mt-1">Wah, Anda belum memilih produk apa pun untuk dibeli.</p>
            <a href="{{ url('/products') }}" class="mt-5 inline-block btn-primary text-xs px-6 py-2.5">
                Mulai Belanja Sekarang
            </a>
        </div>
        @endif
    </div>
</x-app-layout>
