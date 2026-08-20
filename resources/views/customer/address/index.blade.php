<x-app-layout>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <div class="page-container py-5" x-data="{
        showCreateModal: false,
        showEditModal: false,
        isLocating: false,
        searchQuery: '',
        searchResults: [],
        isSearching: false,
        mapCreate: null,
        markerCreate: null,
        mapEdit: null,
        markerEdit: null,
        formData: {
            id: null,
            label: 'Rumah',
            recipient_name: '',
            phone: '',
            full_address: '',
            city: '',
            district: '',
            province: '',
            postal_code: '',
            latitude: '-6.2088',
            longitude: '106.8456',
            notes: '',
            is_default: false,
            actionUrl: ''
        },
        openCreate() {
            this.formData = {
                id: null,
                label: 'Rumah',
                recipient_name: '{{ auth()->user()->name }}',
                phone: '{{ auth()->user()->phone ?? '' }}',
                full_address: '',
                city: '',
                district: '',
                province: '',
                postal_code: '',
                latitude: '-6.2088',
                longitude: '106.8456',
                notes: '',
                is_default: {{ $addresses->count() === 0 ? 'true' : 'false' }},
                actionUrl: '{{ route('customer.addresses.store') }}'
            };
            this.showCreateModal = true;
            this.$nextTick(() => {
                this.initMap('create-map', 'create');
            });
        },
        openEdit(addr, url) {
            this.formData = {
                id: addr.id,
                label: addr.label || 'Rumah',
                recipient_name: addr.recipient_name,
                phone: addr.phone,
                full_address: addr.full_address,
                city: addr.city || '',
                district: addr.district || '',
                province: addr.province || '',
                postal_code: addr.postal_code || '',
                latitude: addr.latitude || '-6.2088',
                longitude: addr.longitude || '106.8456',
                notes: addr.notes || '',
                is_default: Boolean(addr.is_default),
                actionUrl: url
            };
            this.showEditModal = true;
            this.$nextTick(() => {
                this.initMap('edit-map', 'edit');
            });
        },
        initMap(containerId, mode) {
            const lat = parseFloat(this.formData.latitude) || -6.2088;
            const lng = parseFloat(this.formData.longitude) || 106.8456;

            if (mode === 'create') {
                if (this.mapCreate) {
                    this.mapCreate.remove();
                }
                this.mapCreate = L.map(containerId).setView([lat, lng], 14);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(this.mapCreate);

                this.markerCreate = L.marker([lat, lng], { draggable: true }).addTo(this.mapCreate);
                this.markerCreate.on('dragend', (e) => {
                    const pos = e.target.getLatLng();
                    this.formData.latitude = pos.lat.toFixed(6);
                    this.formData.longitude = pos.lng.toFixed(6);
                    this.reverseGeocode(pos.lat, pos.lng);
                });
            } else {
                if (this.mapEdit) {
                    this.mapEdit.remove();
                }
                this.mapEdit = L.map(containerId).setView([lat, lng], 14);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(this.mapEdit);

                this.markerEdit = L.marker([lat, lng], { draggable: true }).addTo(this.mapEdit);
                this.markerEdit.on('dragend', (e) => {
                    const pos = e.target.getLatLng();
                    this.formData.latitude = pos.lat.toFixed(6);
                    this.formData.longitude = pos.lng.toFixed(6);
                    this.reverseGeocode(pos.lat, pos.lng);
                });
            }
        },
        getCurrentLocation(mode) {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung Geolocation.');
                return;
            }
            this.isLocating = true;
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.isLocating = false;
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    this.formData.latitude = lat.toFixed(6);
                    this.formData.longitude = lng.toFixed(6);

                    const map = mode === 'create' ? this.mapCreate : this.mapEdit;
                    const marker = mode === 'create' ? this.markerCreate : this.markerEdit;

                    if (map && marker) {
                        map.setView([lat, lng], 16);
                        marker.setLatLng([lat, lng]);
                    }
                    this.reverseGeocode(lat, lng);
                },
                (error) => {
                    this.isLocating = false;
                    alert('Gagal mendeteksi lokasi: ' + error.message);
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        },
        searchLocation(query) {
            if (!query || query.length < 3) {
                this.searchResults = [];
                return;
            }
            this.isSearching = true;
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query + ', Indonesia')}&addressdetails=1&limit=5`)
                .then(res => res.json())
                .then(data => {
                    this.searchResults = data;
                    this.isSearching = false;
                })
                .catch(() => {
                    this.isSearching = false;
                });
        },
        selectLocation(result, mode) {
            const lat = parseFloat(result.lat);
            const lng = parseFloat(result.lon);
            this.formData.latitude = lat.toFixed(6);
            this.formData.longitude = lng.toFixed(6);

            const map = mode === 'create' ? this.mapCreate : this.mapEdit;
            const marker = mode === 'create' ? this.markerCreate : this.markerEdit;

            if (map && marker) {
                map.setView([lat, lng], 16);
                marker.setLatLng([lat, lng]);
            }

            const addr = result.address || {};
            this.formData.city = addr.city || addr.town || addr.municipality || addr.county || '';
            this.formData.district = addr.suburb || addr.neighbourhood || addr.quarter || '';
            this.formData.province = addr.state || '';
            this.formData.postal_code = addr.postcode || '';
            this.formData.full_address = result.display_name;

            this.searchResults = [];
            this.searchQuery = '';
        },
        reverseGeocode(lat, lng) {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.address) {
                        const addr = data.address;
                        this.formData.city = addr.city || addr.town || addr.municipality || addr.county || this.formData.city;
                        this.formData.district = addr.suburb || addr.neighbourhood || addr.quarter || this.formData.district;
                        this.formData.province = addr.state || this.formData.province;
                        this.formData.postal_code = addr.postcode || this.formData.postal_code;
                        if (!this.formData.full_address) {
                            this.formData.full_address = data.display_name;
                        }
                    }
                })
                .catch(() => {});
        }
    }">
        <nav class="flex text-xs text-slate-400 mb-4 items-center gap-1.5 flex-wrap" aria-label="Breadcrumb">
            <a href="{{ auth()->check() ? url('/?is_from_login=true') : url('/') }}" class="hover:text-cyan-700 transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <a href="{{ route('customer.dashboard') }}" class="hover:text-cyan-700 transition-colors">Akun Saya</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <span class="text-slate-700 font-medium">Buku Alamat Pengiriman</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-cyan-600 text-lg"></i>
                    Buku Alamat Pengiriman
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Kelola daftar alamat tujuan dengan titik akurasi GPS dan estimasi ongkir otomatis</p>
            </div>
            <button @click="openCreate()"
                    class="btn-primary h-9.5 px-4 text-xs flex items-center gap-2 bg-cyan-700 hover:bg-cyan-800 rounded-xl shadow-xs cursor-pointer">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Alamat Baru</span>
            </button>
        </div>

        @if(session('success'))
            <div class="mb-5 flex items-center gap-2.5 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl text-xs font-semibold">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs space-y-1">
                @foreach($errors->all() as $error)
                    <p class="flex items-center gap-1.5"><i class="fa-solid fa-circle-exclamation text-rose-600"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if($addresses->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                @foreach($addresses as $addr)
                    <div class="bg-white rounded-xl border {{ $addr->is_default ? 'border-cyan-500 ring-2 ring-cyan-500/20' : 'border-slate-200/80' }} p-5 shadow-card flex flex-col justify-between relative transition-all">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $addr->label === 'Kantor' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                        <i class="fa-solid {{ $addr->label === 'Kantor' ? 'fa-building' : 'fa-house' }} text-[9px] mr-1"></i>
                                        {{ $addr->label }}
                                    </span>
                                    @if($addr->is_default)
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-cyan-50 text-cyan-800 border border-cyan-200 flex items-center gap-1">
                                            <i class="fa-solid fa-check-circle text-[9px] text-cyan-600"></i> Utama
                                        </span>
                                    @endif
                                    @if($addr->latitude && $addr->longitude)
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                            <i class="fa-solid fa-location-crosshairs text-[9px]"></i> Pinpoint GPS
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-1">
                                    <button @click="openEdit({{ $addr->toJson() }}, '{{ route('customer.addresses.update', $addr) }}')"
                                            class="w-7 h-7 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-cyan-700 flex items-center justify-center text-xs transition-colors cursor-pointer" title="Edit Alamat">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    @if(!$addr->is_default)
                                    <form action="{{ route('customer.addresses.destroy', $addr) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus alamat ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-7 h-7 rounded-lg text-slate-400 hover:bg-rose-50 hover:text-rose-600 flex items-center justify-center text-xs transition-colors cursor-pointer" title="Hapus Alamat">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>

                            <h3 class="text-sm font-bold text-slate-900 leading-tight">{{ $addr->recipient_name }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5 font-mono">{{ $addr->phone }}</p>

                            <p class="text-xs text-slate-700 mt-2.5 leading-relaxed whitespace-pre-line">
                                {{ $addr->full_address }}
                            </p>

                            @if($addr->district || $addr->city || $addr->province || $addr->postal_code)
                                <p class="text-[11px] text-slate-500 font-medium mt-1.5 flex items-center gap-1">
                                    <i class="fa-solid fa-map-pin text-cyan-600 text-[10px]"></i>
                                    {{ implode(', ', array_filter([$addr->district, $addr->city, $addr->province, $addr->postal_code])) }}
                                </p>
                            @endif

                            @if($addr->notes)
                                <div class="mt-2.5 p-2 bg-slate-50 rounded-lg border border-slate-200/80 text-[11px] text-slate-600 flex items-start gap-1.5">
                                    <i class="fa-solid fa-circle-info text-cyan-600 text-[10px] mt-0.5 shrink-0"></i>
                                    <span><strong>Catatan Kurir:</strong> {{ $addr->notes }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                            @if(!$addr->is_default)
                                <form action="{{ route('customer.addresses.set_default', $addr) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-semibold text-cyan-700 hover:text-cyan-800 hover:underline flex items-center gap-1 cursor-pointer">
                                        <i class="fa-regular fa-star text-[10px]"></i> Jadikan Alamat Utama
                                    </button>
                                </form>
                            @else
                                <span class="text-[11px] font-semibold text-emerald-600 flex items-center gap-1">
                                    <i class="fa-solid fa-shield-check text-xs"></i> Alamat aktif saat checkout
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center shadow-card max-w-lg mx-auto">
                <div class="w-16 h-16 rounded-full bg-cyan-50 border border-cyan-100 text-cyan-600 flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Alamat Pengiriman</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                    Simpan alamat rumah atau kantormu agar proses checkout belanjaan lebih cepat, akurat, dan praktis.
                </p>
                <button @click="openCreate()" class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white text-xs font-semibold shadow-sm transition-all cursor-pointer">
                    <i class="fa-solid fa-plus"></i>
                    Tambah Alamat Sekarang
                </button>
            </div>
        @endif

        {{-- Modal Tambah Alamat (Realtime Pinpoint GPS & Autocomplete) --}}
        <div x-show="showCreateModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.outside="showCreateModal = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-2xl max-w-2xl w-full p-5 sm:p-6 shadow-2xl border border-slate-200 my-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-map-location-dot text-cyan-600 text-base"></i>
                        <h3 class="font-bold text-sm sm:text-base text-slate-900">Tambah Alamat Pengiriman Baru</h3>
                    </div>
                    <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
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
                                    <button type="button" @click="selectLocation(res, 'create')"
                                            class="w-full p-2.5 text-left hover:bg-cyan-50/70 flex items-start gap-2 cursor-pointer transition-colors">
                                        <i class="fa-solid fa-location-dot text-cyan-600 text-xs mt-0.5 shrink-0"></i>
                                        <span class="truncate" x-text="res.display_name"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <button type="button" @click="getCurrentLocation('create')"
                                :disabled="isLocating"
                                class="h-9 px-3.5 bg-cyan-50 text-cyan-800 hover:bg-cyan-100 rounded-xl border border-cyan-200 text-xs font-semibold flex items-center justify-center gap-1.5 shrink-0 cursor-pointer transition-all">
                            <i class="fa-solid fa-location-crosshairs text-cyan-700" :class="isLocating ? 'animate-spin' : ''"></i>
                            <span x-text="isLocating ? 'Mendeteksi...' : 'Lokasi Saya'"></span>
                        </button>
                    </div>

                    {{-- Map Container --}}
                    <div id="create-map" class="w-full h-44 rounded-xl border border-slate-200 overflow-hidden shadow-inner"></div>
                    <p class="text-[11px] text-slate-500 italic">
                        <i class="fa-solid fa-circle-info text-cyan-600 text-[10px] mr-1"></i>
                        Geser pin merah di peta untuk menentukan titik koordinat rumah/kantor secara presisi.
                    </p>
                </div>

                <form action="{{ route('customer.addresses.store') }}" method="POST" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    <input type="hidden" name="latitude" x-model="formData.latitude">
                    <input type="hidden" name="longitude" x-model="formData.longitude">

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Label Alamat</label>
                            <select name="label" x-model="formData.label" class="w-full h-9 rounded-xl border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
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
                            <input type="text" name="recipient_name" x-model="formData.recipient_name" required placeholder="Nama Lengkap"
                                   class="w-full h-9 rounded-xl border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nomor Telepon/HP <span class="text-rose-500">*</span></label>
                            <input type="text" name="phone" x-model="formData.phone" required placeholder="08123456789"
                                   class="w-full h-9 rounded-xl border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kota/Kabupaten</label>
                            <input type="text" name="city" x-model="formData.city" placeholder="Contoh: Jakarta Selatan"
                                   class="w-full h-9 rounded-xl border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kecamatan</label>
                            <input type="text" name="district" x-model="formData.district" placeholder="Contoh: Kebayoran Baru"
                                   class="w-full h-9 rounded-xl border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Provinsi</label>
                            <input type="text" name="province" x-model="formData.province" placeholder="DKI Jakarta"
                                   class="w-full h-9 rounded-xl border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kode Pos</label>
                            <input type="text" name="postal_code" x-model="formData.postal_code" placeholder="12190"
                                   class="w-full h-9 rounded-xl border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alamat Lengkap (Nama Jalan, No. Rumah, RT/RW) <span class="text-rose-500">*</span></label>
                        <textarea name="full_address" x-model="formData.full_address" rows="2" required placeholder="Jl. Sudirman No. 45, RT 02/RW 03..."
                                  class="w-full rounded-xl border border-slate-300 text-xs p-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500"></textarea>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Catatan Patokan untuk Kurir (Opsional)</label>
                        <input type="text" name="notes" x-model="formData.notes" placeholder="Contoh: Rumah pagar hitam samping minimarket, titipkan ke satpam"
                               class="w-full h-9 rounded-xl border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                    </div>

                    <label class="flex items-center gap-2 pt-1 cursor-pointer">
                        <input type="checkbox" name="is_default" value="1" x-model="formData.is_default" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                        <span class="text-slate-700 font-medium">Jadikan sebagai alamat utama (default checkout)</span>
                    </label>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-semibold shadow-xs cursor-pointer">
                            Simpan Alamat
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Edit Alamat --}}
        <div x-show="showEditModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.outside="showEditModal = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-2xl max-w-2xl w-full p-5 sm:p-6 shadow-2xl border border-slate-200 my-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-pen-to-square text-cyan-600 text-base"></i>
                        <h3 class="font-bold text-sm sm:text-base text-slate-900">Ubah Alamat Pengiriman</h3>
                    </div>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                {{-- Map Pinpoint for Edit --}}
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

                            <div x-show="searchResults.length > 0" x-cloak
                                 class="absolute z-20 top-10 left-0 right-0 bg-white rounded-xl shadow-xl border border-slate-200 divide-y divide-slate-100 max-h-48 overflow-y-auto text-xs">
                                <template x-for="res in searchResults" :key="res.place_id">
                                    <button type="button" @click="selectLocation(res, 'edit')"
                                            class="w-full p-2.5 text-left hover:bg-cyan-50/70 flex items-start gap-2 cursor-pointer transition-colors">
                                        <i class="fa-solid fa-location-dot text-cyan-600 text-xs mt-0.5 shrink-0"></i>
                                        <span class="truncate" x-text="res.display_name"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <button type="button" @click="getCurrentLocation('edit')"
                                :disabled="isLocating"
                                class="h-9 px-3.5 bg-cyan-50 text-cyan-800 hover:bg-cyan-100 rounded-xl border border-cyan-200 text-xs font-semibold flex items-center justify-center gap-1.5 shrink-0 cursor-pointer transition-all">
                            <i class="fa-solid fa-location-crosshairs text-cyan-700" :class="isLocating ? 'animate-spin' : ''"></i>
                            <span x-text="isLocating ? 'Mendeteksi...' : 'Lokasi Saya'"></span>
                        </button>
                    </div>

                    <div id="edit-map" class="w-full h-44 rounded-xl border border-slate-200 overflow-hidden shadow-inner"></div>
                </div>

                <form :action="formData.actionUrl" method="POST" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="latitude" x-model="formData.latitude">
                    <input type="hidden" name="longitude" x-model="formData.longitude">

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Label Alamat</label>
                            <select name="label" x-model="formData.label" class="w-full h-9 rounded-xl border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
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
                            <input type="text" name="recipient_name" x-model="formData.recipient_name" required
                                   class="w-full h-9 rounded-xl border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nomor Telepon/HP <span class="text-rose-500">*</span></label>
                            <input type="text" name="phone" x-model="formData.phone" required
                                   class="w-full h-9 rounded-xl border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kota/Kabupaten</label>
                            <input type="text" name="city" x-model="formData.city"
                                   class="w-full h-9 rounded-xl border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kecamatan</label>
                            <input type="text" name="district" x-model="formData.district"
                                   class="w-full h-9 rounded-xl border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Provinsi</label>
                            <input type="text" name="province" x-model="formData.province"
                                   class="w-full h-9 rounded-xl border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kode Pos</label>
                            <input type="text" name="postal_code" x-model="formData.postal_code"
                                   class="w-full h-9 rounded-xl border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alamat Lengkap <span class="text-rose-500">*</span></label>
                        <textarea name="full_address" x-model="formData.full_address" rows="2" required
                                  class="w-full rounded-xl border border-slate-300 text-xs p-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500"></textarea>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Catatan Patokan untuk Kurir</label>
                        <input type="text" name="notes" x-model="formData.notes"
                               class="w-full h-9 rounded-xl border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                    </div>

                    <label class="flex items-center gap-2 pt-1 cursor-pointer">
                        <input type="checkbox" name="is_default" value="1" x-model="formData.is_default" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                        <span class="text-slate-700 font-medium">Jadikan sebagai alamat utama (default checkout)</span>
                    </label>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-semibold shadow-xs cursor-pointer">
                            Perbarui Alamat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
