<x-app-layout>
    <div class="page-container py-5">
        <div class="max-w-4xl mx-auto mb-5">
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Checkout Pesanan</h1>
            <p class="text-xs text-slate-400 mt-0.5">Konfirmasi alamat pengiriman dan tinjau tagihan pembayaran Anda</p>
        </div>

        <form action="{{ route('customer.order.store') }}" method="POST" class="max-w-4xl mx-auto space-y-4">
            @csrf

            <div class="bg-white rounded-xl border border-slate-200/80 p-5 sm:p-6 shadow-card space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <i class="fa-solid fa-location-dot text-cyan-700 text-xs"></i>
                    <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">1. Alamat Pengiriman</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs bg-slate-50 p-4 rounded-lg border border-slate-200">
                    <div class="font-medium text-slate-800">
                        <p class="font-bold text-slate-900 text-sm">{{ auth()->user()->name }}</p>
                        <p class="text-slate-500 mt-0.5">{{ auth()->user()->email }}</p>
                        <span class="inline-block mt-2 px-2 py-0.5 bg-cyan-50 text-cyan-800 font-semibold rounded text-[10px] border border-cyan-200">
                            Penerima Pesanan
                        </span>
                    </div>
                    <div class="md:col-span-2">
                        <label for="shipping_address" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Alamat Lengkap Tujuan <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="shipping_address" id="shipping_address" rows="3" required
                                  class="input text-xs rounded-md"
                                  placeholder="Contoh: Jl. Kebon Sirih No. 45, Menteng, Jakarta Pusat 10340">{{ old('shipping_address', auth()->user()->address) }}</textarea>
                        @error('shipping_address')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            @php
                $groupedCarts = $carts->groupBy(function($item) {
                    return $item->product->store->name ?? 'Official Store BelanjaIn';
                });
            @endphp

            @foreach($groupedCarts as $storeName => $items)
                <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden shadow-card">
                    <div class="px-4 py-3 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-store text-cyan-700 text-xs"></i>
                            <span class="font-bold text-slate-800 text-xs">{{ $storeName }}</span>
                        </div>
                        <span class="text-[10px] text-cyan-700 font-semibold flex items-center gap-1">
                            <i class="fa-solid fa-truck-fast text-[10px]"></i> Pengiriman Bebas Ongkir
                        </span>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @foreach($items as $item)
                            <div class="p-4 flex items-center gap-4">
                                <div class="w-14 h-14 rounded-lg bg-slate-100 border border-slate-200 shrink-0 overflow-hidden">
                                    @if($item->product->image)
                                        <img src="{{ $item->product->image_url }}" class="w-full h-full object-cover" alt="{{ $item->product->name }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-300 text-lg">
                                            <i class="fa-solid fa-box"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-xs sm:text-sm font-medium text-slate-800 line-clamp-1">{{ $item->product->name }}</h3>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $item->quantity }} unit x Rp {{ number_format($item->product->final_price, 0, ',', '.') }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-slate-900 text-xs sm:text-sm">
                                        Rp {{ number_format($item->product->final_price * $item->quantity, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="bg-white rounded-xl border border-slate-200/80 p-5 sm:p-6 shadow-card space-y-4">
                <h3 class="font-bold text-xs text-slate-900 pb-3 border-b border-slate-100 uppercase tracking-wider">2. Rincian Pembayaran</h3>

                @php
                    $subtotal = $carts->sum(fn($c) => $c->product->final_price * $c->quantity);
                @endphp

                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between text-slate-500">
                        <span>Total Harga Barang ({{ $carts->sum('quantity') }} unit)</span>
                        <span class="font-medium text-slate-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-500">
                        <span>Biaya Pengiriman</span>
                        <span class="font-semibold text-cyan-700">Rp 0 (Gratis)</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-500">
                        <span>Biaya Penanganan Platform</span>
                        <span class="font-semibold text-cyan-700">Rp 0 (Gratis)</span>
                    </div>
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <span class="font-bold text-sm text-slate-900">Total Tagihan Final</span>
                        <span class="font-extrabold text-base sm:text-lg text-slate-900">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <a href="{{ route('customer.cart.index') }}" class="text-xs font-semibold text-slate-500 hover:text-cyan-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-left text-[10px]"></i>
                        Kembali ke Keranjang
                    </a>
                    <button type="submit" class="w-full sm:w-auto btn-primary h-10 px-6 text-xs flex items-center justify-center gap-2 bg-cyan-700 hover:bg-cyan-800">
                        <i class="fa-solid fa-lock text-xs"></i>
                        <span>Konfirmasi & Buat Pesanan</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
