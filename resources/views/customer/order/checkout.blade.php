<x-app-layout>
    @php
        $subtotal = $carts->sum(fn($c) => $c->product->final_price * $c->quantity);
        $totalItemsCount = $carts->sum('quantity');
        $groupedCarts = $carts->groupBy(fn($item) => $item->product->store_id);
    @endphp

    <div class="page-container py-5"
         x-data="{
            isSubmitting: false,
            useSaved: {{ count($addresses) > 0 ? 'true' : 'false' }},
            selectedAddressId: {{ $defaultAddress ? $defaultAddress->id : 'null' }},
            selectedPaymentMethod: 'qris',
            baseSubtotal: {{ $subtotal }},
            voucherDiscount: {{ $voucherDiscount ?? 0 }},
            courierCosts: {
                @foreach($storeShippingData as $stId => $stData)
                    '{{ $stId }}': {{ $stData['selected_cost'] }},
                @endforeach
            },
            selectedCouriers: {
                @foreach($storeShippingData as $stId => $stData)
                    '{{ $stId }}': '{{ $stData['selected_id'] }}',
                @endforeach
            },
            updateCourierCost(storeId, cost, optionId) {
                this.courierCosts[storeId] = Number(cost);
                this.selectedCouriers[storeId] = optionId;
            },
            getTotalShipping() {
                return Object.values(this.courierCosts).reduce((acc, curr) => acc + Number(curr), 0);
            },
            getGrandTotal() {
                return Math.max(0, this.baseSubtotal - this.voucherDiscount + this.getTotalShipping());
            },
            formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(number).replace('IDR', 'Rp');
            }
         }">

        <div class="max-w-4xl mx-auto mb-5">
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Checkout Pesanan</h1>
            <p class="text-xs text-slate-400 mt-0.5">Konfirmasi alamat pengiriman, opsi kurir, dan tinjau tagihan pembayaran Anda</p>
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
              @submit="isSubmitting = true"
              class="max-w-4xl mx-auto space-y-4">
            @csrf

            {{-- 1. ALAMAT PENGIRIMAN --}}
            <div class="bg-white rounded-xl border border-slate-200/80 p-5 sm:p-6 shadow-card space-y-4">
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

            {{-- 2. RINCIAN BARANG & PILIHAN KURIR PENGIRIMAN PER TOKO --}}
            <div class="space-y-4">
                <div class="flex items-center justify-between px-1">
                    <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-boxes-packing text-cyan-700"></i>
                        <span>2. Rincian Barang & Pilihan Ekspedisi Kurir</span>
                    </h2>
                    <span class="text-xs font-medium text-slate-500">{{ count($groupedCarts) }} Toko ({{ $totalItemsCount }} Barang)</span>
                </div>

                @foreach($groupedCarts as $storeId => $items)
                    @php
                        $storeObj = $items->first()->product->store ?? null;
                        $storeName = $storeObj ? $storeObj->name : 'Official Store BelanjaIn';
                        $shippingInfo = $storeShippingData[$storeId] ?? null;
                    @endphp
                    <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden shadow-card">
                        {{-- Store Header --}}
                        <div class="px-4 py-3 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-store text-cyan-700 text-xs"></i>
                                <span class="font-bold text-slate-800 text-xs">{{ $storeName }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-[11px] text-slate-500 font-medium">
                                <span class="bg-slate-200/70 text-slate-700 px-2 py-0.5 rounded text-[10px] font-semibold">
                                    <i class="fa-solid fa-weight-hanging text-[9px] mr-1"></i>{{ number_format($shippingInfo['weight'] ?? 0.5, 1) }} kg
                                </span>
                            </div>
                        </div>

                        {{-- Product Items in Store --}}
                        <div class="divide-y divide-slate-100">
                            @foreach($items as $item)
                                <div class="p-4 flex items-center gap-3 sm:gap-4">
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
                                        <h3 class="text-xs sm:text-sm font-semibold text-slate-900 line-clamp-1">{{ $item->product->name }}</h3>
                                        @if($item->variant)
                                            <div class="mt-1">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-cyan-50 text-cyan-800 text-[10px] font-semibold border border-cyan-200">
                                                    <i class="fa-solid fa-sliders text-[9px]"></i> {{ $item->variant }}
                                                </span>
                                            </div>
                                        @endif
                                        <p class="text-xs text-slate-400 mt-1">{{ $item->quantity }} unit x Rp {{ number_format($item->product->final_price, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="font-extrabold text-slate-900 text-xs sm:text-sm">
                                            Rp {{ number_format($item->product->final_price * $item->quantity, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Courier Shipping Selection Row --}}
                        @if($shippingInfo && count($shippingInfo['options']) > 0)
                        <div class="p-3.5 sm:p-4 bg-cyan-50/20 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-cyan-100 text-cyan-800 flex items-center justify-center text-xs shrink-0">
                                    <i class="fa-solid fa-truck-fast"></i>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-800 block text-xs">Pilih Opsi Kurir Ekspedisi:</span>
                                    <span class="text-[10px] text-slate-400">Tarif otomatis dihitung sesuai berat dan estimasi pengiriman</span>
                                </div>
                            </div>

                            <div class="sm:w-80">
                                <select name="couriers[{{ $storeId }}]"
                                        @change="
                                            const opt = $event.target.selectedOptions[0];
                                            updateCourierCost('{{ $storeId }}', opt.dataset.cost, opt.value);
                                        "
                                        class="w-full text-xs font-semibold text-slate-800 bg-white border border-slate-300 rounded-xl px-3 py-2 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500 shadow-2xs cursor-pointer">
                                    @foreach($shippingInfo['options'] as $cOpt)
                                        <option value="{{ $cOpt['id'] }}"
                                                data-cost="{{ $cOpt['cost'] }}"
                                                {{ ($shippingInfo['selected_id'] == $cOpt['id']) ? 'selected' : '' }}>
                                            {{ $cOpt['courier_name'] }} - {{ $cOpt['service_name'] }} ({{ $cOpt['etd'] }}) : {{ $cOpt['formatted_cost'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- 3. METODE PEMBAYARAN OTOMATIS & MANUAL --}}
            <div class="bg-white rounded-xl border border-slate-200/80 p-5 sm:p-6 shadow-card space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-credit-card text-cyan-700 text-xs"></i>
                        <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">3. Metode Pembayaran</h2>
                    </div>
                    <span class="text-[11px] font-semibold text-emerald-600 flex items-center gap-1">
                        <i class="fa-solid fa-shield-check text-xs"></i> Aman & Terverifikasi
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($paymentChannels as $pKey => $pChannel)
                        <label class="border rounded-xl p-3.5 cursor-pointer flex flex-col justify-between gap-2 transition-all relative"
                               :class="selectedPaymentMethod === '{{ $pKey }}' ? 'border-cyan-600 bg-cyan-50/40 ring-2 ring-cyan-600/30' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <input type="radio" name="payment_method" value="{{ $pKey }}"
                                           x-model="selectedPaymentMethod" class="text-cyan-600 focus:ring-cyan-500">
                                    <span class="text-xs font-bold text-slate-900">{{ $pChannel['name'] }}</span>
                                </div>
                                <i class="{{ $pChannel['icon'] }} text-cyan-700 text-sm shrink-0"></i>
                            </div>
                            <div class="flex items-center justify-between pt-1">
                                <span class="px-2 py-0.5 rounded text-[9px] font-bold border {{ $pChannel['badge_bg'] }}">
                                    {{ $pChannel['badge'] }}
                                </span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- 4. VOUCHER & PROMO --}}
            <div class="bg-white rounded-xl border border-slate-200/80 p-5 sm:p-6 shadow-card">
                <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-ticket text-cyan-700 text-xs"></i>
                        <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">4. Voucher & Kode Promo</h2>
                    </div>
                </div>

                @if($appliedVoucher)
                    <div class="border border-emerald-200 bg-emerald-50/40 rounded-xl p-3.5 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
                                <i class="fa-solid fa-ticket"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-xs text-slate-900">{{ $appliedVoucher->code }}</div>
                                <div class="text-[11px] text-emerald-700 font-medium truncate">{{ $appliedVoucher->name }}</div>
                            </div>
                        </div>
                        <a href="{{ route('customer.vouchers.index') }}" class="text-cyan-700 hover:text-cyan-800 text-xs font-bold shrink-0 hover:underline">
                            Ganti Voucher
                        </a>
                    </div>
                @else
                    <a href="{{ route('customer.vouchers.index') }}" class="block border border-dashed border-slate-300 rounded-xl p-3 hover:border-cyan-500 hover:bg-cyan-50/30 transition-all group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid fa-ticket text-slate-400 group-hover:text-cyan-600 text-xs"></i>
                                <span class="text-xs font-medium text-slate-700 group-hover:text-cyan-800">Gunakan Voucher Diskon / Potongan Ongkir</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-slate-400 text-xs group-hover:text-cyan-600"></i>
                        </div>
                    </a>
                @endif
            </div>

            {{-- 5. RINCIAN PEMBAYARAN FINAL --}}
            <div class="bg-white rounded-xl border border-slate-200/80 p-5 sm:p-6 shadow-card space-y-4">
                <h3 class="font-bold text-xs text-slate-900 pb-3 border-b border-slate-100 uppercase tracking-wider">5. Rincian Pembayaran</h3>

                <div class="space-y-2.5 text-xs">
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Total Harga Barang ({{ $totalItemsCount }} unit)</span>
                        <span class="font-semibold text-slate-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>

                    @if($voucherDiscount > 0)
                        <div class="flex items-center justify-between text-emerald-600 font-medium">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-ticket text-xs"></i>
                                <span>Potongan Diskon Voucher</span>
                            </span>
                            <span class="font-bold">-Rp {{ number_format($voucherDiscount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between text-slate-600">
                        <span>Total Biaya Pengiriman Kurir</span>
                        <span class="font-semibold text-cyan-800" x-text="formatRupiah(getTotalShipping())">
                            Rp {{ number_format($totalInitialShipping, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-slate-600">
                        <span>Biaya Penanganan Platform</span>
                        <span class="font-semibold text-emerald-600">Rp 0 (Gratis)</span>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-sm text-slate-900 block">Total Tagihan Final</span>
                            <span class="text-[10px] text-slate-400">Sudah termasuk ongkir dan potongan diskon</span>
                        </div>
                        <div class="text-right">
                            <span class="font-extrabold text-lg sm:text-xl text-cyan-900" x-text="formatRupiah(getGrandTotal())">
                                Rp {{ number_format($grandTotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <a href="{{ route('customer.cart.index') }}" class="text-xs font-semibold text-slate-500 hover:text-cyan-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        Kembali ke Keranjang
                    </a>
                    <button type="submit"
                            :disabled="isSubmitting"
                            class="w-full sm:w-auto btn-primary h-10 px-6 text-xs flex items-center justify-center gap-2 bg-cyan-700 hover:bg-cyan-800 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer transition-all shadow-xs">
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

{{-- Cache bust: 20260820113044 --}}
