<x-seller-layout>
    <x-slot name="title">
        Detail Pesanan #{{ $order->invoice_number }} - {{ config('app.name', 'NitipDong') }}
    </x-slot>

    <div class="space-y-6" x-data="{
        showShipModal: false,
        showCancelModal: false,
        trackingNumber: '{{ $order->tracking_number ?: ('NDX-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(8))) }}',
        cancelReason: ''
    }">
        {{-- Header Navigation & Action Bar --}}
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center gap-3">
                <a href="{{ route('seller.orders.index') }}" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition-colors">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight font-mono">
                            #{{ $order->invoice_number }}
                        </h1>
                        @php
                            $statusStyles = [
                                'pending'    => 'bg-amber-50 text-amber-700 border-amber-200',
                                'processing' => 'bg-cyan-50 text-cyan-800 border-cyan-200',
                                'shipped'    => 'bg-purple-50 text-purple-700 border-purple-200',
                                'completed'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'cancelled'  => 'bg-rose-50 text-rose-700 border-rose-200',
                            ];
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusStyles[$order->status] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Dibuat pada {{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('orders.invoice', $order) }}" target="_blank"
                   class="px-3 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold flex items-center gap-1.5 transition-colors">
                    <i class="fa-solid fa-file-invoice text-slate-500"></i> Cetak Invoice
                </a>
                <a href="{{ route('orders.shipping_label', $order) }}" target="_blank"
                   class="px-3 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold flex items-center gap-1.5 transition-colors">
                    <i class="fa-solid fa-print text-slate-500"></i> Label Resi
                </a>

                @if($order->status === 'pending')
                    <form action="{{ route('seller.orders.process', $order) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5">
                            <i class="fa-solid fa-box-open"></i> Proses Pesanan
                        </button>
                    </form>
                    <button type="button" @click="showCancelModal = true" class="px-3 py-2 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-semibold transition-colors">
                        Batalkan
                    </button>
                @elseif($order->status === 'processing')
                    <button type="button" @click="showShipModal = true" class="px-4 py-2 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5">
                        <i class="fa-solid fa-truck-fast"></i> Kirim Pesanan
                    </button>
                    <button type="button" @click="showCancelModal = true" class="px-3 py-2 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-semibold transition-colors">
                        Batalkan
                    </button>
                @endif
            </div>
        </div>

        {{-- Main Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column: Items & Payment info --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Ordered Items Table --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-bag-shopping text-cyan-700"></i>
                            Daftar Produk Pesanan ({{ $order->orderItems->count() }} item)
                        </h2>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @foreach($order->orderItems as $item)
                            <div class="p-5 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <img src="{{ $item->product->image_url ?? asset('images/product-placeholder.png') }}"
                                         alt="{{ $item->product->name ?? 'Produk' }}"
                                         class="w-14 h-14 rounded-xl object-cover border border-slate-100 shrink-0">
                                    <div class="min-w-0">
                                        <h3 class="text-xs font-bold text-slate-900 truncate">
                                            {{ $item->product->name ?? 'Produk Dihapus' }}
                                        </h3>
                                        @if($item->variant)
                                            <span class="inline-block mt-0.5 px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-medium">
                                                Varian: {{ $item->variant }}
                                            </span>
                                        @endif
                                        <p class="text-xs text-slate-500 mt-1">
                                            Rp {{ number_format($item->price, 0, ',', '.') }} &times; {{ $item->quantity }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-xs font-bold text-slate-900">
                                        Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Price Calculation Breakdown --}}
                    <div class="bg-slate-50/70 p-5 border-t border-slate-100 space-y-2 text-xs">
                        @php
                            $itemsSubtotal = $order->orderItems->sum(fn($i) => $i->price * $i->quantity);
                        @endphp
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal Produk</span>
                            <span class="font-semibold text-slate-800">Rp {{ number_format($itemsSubtotal, 0, ',', '.') }}</span>
                        </div>
                        @if($order->discount_amount > 0)
                            <div class="flex justify-between text-cyan-700">
                                <span>Diskon Voucher ({{ $order->voucher_code }})</span>
                                <span class="font-semibold">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-slate-600">
                            <span>Ongkos Kirim ({{ $order->shipping_courier ?? 'NDX' }} - {{ $order->shipping_service ?? 'REG' }})</span>
                            <span class="font-semibold text-slate-800">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="pt-2 border-t border-slate-200 flex justify-between text-sm font-bold text-slate-900">
                            <span>Total Tagihan Pesanan</span>
                            <span class="text-cyan-700">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Payment Proof (if uploaded) --}}
                @if($order->payment_proof)
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                        <h2 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-receipt text-cyan-700"></i>
                            Bukti Transfer Pembeli
                        </h2>
                        <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank" class="block max-w-sm rounded-xl overflow-hidden border border-slate-200 hover:opacity-90 transition-opacity">
                            <img src="{{ asset('storage/' . $order->payment_proof) }}" alt="Bukti Transfer" class="w-full h-auto object-cover">
                        </a>
                    </div>
                @endif
            </div>

            {{-- Right Column: Buyer & Shipping Info --}}
            <div class="space-y-6">
                {{-- Buyer Info --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                    <h2 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-user text-cyan-700"></i>
                        Informasi Pembeli
                    </h2>
                    <div class="space-y-2 text-xs">
                        <div>
                            <span class="text-slate-400 block text-[11px]">Nama Pembeli</span>
                            <span class="font-bold text-slate-800">{{ $order->user->name ?? 'Pembeli' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Email</span>
                            <span class="font-medium text-slate-700">{{ $order->user->email ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Nomor Telepon</span>
                            <span class="font-medium text-slate-700">{{ $order->user->phone ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Shipping Details --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                    <h2 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-truck-fast text-cyan-700"></i>
                        Informasi Pengiriman (NDX)
                    </h2>
                    <div class="space-y-3 text-xs">
                        <div>
                            <span class="text-slate-400 block text-[11px]">Kurir & Layanan</span>
                            <span class="font-bold text-slate-800">
                                {{ $order->shipping_courier ?: 'NitipDongExpress' }} - {{ $order->shipping_service ?: 'Reguler' }}
                            </span>
                        </div>

                        <div>
                            <span class="text-slate-400 block text-[11px]">Nomor Resi</span>
                            @if($order->tracking_number)
                                <span class="font-mono font-bold text-cyan-800 bg-cyan-50 px-2 py-0.5 rounded border border-cyan-200 block w-fit mt-0.5">
                                    {{ $order->tracking_number }}
                                </span>
                            @else
                                <span class="text-slate-400 italic">Belum dibuat / menunggu pengiriman</span>
                            @endif
                        </div>

                        <div>
                            <span class="text-slate-400 block text-[11px]">Alamat Tujuan</span>
                            <p class="font-medium text-slate-700 whitespace-pre-line mt-0.5 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                {{ $order->shipping_address }}
                            </p>
                        </div>

                        @if($order->courier)
                            <div>
                                <span class="text-slate-400 block text-[11px]">Kurir Pengantar (Driver)</span>
                                <span class="font-bold text-slate-800 flex items-center gap-1.5 mt-0.5">
                                    <i class="fa-solid fa-motorcycle text-cyan-600"></i> {{ $order->courier->name }} ({{ $order->courier->phone ?? '-' }})
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Ship Order --}}
        <div x-show="showShipModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="showShipModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 text-xs">
                <h3 class="font-bold text-sm text-slate-900 mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-truck-fast text-cyan-700"></i>
                    Konfirmasi Pengiriman Paket
                </h3>
                <p class="text-slate-500 mb-4">
                    Masukkan nomor resi NitipDongExpress (NDX). Nomor resi otomatis telah disiapkan di bawah.
                </p>

                <form action="{{ route('seller.orders.ship', $order) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nomor Resi NDX</label>
                        <input type="text" name="tracking_number" x-model="trackingNumber" required
                               class="w-full py-2.5 px-3 rounded-xl border border-slate-300 font-mono text-xs focus:border-cyan-600 focus:ring-cyan-200">
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="showShipModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 font-semibold hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-cyan-700 text-white font-bold hover:bg-cyan-800 shadow-xs">
                            Kirim Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Cancel Order --}}
        <div x-show="showCancelModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="showCancelModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 text-xs">
                <h3 class="font-bold text-sm text-rose-700 mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    Batalkan Pesanan #{{ $order->invoice_number }}
                </h3>
                <p class="text-slate-500 mb-4">
                    Apakah Anda yakin ingin membatalkan pesanan ini? Stok produk akan secara otomatis dipulihkan ke etalase toko.
                </p>

                <form action="{{ route('seller.orders.cancel', $order) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alasan Pembatalan</label>
                        <textarea name="reason" x-model="cancelReason" required rows="3"
                                  placeholder="Contoh: Stok barang fisik rusak atau tidak memenuhi standar kualitas."
                                  class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs focus:border-rose-500 focus:ring-rose-200"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="showCancelModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 font-semibold hover:bg-slate-50">
                            Kembali
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 text-white font-bold hover:bg-rose-700 shadow-xs">
                            Ya, Batalkan Pesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-seller-layout>
