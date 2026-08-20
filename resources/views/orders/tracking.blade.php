<x-app-layout>
    <x-slot name="title">
        Lacak Pengiriman Pesanan #{{ $order->invoice_number }} — SakserShop
    </x-slot>

    {{-- Leaflet Maps CSS & JS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        .custom-pulsing-marker {
            position: relative;
        }
        .pulse-ring {
            position: absolute;
            top: -10px;
            left: -10px;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(8, 145, 178, 0.4);
            animation: pulseAnimation 2s infinite ease-out;
        }
        @keyframes pulseAnimation {
            0% { transform: scale(0.6); opacity: 1; }
            100% { transform: scale(1.6); opacity: 0; }
        }
    </style>

    <div class="page-container py-6" x-data="{
        copiedResi: false,
        activeView: 'map',
        copyResi(text) {
            navigator.clipboard.writeText(text);
            this.copiedResi = true;
            setTimeout(() => this.copiedResi = false, 2000);
        }
    }">
        {{-- Breadcrumb --}}
        <nav class="flex text-xs text-slate-400 mb-4 items-center gap-1.5 flex-wrap" aria-label="Breadcrumb">
            <a href="{{ auth()->check() ? url('/?is_from_login=true') : url('/') }}" class="hover:text-cyan-700 transition-colors flex items-center gap-1">
                <i class="fa-solid fa-house text-[10px]"></i> Beranda
            </a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <a href="{{ route('customer.dashboard') }}" class="hover:text-cyan-700 transition-colors">Pesanan Saya</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <span class="text-slate-800 font-semibold">Lacak Pengiriman Paket</span>
        </nav>

        {{-- Main Layout Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- Left Column: Live Maps & Courier Card (7 cols) --}}
            <div class="lg:col-span-7 space-y-4">
                
                {{-- Live Delivery Status Card --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-card relative overflow-hidden">
                    <div class="flex items-center justify-between flex-wrap gap-3 pb-3.5 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                            </span>
                            <h1 class="text-base font-extrabold text-slate-900 tracking-tight">
                                @if($order->status === 'completed')
                                    Paket Telah Sampai di Tujuan
                                @elseif($order->status === 'shipped')
                                    Kurir Sedang Menuju Alamat Anda
                                @elseif($order->status === 'processing')
                                    Paket Sedang Disiapkan Penjual
                                @else
                                    Menunggu Pembayaran
                                @endif
                            </h1>
                        </div>
                        <span class="text-xs font-bold text-cyan-800 bg-cyan-50 px-2.5 py-1 rounded-full border border-cyan-200">
                            {{ $courier['exp'] }}
                        </span>
                    </div>

                    {{-- Progress Bar Strip --}}
                    <div class="mt-4">
                        <div class="flex items-center justify-between text-xs mb-1.5 font-semibold">
                            <span class="text-slate-500">Proses Pengiriman</span>
                            <span class="text-cyan-700">{{ $progress }}%</span>
                        </div>
                        <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-cyan-600 to-teal-500 rounded-full transition-all duration-1000" style="width: {{ $progress }}%"></div>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-2 font-medium flex items-center gap-1.5">
                            <i class="fa-solid fa-clock text-cyan-600 text-xs"></i>
                            <strong>Estimasi Tiba:</strong> {{ $estimated_time }}
                        </p>
                    </div>

                    {{-- Courier Profile Card Overlay --}}
                    <div class="mt-4 pt-3.5 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($courier['name']) }}&background=0891b2&color=fff&size=80&bold=true"
                                     class="w-11 h-11 rounded-xl object-cover border border-cyan-200" alt="Kurir">
                                <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 rounded-full border-2 border-white flex items-center justify-center text-[8px] text-white">
                                    <i class="fa-solid fa-check"></i>
                                </span>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-slate-900">{{ $courier['name'] }}</h3>
                                <p class="text-[11px] text-slate-500 font-medium">Kurir Pengantar • <span class="font-mono font-bold text-slate-700">{{ $courier['plate'] }}</span></p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="https://wa.me/?text=Halo%20Kurir%20{{ urlencode($courier['name']) }},%20saya%20pembeli%20pesanan%20{{ $order->invoice_number }}" target="_blank"
                               class="btn-primary text-xs h-8.5 px-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold flex items-center gap-1.5 shadow-xs transition-all">
                                <i class="fa-brands fa-whatsapp text-sm"></i>
                                <span>Chat Kurir</span>
                            </a>
                            <button type="button" @click="copyResi('{{ $order->tracking_number ?: 'BLJEXP' . $order->id }}')"
                                    class="h-8.5 px-3 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs flex items-center gap-1.5 transition-colors cursor-pointer" title="Salin Resi">
                                <i class="fa-solid fa-barcode text-slate-400"></i>
                                <span x-text="copiedResi ? 'Tersalin!' : 'Salin Resi'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Interactive Leaflet Map Container --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-card overflow-hidden relative">
                    <div class="p-3.5 bg-slate-50 border-b border-slate-200/80 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2 font-bold text-slate-800">
                            <i class="fa-solid fa-map-location-dot text-cyan-700"></i>
                            <span>Live Maps Lokasi Paket & Kurir</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button id="recenterBtn" type="button" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-semibold text-[11px] flex items-center gap-1 cursor-pointer">
                                <i class="fa-solid fa-crosshairs text-cyan-600"></i> Pusatkan Peta
                            </button>
                        </div>
                    </div>

                    {{-- Map Div --}}
                    <div id="liveTrackingMap" class="w-full h-80 sm:h-96 z-10 bg-slate-100"></div>

                    {{-- Map Legend Bar --}}
                    <div class="p-3 bg-white border-t border-slate-100 flex items-center justify-around text-[10px] text-slate-500 font-semibold flex-wrap gap-2">
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-purple-600 border-2 border-white shadow-xs"></span>
                            <span>Gudang Penjual</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-amber-500 border-2 border-white shadow-xs"></span>
                            <span>DC Sortir Hub</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-cyan-600 border-2 border-white shadow-xs"></span>
                            <span>Posisi Kurir</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-emerald-600 border-2 border-white shadow-xs"></span>
                            <span>Alamat Pembeli</span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right Column: Timeline & Order Summary (5 cols) --}}
            <div class="lg:col-span-5 space-y-4">
                
                {{-- Order Item Summary Mini Card --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-card">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 text-xs">
                        <div class="flex items-center gap-1.5 font-bold text-slate-900">
                            <i class="fa-solid fa-receipt text-cyan-700"></i>
                            <span>#{{ $order->invoice_number }}</span>
                        </div>
                        <a href="{{ route('orders.invoice', $order) }}" target="_blank" class="text-cyan-700 hover:underline font-semibold text-[11px] flex items-center gap-1">
                            <i class="fa-solid fa-file-invoice"></i> Invoice Resmi
                        </a>
                    </div>

                    <div class="py-3 space-y-2.5 max-h-48 overflow-y-auto">
                        @foreach($order->orderItems as $it)
                        <div class="flex items-center gap-3">
                            <img src="{{ $it->product?->image_url ?? 'https://via.placeholder.com/60' }}" class="w-10 h-10 rounded-lg object-cover border border-slate-200 bg-slate-50 shrink-0" alt="Item">
                            <div class="min-w-0 flex-1 text-xs">
                                <p class="font-bold text-slate-900 truncate">{{ $it->product?->name ?? 'Produk' }}</p>
                                <p class="text-[11px] text-slate-400">{{ $it->quantity }}x @ Rp {{ number_format($it->price, 0, ',', '.') }}</p>
                            </div>
                            <span class="text-xs font-bold text-slate-900 shrink-0">Rp {{ number_format($it->price * $it->quantity, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-medium">Total Pembayaran:</span>
                        <span class="text-base font-extrabold text-cyan-800">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Delivery Timeline Logs Card (Shopee / Tokopedia Style) --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-card">
                    <h3 class="text-xs font-extrabold text-slate-900 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-timeline text-cyan-700"></i>
                        <span>Riwayat Perjalanan Paket</span>
                    </h3>

                    <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200 text-xs">
                        @foreach($checkpoints as $index => $cp)
                        <div class="relative">
                            {{-- Dot / Icon Indicator --}}
                            <div class="absolute -left-6 top-0.5 w-4.5 h-4.5 rounded-full flex items-center justify-center text-[9px] border-2 border-white shadow-xs
                                        {{ $loop->first ? 'bg-cyan-600 text-white ring-4 ring-cyan-100' : 'bg-slate-300 text-white' }}">
                                <i class="fa-solid {{ $cp['icon'] }}"></i>
                            </div>

                            {{-- Content --}}
                            <div>
                                <h4 class="font-bold {{ $loop->first ? 'text-cyan-900 text-xs' : 'text-slate-700 text-[11px]' }}">
                                    {{ $cp['title'] }}
                                </h4>
                                <p class="text-[11px] {{ $loop->first ? 'text-cyan-700 font-semibold' : 'text-slate-500' }} mt-0.5">
                                    {{ $cp['location'] }}
                                </p>
                                <span class="text-[10px] text-slate-400 block mt-1 font-mono">
                                    {{ $cp['time'] }} WIB
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Delivery Destination Address Card --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-card text-xs">
                    <div class="flex items-center gap-2 text-slate-900 font-bold mb-2">
                        <i class="fa-solid fa-location-dot text-rose-500"></i>
                        <span>Alamat Penerima</span>
                    </div>
                    <p class="font-bold text-slate-800">{{ $order->user->name }} ({{ $order->user->phone ?? '08xxxxxxxxxx' }})</p>
                    <p class="text-slate-600 mt-1 whitespace-pre-line leading-relaxed text-[11px] bg-slate-50 p-2.5 rounded-xl border border-slate-200/80">
                        {{ $order->shipping_address }}
                    </p>
                </div>

            </div>

        </div>
    </div>

    {{-- Leaflet Map Initialization Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const originCoord = [{{ $origin['lat'] }}, {{ $origin['lng'] }}];
            const hubCoord = [{{ $hub['lat'] }}, {{ $hub['lng'] }}];
            const destCoord = [{{ $destination['lat'] }}, {{ $destination['lng'] }}];
            const courierCoord = [{{ $courier_pos['lat'] }}, {{ $courier_pos['lng'] }}];

            // Initialize map
            const map = L.map('liveTrackingMap', {
                zoomControl: true,
                attributionControl: false
            }).setView(courierCoord, 13);

            // Add tile layer (OpenStreetMap Standard)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(map);

            // Create custom HTML icon helper
            function createCustomIcon(iconClass, bgColor, isPulsing = false) {
                const pulseHtml = isPulsing ? '<div class="pulse-ring"></div>' : '';
                return L.divIcon({
                    className: 'custom-map-icon',
                    html: `
                        <div class="relative flex items-center justify-center select-none" style="width: 32px; height: 32px;">
                            ${pulseHtml}
                            <div class="w-8 h-8 rounded-full ${bgColor} text-white flex items-center justify-center shadow-lg border-2 border-white text-xs z-10">
                                <i class="fa-solid ${iconClass}"></i>
                            </div>
                        </div>
                    `,
                    iconSize: [32, 32],
                    iconAnchor: [16, 16],
                    popupAnchor: [0, -16]
                });
            }

            // 1. Origin Marker (Store)
            const originMarker = L.marker(originCoord, {
                icon: createCustomIcon('fa-store', 'bg-purple-600')
            }).addTo(map).bindPopup('<strong>Toko Penjual</strong><br>{{ addslashes($order->store->name ?? "Toko") }}');

            // 2. Hub Marker
            const hubMarker = L.marker(hubCoord, {
                icon: createCustomIcon('fa-warehouse', 'bg-amber-500')
            }).addTo(map).bindPopup('<strong>Pusat Sortir Hub DC</strong><br>Paket telah melewati sortir transit.');

            // 3. Destination Marker (Buyer Home)
            const destMarker = L.marker(destCoord, {
                icon: createCustomIcon('fa-house-chimney', 'bg-emerald-600')
            }).addTo(map).bindPopup('<strong>Alamat Tujuan Anda</strong><br>{{ addslashes($order->user->name) }}');

            // 4. Live Courier Marker (Pulsating)
            const courierMarker = L.marker(courierCoord, {
                icon: createCustomIcon('fa-motorcycle', 'bg-cyan-600', true)
            }).addTo(map).bindPopup('<strong>Kurir: {{ addslashes($courier["name"]) }}</strong><br>{{ $courier["plate"] }}<br><span class="text-cyan-700 font-bold">Sedang Mengantar Paket</span>').openPopup();

            // Route Polylines
            // Route from Origin to Hub (Completed leg)
            const leg1 = L.polyline([originCoord, hubCoord], {
                color: '#9333ea',
                weight: 4,
                opacity: 0.7,
                dashArray: '6, 8'
            }).addTo(map);

            // Route from Hub to Courier
            const leg2 = L.polyline([hubCoord, courierCoord], {
                color: '#0891b2',
                weight: 5,
                opacity: 0.9
            }).addTo(map);

            // Route from Courier to Destination (Remaining leg)
            const leg3 = L.polyline([courierCoord, destCoord], {
                color: '#10b981',
                weight: 4,
                opacity: 0.6,
                dashArray: '8, 8'
            }).addTo(map);

            // Fit all markers in viewport
            const group = new L.featureGroup([originMarker, hubMarker, courierMarker, destMarker]);
            map.fitBounds(group.getBounds().pad(0.2));

            // Re-center button action
            document.getElementById('recenterBtn').addEventListener('click', function () {
                map.flyTo(courierCoord, 14, {
                    animate: true,
                    duration: 1
                });
                courierMarker.openPopup();
            });

            // Simulated micro-motion heartbeat for courier marker
            let step = 0;
            setInterval(() => {
                step = (step + 1) % 4;
                const jitterLat = (Math.sin(step) * 0.00015);
                const jitterLng = (Math.cos(step) * 0.00015);
                courierMarker.setLatLng([courierCoord[0] + jitterLat, courierCoord[1] + jitterLng]);
            }, 3000);
        });
    </script>
</x-app-layout>
