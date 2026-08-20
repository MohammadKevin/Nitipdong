<x-app-layout>
    <div class="page-container py-5">
        <div class="max-w-4xl mx-auto mb-5">
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Checkout Pesanan</h1>
            <p class="text-xs text-slate-400 mt-0.5">Konfirmasi alamat pengiriman dan tinjau tagihan pembayaran Anda</p>
        </div>

        @if(session('error'))
            <div class="max-w-4xl mx-auto mb-4 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-semibold flex items-center gap-2.5 shadow-2xs animate-fade-up">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm shrink-0"></i>
                <span class="flex-1">{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="max-w-4xl mx-auto mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-medium shadow-2xs animate-fade-up">
                <div class="flex items-center gap-2 font-bold text-rose-900 mb-1.5">
                    <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm"></i>
                    <span>Terdapat kendala pada data checkout Anda:</span>
                </div>
                <ul class="list-disc list-inside ml-2 space-y-0.5 text-rose-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('customer.order.store') }}" method="POST"
              x-data="{ isSubmitting: false }"
              @submit="isSubmitting = true"
              class="max-w-4xl mx-auto space-y-4">
            @csrf

            <div class="bg-white rounded-xl border border-slate-200/80 p-5 sm:p-6 shadow-card space-y-4"
                 x-data="{
                    useSaved: {{ count($addresses) > 0 ? 'true' : 'false' }},
                    selectedAddressId: {{ $defaultAddress ? $defaultAddress->id : 'null' }}
                 }">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-cyan-700 text-xs"></i>
                        <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">1. Alamat Pengiriman</h2>
                    </div>
                    @if(count($addresses) > 0)
                        <a href="{{ route('customer.addresses.index') }}" target="_blank" class="text-[11px] font-semibold text-cyan-700 hover:underline flex items-center gap-1">
                            <i class="fa-solid fa-gear text-[10px]"></i> Kelola Buku Alamat
                        </a>
                    @endif
                </div>

                @if(count($addresses) > 0)
                    <div x-show="useSaved" class="space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($addresses as $addr)
                                <label class="border rounded-xl p-3.5 cursor-pointer flex items-start gap-3 transition-all relative"
                                       :class="selectedAddressId == {{ $addr->id }} ? 'border-cyan-600 bg-cyan-50/40 ring-1 ring-cyan-600' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                    <input type="radio" name="address_id" value="{{ $addr->id }}"
                                           :disabled="!useSaved"
                                           x-model="selectedAddressId" class="mt-0.5 text-cyan-600 focus:ring-cyan-500">
                                    <div class="text-xs min-w-0">
                                        <div class="flex items-center gap-1.5 mb-1">
                                            <span class="font-bold text-slate-900">{{ $addr->label }}</span>
                                            @if($addr->is_default)
                                                <span class="px-1.5 py-0.2 bg-cyan-100 text-cyan-800 text-[9px] font-bold rounded">Utama</span>
                                            @endif
                                        </div>
                                        <p class="font-semibold text-slate-800">{{ $addr->recipient_name }} <span class="font-normal text-slate-500 font-mono">({{ $addr->phone }})</span></p>
                                        <p class="text-slate-600 text-[11px] mt-1 line-clamp-2">{{ $addr->full_address }}</p>
                                        @if($addr->city || $addr->province)
                                            <p class="text-[10px] text-slate-400 mt-0.5">{{ implode(', ', array_filter([$addr->city, $addr->province, $addr->postal_code])) }}</p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        @error('address_id')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror

                        <button type="button" @click="useSaved = false; selectedAddressId = null"
                                class="text-xs font-semibold text-slate-600 hover:text-cyan-700 flex items-center gap-1.5 pt-1 cursor-pointer">
                            <i class="fa-solid fa-plus text-[10px]"></i> Tulis Alamat Baru Lainnya
                        </button>
                    </div>

                    <div x-show="!useSaved" x-cloak class="space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs bg-slate-50 p-4 rounded-lg border border-slate-200">
                            <div class="font-medium text-slate-800">
                                <p class="font-bold text-slate-900 text-sm">{{ auth()->user()->name }}</p>
                                <p class="text-slate-500 mt-0.5">{{ auth()->user()->email }}</p>
                                <button type="button" @click="useSaved = true; selectedAddressId = {{ $defaultAddress ? $defaultAddress->id : 'null' }}"
                                        class="mt-3 text-[11px] font-semibold text-cyan-700 hover:underline block cursor-pointer">
                                    ← Pilih Dari Alamat Tersimpan
                                </button>
                            </div>
                            <div class="md:col-span-2">
                                <label for="shipping_address_new" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Alamat Pengiriman Baru <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="shipping_address" id="shipping_address_new" rows="3"
                                          :disabled="useSaved"
                                          class="input text-xs rounded-md"
                                          placeholder="Nama penerima, no. HP, dan alamat lengkap tujuan...">{{ old('shipping_address') }}</textarea>
                                @error('shipping_address')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                @else
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
                @endif
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
                    $finalGrandTotal = $grandTotal ?? max(0, $subtotal - ($voucherDiscount ?? 0));
                @endphp

                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between text-slate-500">
                        <span>Total Harga Barang ({{ $carts->sum('quantity') }} unit)</span>
                        <span class="font-medium text-slate-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if(isset($voucherDiscount) && $voucherDiscount > 0)
                        <div class="flex items-center justify-between text-emerald-600 font-medium">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-ticket text-xs"></i>
                                <span>Diskon Voucher ({{ $appliedVoucher->code ?? 'Kode Promo' }})</span>
                            </span>
                            <span class="font-bold">-Rp {{ number_format($voucherDiscount, 0, ',', '.') }}</span>
                        </div>
                    @endif
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
                        <span class="font-extrabold text-base sm:text-lg text-cyan-900">
                            Rp {{ number_format($finalGrandTotal, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <a href="{{ route('customer.cart.index') }}" class="text-xs font-semibold text-slate-500 hover:text-cyan-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-left text-[10px]"></i>
                        Kembali ke Keranjang
                    </a>
                    <button type="submit"
                            :disabled="isSubmitting"
                            class="w-full sm:w-auto btn-primary h-10 px-6 text-xs flex items-center justify-center gap-2 bg-cyan-700 hover:bg-cyan-800 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer transition-all">
                        <span x-show="!isSubmitting" class="flex items-center gap-2">
                            <i class="fa-solid fa-lock text-xs"></i>
                            <span>Konfirmasi & Buat Pesanan</span>
                        </span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                            <span>Memproses Pesanan...</span>
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
