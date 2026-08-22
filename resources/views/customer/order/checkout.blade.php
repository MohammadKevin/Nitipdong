<x-app-layout>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    @php
        $subtotal = $carts->sum(fn($c) => $c->product->final_price * $c->quantity);
        $totalItemsCount = $carts->sum('quantity');
        $groupedCarts = $carts->groupBy(fn($item) => $item->product->store_id);
    @endphp

    <div class="page-container py-5"
         x-data="{
            isSubmitting: false,
            isRecalculatingShipping: false,
            showAddressModal: false,
            showAddAddressModal: false,
            useSaved: {{ count($addresses) > 0 ? 'true' : 'false' }},
            selectedAddressId: {{ $defaultAddress ? $defaultAddress->id : 'null' }},
            activeAddress: {{ $defaultAddress ? $defaultAddress->toJson() : 'null' }},
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
            storeShippingOptions: {
                @foreach($storeShippingData as $stId => $stData)
                    '{{ $stId }}': {{ json_encode($stData['options']) }},
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
            },
            selectAddress(addr) {
                this.selectedAddressId = addr.id;
                this.activeAddress = addr;
                this.useSaved = true;
                this.showAddressModal = false;
                this.recalculateShipping(addr.id, addr.city);
            },
            async recalculateShipping(addressId, city) {
                this.isRecalculatingShipping = true;
                try {
                    const response = await fetch('{{ route('customer.shipping.calculate_options') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            address_id: addressId,
                            city: city
                        })
                    });
                    const res = await response.json();
                    if (res.status === 'success' && res.storeShippingData) {
                        for (const [storeId, sData] of Object.entries(res.storeShippingData)) {
                            this.storeShippingOptions[storeId] = sData.options;
                            this.courierCosts[storeId] = sData.selected_cost;
                            this.selectedCouriers[storeId] = sData.selected_id;
                        }
                    }
                } catch (e) {
                    console.error('Gagal menghitung ulang ongkir:', e);
                } finally {
                    this.isRecalculatingShipping = false;
                }
            },
            provincesData: {{ json_encode(\App\Services\IndonesianRegionService::PROVINCES_DATA) }},
            // Map Modal properties
            newAddr: {
                label: 'Rumah',
                recipient_name: '{{ auth()->user()->name }}',
                phone: '{{ auth()->user()->phone ?? '' }}',
                full_address: '',
                city: 'Jakarta Pusat',
                district: '',
                province: 'DKI Jakarta',
                postal_code: '10110',
                latitude: '-6.2088',
                longitude: '106.8456',
                notes: '',
                is_default: true
            },
            getAvailableCities() {
                if (this.newAddr.province && this.provincesData[this.newAddr.province]) {
                    return Object.keys(this.provincesData[this.newAddr.province].cities);
                }
                return [];
            },
            onProvinceChange() {
                const prov = this.newAddr.province;
                const cities = this.getAvailableCities();
                if (cities.length > 0 && !cities.includes(this.newAddr.city)) {
                    this.newAddr.city = cities[0];
                }
                this.onCityChange();
            },
            onCityChange() {
                const prov = this.newAddr.province;
                const city = this.newAddr.city;
                if (prov && city && this.provincesData[prov] && this.provincesData[prov].cities[city]) {
                    const cityInfo = this.provincesData[prov].cities[city];
                    this.newAddr.postal_code = cityInfo.postal || this.newAddr.postal_code;
                    this.newAddr.latitude = cityInfo.lat.toFixed(6);
                    this.newAddr.longitude = cityInfo.lng.toFixed(6);

                    if (this.mapObj && this.markerObj) {
                        this.mapObj.flyTo([cityInfo.lat, cityInfo.lng], 13);
                        this.markerObj.setLatLng([cityInfo.lat, cityInfo.lng]);
                    }
                }
            },
            mapObj: null,
            markerObj: null,
            isLocating: false,
            searchQuery: '',
            searchResults: [],
            isSearching: false,
            openAddModal() {
                this.showAddressModal = false;
                this.showAddAddressModal = true;
                this.$nextTick(() => {
                    this.initCheckoutMap();
                });
            },
            initCheckoutMap() {
                const lat = parseFloat(this.newAddr.latitude) || -6.2088;
                const lng = parseFloat(this.newAddr.longitude) || 106.8456;
                if (this.mapObj) {
                    this.mapObj.remove();
                }
                this.mapObj = L.map('checkout-map').setView([lat, lng], 14);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(this.mapObj);

                this.markerObj = L.marker([lat, lng], { draggable: true }).addTo(this.mapObj);
                this.markerObj.on('dragend', (e) => {
                    const pos = e.target.getLatLng();
                    this.newAddr.latitude = pos.lat.toFixed(6);
                    this.newAddr.longitude = pos.lng.toFixed(6);
                    this.reverseGeocode(pos.lat, pos.lng);
                });
            },
            getCurrentLocation() {
                if (!navigator.geolocation) return;
                this.isLocating = true;
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        this.isLocating = false;
                        const lat = pos.coords.latitude;
                        const lng = pos.coords.longitude;
                        this.newAddr.latitude = lat.toFixed(6);
                        this.newAddr.longitude = lng.toFixed(6);
                        if (this.mapObj && this.markerObj) {
                            this.mapObj.setView([lat, lng], 16);
                            this.markerObj.setLatLng([lat, lng]);
                        }
                        this.reverseGeocode(lat, lng);
                    },
                    () => { this.isLocating = false; },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            },
            searchLocation(q) {
                if (!q || q.length < 3) { this.searchResults = []; return; }
                this.isSearching = true;
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q + ', Indonesia')}&addressdetails=1&limit=5`)
                    .then(r => r.json())
                    .then(data => { this.searchResults = data; this.isSearching = false; })
                    .catch(() => { this.isSearching = false; });
            },
            pickSearchResult(res) {
                const lat = parseFloat(res.lat);
                const lng = parseFloat(res.lon);
                this.newAddr.latitude = lat.toFixed(6);
                this.newAddr.longitude = lng.toFixed(6);
                if (this.mapObj && this.markerObj) {
                    this.mapObj.setView([lat, lng], 16);
                    this.markerObj.setLatLng([lat, lng]);
                }
                const addr = res.address || {};
                this.newAddr.city = addr.city || addr.town || addr.municipality || addr.county || '';
                this.newAddr.district = addr.suburb || addr.neighbourhood || addr.quarter || '';
                this.newAddr.province = addr.state || '';
                this.newAddr.postal_code = addr.postcode || '';
                this.newAddr.full_address = res.display_name;
                this.searchResults = [];
                this.searchQuery = '';
            },
            reverseGeocode(lat, lng) {
                fetch(`{{ route('api.regions.reverse_geocode') }}?lat=${lat}&lng=${lng}`)
                    .then(r => r.json())
                    .then(res => {
                        if (res.success && res.data) {
                            const data = res.data;
                            if (data.city) this.newAddr.city = data.city;
                            if (data.district) this.newAddr.district = data.district;
                            if (data.province) this.newAddr.province = data.province;
                            if (data.postal_code) this.newAddr.postal_code = data.postal_code;
                            if (data.street && !this.newAddr.full_address) {
                                this.newAddr.full_address = data.street;
                            } else if (data.display_name && !this.newAddr.full_address) {
                                this.newAddr.full_address = data.display_name;
                            }
                        }
                    }).catch(() => {});
            },
            async saveNewAddress() {
                try {
                    const resp = await fetch('{{ route('customer.addresses.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.newAddr)
                    });
                    const res = await resp.json();
                    if (res.status === 'success' && res.data) {
                        this.showAddAddressModal = false;
                        this.selectAddress(res.data);
                    }
                } catch (e) {
                    alert('Gagal menyimpan alamat: ' + e.message);
                }
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

            {{-- 1. ALAMAT PENGIRIMAN (REALTIME INTERACTIVE CARD) --}}
            <div class="bg-white rounded-xl border border-slate-200/80 p-5 sm:p-6 shadow-card space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-cyan-700 text-xs"></i>
                        <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">1. Alamat Pengiriman</h2>
                    </div>
                    <div class="flex items-center gap-3">
                        @if(count($addresses) > 0)
                            <button type="button" @click="showAddressModal = true"
                                    class="text-xs font-bold text-cyan-700 hover:text-cyan-800 flex items-center gap-1.5 bg-cyan-50/70 hover:bg-cyan-100/70 px-3 py-1.5 rounded-lg border border-cyan-200 transition-all cursor-pointer">
                                <i class="fa-solid fa-arrows-rotate text-xs"></i>
                                <span>Pilih Alamat Lain</span>
                            </button>
                        @endif
                        <button type="button" @click="openAddModal()"
                                class="text-xs font-bold text-slate-700 hover:text-cyan-700 flex items-center gap-1 cursor-pointer">
                            <i class="fa-solid fa-plus text-[10px]"></i> Alamat Baru
                        </button>
                    </div>
                </div>

                {{-- Active Address Card Display --}}
                <div x-show="useSaved && activeAddress" class="space-y-3">
                    <input type="hidden" name="address_id" :value="selectedAddressId">

                    <div class="p-4 rounded-xl border-2 border-cyan-600 bg-cyan-50/30 relative">
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="font-extrabold text-slate-900 text-xs uppercase" x-text="activeAddress ? activeAddress.label : 'Alamat'"></span>
                                <template x-if="activeAddress && activeAddress.is_default">
                                    <span class="px-2 py-0.2 bg-cyan-100 text-cyan-800 text-[9px] font-bold rounded">Utama</span>
                                </template>
                                <template x-if="activeAddress && activeAddress.latitude">
                                    <span class="px-2 py-0.2 bg-emerald-100 text-emerald-800 text-[9px] font-bold rounded flex items-center gap-1">
                                        <i class="fa-solid fa-location-crosshairs text-[8px]"></i> Pinpoint GPS
                                    </span>
                                </template>
                            </div>
                            <span class="text-[11px] font-semibold text-cyan-800 flex items-center gap-1">
                                <i class="fa-solid fa-circle-check text-xs text-cyan-600"></i> Alamat Terpilih
                            </span>
                        </div>

                        <p class="font-bold text-slate-900 text-sm">
                            <span x-text="activeAddress ? activeAddress.recipient_name : ''"></span>
                            <span class="font-mono font-normal text-slate-500 text-xs ml-1" x-text="activeAddress ? '(' + activeAddress.phone + ')' : ''"></span>
                        </p>
                        <p class="text-slate-700 text-xs mt-1 leading-relaxed" x-text="activeAddress ? activeAddress.full_address : ''"></p>
                        
                        <template x-if="activeAddress && (activeAddress.district || activeAddress.city || activeAddress.province)">
                            <p class="text-[11px] text-slate-500 font-medium mt-1 flex items-center gap-1">
                                <i class="fa-solid fa-map-pin text-cyan-600 text-[10px]"></i>
                                <span x-text="[activeAddress.district, activeAddress.city, activeAddress.province, activeAddress.postal_code].filter(Boolean).join(', ')"></span>
                            </p>
                        </template>

                        <template x-if="activeAddress && activeAddress.notes">
                            <div class="mt-2 p-2 bg-white/80 rounded-lg border border-slate-200 text-[11px] text-slate-600 flex items-start gap-1.5">
                                <i class="fa-solid fa-circle-info text-cyan-600 text-[10px] mt-0.5 shrink-0"></i>
                                <span><strong>Catatan Kurir:</strong> <span x-text="activeAddress.notes"></span></span>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Manual Address Fallback --}}
                <div x-show="!useSaved" x-cloak class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <div class="font-medium text-slate-800">
                            <p class="font-bold text-slate-900 text-sm">{{ auth()->user()->name }}</p>
                            <p class="text-slate-500 mt-0.5">{{ auth()->user()->email }}</p>
                            @if(count($addresses) > 0)
                                <button type="button" @click="useSaved = true; selectedAddressId = {{ $defaultAddress ? $defaultAddress->id : 'null' }}; activeAddress = {{ $defaultAddress ? $defaultAddress->toJson() : 'null' }}"
                                        class="mt-3 text-[11px] font-semibold text-cyan-700 hover:underline block cursor-pointer">
                                    ← Pilih Dari Alamat Tersimpan
                                </button>
                            @endif
                        </div>
                        <div class="md:col-span-2">
                            <label for="shipping_address_manual" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Alamat Pengiriman Lengkap Tujuan <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="shipping_address" id="shipping_address_manual" rows="3"
                                      :disabled="useSaved"
                                      class="input text-xs rounded-md"
                                      placeholder="Nama penerima, no. HP, dan alamat lengkap tujuan...">{{ old('shipping_address', auth()->user()->address) }}</textarea>
                        </div>
                    </div>
                </div>
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
                        $storeName = $storeObj ? $storeObj->name : 'Official Store NitipDong';
                        $shippingInfo = $storeShippingData[$storeId] ?? null;
                        $storeOriginCity = $shippingInfo['origin_city'] ?? ($storeObj?->effective_city ?? 'Jakarta Pusat');
                    @endphp
                    <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden shadow-card">
                        {{-- Store Header with Origin Location --}}
                        <div class="px-4 py-3 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-2 flex-wrap">
                                <i class="fa-solid fa-store text-cyan-700 text-xs"></i>
                                <span class="font-bold text-slate-800 text-xs">{{ $storeName }}</span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white border border-slate-200 text-[10px] font-semibold text-slate-600 shadow-2xs">
                                    <i class="fa-solid fa-location-dot text-cyan-600 text-[9px]"></i>
                                    <span>Dikirim dari: <strong class="text-slate-800">{{ $storeOriginCity }}</strong></span>
                                </span>
                            </div>
                            <div class="flex items-center gap-2 text-[11px] text-slate-500 font-medium">
                                <span class="bg-slate-200/70 text-slate-700 px-2 py-0.5 rounded text-[10px] font-semibold">
                                    <i class="fa-solid fa-weight-hanging text-[9px] mr-1"></i>{{ number_format($shippingInfo['weight'] ?? 0.5, 1) }} kg
                                </span>
                            </div>
                        </div>

                        {{-- Same City Free Shipping Notice Banner --}}
                        <template x-if="storeShippingOptions['{{ $storeId }}'] && storeShippingOptions['{{ $storeId }}'][0] && storeShippingOptions['{{ $storeId }}'][0].is_same_city">
                            <div class="mx-4 mt-3 p-3 bg-gradient-to-r from-emerald-500/10 via-teal-500/10 to-cyan-500/10 border border-emerald-300 text-emerald-900 rounded-xl text-xs flex items-center justify-between gap-3 shadow-2xs">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-xs shrink-0 shadow-xs">
                                        <i class="fa-solid fa-gift"></i>
                                    </div>
                                    <div>
                                        <span class="font-black text-slate-900 block">🎉 Promo Gratis Ongkir 1 Kota Aktif!</span>
                                        <span class="text-[11px] text-slate-600">Alamat tujuan dan toko berada dalam 1 kota. Seluruh kurir otomatis <strong>Rp 0</strong>.</span>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 bg-emerald-600 text-white font-extrabold text-[10px] rounded-lg shrink-0 shadow-xs tracking-wider uppercase">
                                    GRATIS ONGKIR Rp0
                                </span>
                            </div>
                        </template>

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
                        <div class="p-3.5 sm:p-4 bg-cyan-50/20 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-cyan-100 text-cyan-800 flex items-center justify-center text-xs shrink-0">
                                    <i class="fa-solid fa-truck-fast"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-800 text-xs">Pilih Opsi Kurir Ekspedisi:</span>
                                        <span x-show="isRecalculatingShipping" x-cloak class="text-[10px] text-cyan-700 font-semibold flex items-center gap-1 animate-pulse">
                                            <i class="fa-solid fa-circle-notch fa-spin text-[9px]"></i> Menghitung ulang tarif...
                                        </span>
                                    </div>
                                    <span class="text-[10px] text-slate-400">Tarif dihitung otomatis sesuai berat dan lokasi asal toko & tujuan</span>
                                </div>
                            </div>

                            <div class="sm:w-88">
                                <select name="couriers[{{ $storeId }}]"
                                        @change="
                                            const opt = $event.target.selectedOptions[0];
                                            updateCourierCost('{{ $storeId }}', opt.dataset.cost, opt.value);
                                        "
                                        class="w-full text-xs font-semibold text-slate-800 bg-white border border-slate-300 rounded-xl px-3 py-2.5 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500 shadow-2xs cursor-pointer">
                                    <template x-for="cOpt in storeShippingOptions['{{ $storeId }}'] || []" :key="cOpt.id">
                                        <option :value="cOpt.id"
                                                :data-cost="cOpt.cost"
                                                :selected="selectedCouriers['{{ $storeId }}'] === cOpt.id"
                                                x-text="cOpt.courier_name + ' - ' + cOpt.service_name + ' (' + cOpt.etd + ') : ' + (cOpt.is_free_shipping ? 'Rp 0 (GRATIS ONGKIR 1 KOTA)' : cOpt.formatted_cost)">
                                        </option>
                                    </template>
                                </select>
                            </div>
                        </div>
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
                            :disabled="isSubmitting || isRecalculatingShipping"
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

        {{-- MODAL PILIH ALAMAT LAIN (QUICK SWITCHER) --}}
        <div x-show="showAddressModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.outside="showAddressModal = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-2xl border border-slate-200 my-4 max-h-[85vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-map-location-dot text-cyan-600 text-base"></i>
                        <h3 class="font-bold text-sm text-slate-900">Pilih Alamat Pengiriman</h3>
                    </div>
                    <button @click="showAddressModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <div class="mt-4 space-y-3">
                    @foreach($addresses as $addr)
                        <div @click="selectAddress({{ $addr->toJson() }})"
                             class="p-4 rounded-xl border cursor-pointer transition-all relative"
                             :class="selectedAddressId == {{ $addr->id }} ? 'border-cyan-600 bg-cyan-50/40 ring-1 ring-cyan-600' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-900 text-xs uppercase">{{ $addr->label }}</span>
                                    @if($addr->is_default)
                                        <span class="px-1.5 py-0.2 bg-cyan-100 text-cyan-800 text-[9px] font-bold rounded">Utama</span>
                                    @endif
                                    @if($addr->latitude)
                                        <span class="px-1.5 py-0.2 bg-emerald-100 text-emerald-800 text-[9px] font-bold rounded flex items-center gap-1">
                                            <i class="fa-solid fa-location-crosshairs text-[8px]"></i> Pinpoint
                                        </span>
                                    @endif
                                </div>
                                <template x-if="selectedAddressId == {{ $addr->id }}">
                                    <span class="text-cyan-700 font-bold text-xs"><i class="fa-solid fa-check"></i> Terpilih</span>
                                </template>
                            </div>
                            <p class="font-semibold text-slate-800 text-xs">{{ $addr->recipient_name }} <span class="text-slate-500 font-mono">({{ $addr->phone }})</span></p>
                            <p class="text-slate-600 text-[11px] mt-1 line-clamp-2">{{ $addr->full_address }}</p>
                            @if($addr->city || $addr->province)
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ implode(', ', array_filter([$addr->district, $addr->city, $addr->province, $addr->postal_code])) }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                    <button type="button" @click="openAddModal()"
                            class="text-xs font-bold text-cyan-700 hover:text-cyan-800 flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-plus text-xs"></i> Tambah Alamat Baru
                    </button>
                    <button type="button" @click="showAddressModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        {{-- MODAL TAMBAH ALAMAT BARU DENGAN LEAFLET GPS & PINPOINT --}}
        <div x-show="showAddAddressModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.outside="showAddAddressModal = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-2xl max-w-xl w-full p-5 sm:p-6 shadow-2xl border border-slate-200 my-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-map-location-dot text-cyan-600 text-base"></i>
                        <h3 class="font-bold text-sm sm:text-base text-slate-900">Tambah Alamat Pengiriman Baru</h3>
                    </div>
                    <button @click="showAddAddressModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                {{-- Interactive Map Pinpoint Section --}}
                <div class="mt-4 space-y-3">
                    <div class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-center justify-between">
                        <div class="relative flex-1">
                            <input type="text" x-model="searchQuery"
                                   @input.debounce.500ms="searchLocation(searchQuery)"
                                   placeholder="Cari jalan, kelurahan, kecamatan, atau kota..."
                                   class="w-full h-9 rounded-xl border border-slate-300 text-xs px-3 pl-8 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                            <div x-show="isSearching" class="absolute right-3 top-2.5 text-cyan-600 text-xs">
                                <i class="fa-solid fa-circle-notch fa-spin"></i>
                            </div>

                            {{-- Autocomplete Dropdown --}}
                            <div x-show="searchResults.length > 0" x-cloak
                                 class="absolute z-20 top-10 left-0 right-0 bg-white rounded-xl shadow-xl border border-slate-200 divide-y divide-slate-100 max-h-48 overflow-y-auto text-xs">
                                <template x-for="res in searchResults" :key="res.place_id">
                                    <button type="button" @click="pickSearchResult(res)"
                                            class="w-full p-2.5 text-left hover:bg-cyan-50/70 flex items-start gap-2 cursor-pointer transition-colors">
                                        <i class="fa-solid fa-location-dot text-cyan-600 text-xs mt-0.5 shrink-0"></i>
                                        <span class="truncate" x-text="res.display_name"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <button type="button" @click="getCurrentLocation()"
                                :disabled="isLocating"
                                class="h-9 px-3.5 bg-cyan-50 text-cyan-800 hover:bg-cyan-100 rounded-xl border border-cyan-200 text-xs font-semibold flex items-center justify-center gap-1.5 shrink-0 cursor-pointer transition-all">
                            <i class="fa-solid fa-location-crosshairs text-cyan-700" :class="isLocating ? 'animate-spin' : ''"></i>
                            <span x-text="isLocating ? 'Mendeteksi...' : 'Lokasi Saya'"></span>
                        </button>
                    </div>

                    {{-- Map Container --}}
                    <div id="checkout-map" class="w-full h-40 rounded-xl border border-slate-200 overflow-hidden shadow-inner"></div>
                </div>

                <div class="mt-4 space-y-3.5 text-xs">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Label Alamat</label>
                            <select x-model="newAddr.label" class="w-full h-9 rounded-xl border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                                <option value="Rumah">Rumah</option>
                                <option value="Kantor">Kantor</option>
                                <option value="Apartemen">Apartemen</option>
                                <option value="Kos">Kos</option>
                                <option value="Toko">Toko</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nama Penerima <span class="text-rose-500">*</span></label>
                            <input type="text" x-model="newAddr.recipient_name" required placeholder="Nama Lengkap"
                                   class="w-full h-9 rounded-xl border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nomor Telepon/HP <span class="text-rose-500">*</span></label>
                            <input type="text" x-model="newAddr.phone" required placeholder="08123456789"
                                   class="w-full h-9 rounded-xl border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Provinsi <span class="text-rose-500">*</span></label>
                            <select x-model="newAddr.province" @change="onProvinceChange()"
                                    class="w-full h-9 rounded-xl border border-slate-300 text-xs px-2.5 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                                <template x-for="prov in Object.keys(provincesData)" :key="prov">
                                    <option :value="prov" x-text="prov" :selected="newAddr.province === prov"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kota/Kabupaten <span class="text-rose-500">*</span></label>
                            <select x-model="newAddr.city" @change="onCityChange()"
                                    class="w-full h-9 rounded-xl border border-slate-300 text-xs px-2.5 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                                <template x-for="c in getAvailableCities()" :key="c">
                                    <option :value="c" x-text="c" :selected="newAddr.city === c"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kecamatan</label>
                            <input type="text" x-model="newAddr.district" placeholder="Kebayoran Baru"
                                   class="w-full h-9 rounded-xl border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kode Pos</label>
                            <input type="text" x-model="newAddr.postal_code" placeholder="12190"
                                   class="w-full h-9 rounded-xl border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alamat Lengkap <span class="text-rose-500">*</span></label>
                        <textarea x-model="newAddr.full_address" rows="2" required placeholder="Jl. Sudirman No. 45, RT 02/RW 03..."
                                  class="w-full rounded-xl border border-slate-300 text-xs p-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500"></textarea>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Catatan Patokan untuk Kurir (Opsional)</label>
                        <input type="text" x-model="newAddr.notes" placeholder="Contoh: Rumah pagar hitam samping minimarket"
                               class="w-full h-9 rounded-xl border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="showAddAddressModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium cursor-pointer">
                            Batal
                        </button>
                        <button type="button" @click="saveNewAddress()" class="px-5 py-2 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-semibold shadow-xs cursor-pointer">
                            Simpan & Gunakan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- Cache bust: 20260820113044 --}}
