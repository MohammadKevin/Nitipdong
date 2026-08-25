<x-seller-layout title="Pengaturan Alamat & Profil Toko - NitipDong">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <div class="p-6 max-w-5xl mx-auto space-y-6">
        
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-store text-cyan-600"></i>
                    <span>Pengaturan Toko & Alamat Pengiriman</span>
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Atur lokasi gudang asal pengiriman barang Anda. Lokasi kota menentukan berlakunya promo <strong>Gratis Ongkir Rp0</strong> untuk pelanggan.
                </p>
            </div>
            <a href="{{ route('store.show', $store->slug) }}" target="_blank"
               class="btn-secondary text-xs h-9 px-4 rounded-xl flex items-center gap-1.5 border-slate-300 hover:border-cyan-600 hover:text-cyan-700 shadow-2xs">
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                <span>Lihat Halaman Toko Saya</span>
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs flex items-center gap-2 shadow-2xs">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs space-y-1 shadow-2xs">
                <div class="font-bold flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                    <span>Terdapat beberapa kesalahan pengisian form:</span>
                </div>
                <ul class="list-disc list-inside pl-4 text-[11px] text-rose-700 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('seller.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6"
              x-data="{
                  provincesList: [],
                  regenciesList: [],
                  districtsList: [],
                  villagesList: [],
                  selectedProvince: '{{ old('province', $store->province ?: 'DKI Jakarta') }}',
                  selectedCity: '{{ old('city', $store->city ?: ($store->effective_city ?: 'Jakarta Pusat')) }}',
                  selectedDistrict: '{{ old('district', $store->district ?: '') }}',
                  selectedVillage: '',
                  postalCode: '{{ old('postal_code', $store->postal_code ?: '10110') }}',
                  fullAddress: '{{ old('address', $store->address ?: '') }}',
                  lat: '{{ old('latitude', $store->latitude ?: -6.2088) }}',
                  lng: '{{ old('longitude', $store->longitude ?: 106.8456) }}',
                  isLoadingRegions: false,
                  isLocating: false,
                  map: null,
                  marker: null,

                  async init() {
                      await this.loadProvinces();
                      this.$nextTick(() => {
                          this.initMap();
                      });
                  },

                  async loadProvinces() {
                      try {
                          const res = await fetch('{{ route('api.regions.provinces') }}');
                          const json = await res.json();
                          if (json.success && Array.isArray(json.data)) {
                              this.provincesList = json.data;
                              await this.onProvinceChange(true);
                          }
                      } catch (e) {
                          console.error('Error loading provinces:', e);
                      }
                  },

                  async onProvinceChange(isInit = false) {
                      const prov = this.provincesList.find(p => p.name === this.selectedProvince);
                      this.regenciesList = [];
                      this.districtsList = [];
                      this.villagesList = [];

                      if (!prov) return;

                      this.isLoadingRegions = true;
                      try {
                          const res = await fetch(`{{ url('/api/regions/regencies') }}/${prov.id}`);
                          const json = await res.json();
                          if (json.success && Array.isArray(json.data)) {
                              this.regenciesList = json.data;
                              if (!isInit && this.regenciesList.length > 0) {
                                  this.selectedCity = this.regenciesList[0].name;
                                  await this.onCityChange();
                              } else if (isInit) {
                                  await this.onCityChange(true);
                              }
                          }
                      } catch (e) {
                          console.error('Error loading regencies:', e);
                      } finally {
                          this.isLoadingRegions = false;
                      }
                  },

                  async onCityChange(isInit = false) {
                      const reg = this.regenciesList.find(r => r.name === this.selectedCity);
                      this.districtsList = [];
                      this.villagesList = [];

                      if (!reg) return;

                      this.isLoadingRegions = true;
                      try {
                          const res = await fetch(`{{ url('/api/regions/districts') }}/${reg.id}`);
                          const json = await res.json();
                          if (json.success && Array.isArray(json.data)) {
                              this.districtsList = json.data;
                              if (!isInit && this.districtsList.length > 0) {
                                  this.selectedDistrict = this.districtsList[0].name;
                              }
                          }
                      } catch (e) {
                          console.error('Error loading districts:', e);
                      } finally {
                          this.isLoadingRegions = false;
                      }
                  },

                  initMap() {
                      const mapContainer = document.getElementById('seller-store-map');
                      if (!mapContainer) return;

                      const curLat = parseFloat(this.lat) || -6.2088;
                      const curLng = parseFloat(this.lng) || 106.8456;

                      this.map = L.map('seller-store-map').setView([curLat, curLng], 14);
                      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                          maxZoom: 19,
                          attribution: '&copy; OpenStreetMap contributors'
                      }).addTo(this.map);

                      const pinIcon = L.divIcon({
                          className: 'custom-pin',
                          html: '<div style=\'background-color:#0891b2;width:34px;height:34px;border-radius:50%;border:3px solid #fff;box-shadow:0 4px 6px -1px rgba(0,0,0,0.3);display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;\'><i class=\'fa-solid fa-store\'></i></div>',
                          iconSize: [34, 34],
                          iconAnchor: [17, 17]
                      });

                      this.marker = L.marker([curLat, curLng], {
                          draggable: true,
                          icon: pinIcon
                      }).addTo(this.map);

                      this.marker.on('dragend', (e) => {
                          const pos = e.target.getLatLng();
                          this.lat = pos.lat.toFixed(7);
                          this.lng = pos.lng.toFixed(7);
                          this.reverseGeocode(pos.lat, pos.lng);
                      });

                      this.map.on('click', (e) => {
                          this.updateCoords(e.latlng.lat, e.latlng.lng);
                          this.reverseGeocode(e.latlng.lat, e.latlng.lng);
                      });
                  },

                  updateCoords(newLat, newLng) {
                      this.lat = Number(newLat).toFixed(7);
                      this.lng = Number(newLng).toFixed(7);
                      if (this.marker) {
                          this.marker.setLatLng([this.lat, this.lng]);
                      }
                      if (this.map) {
                          this.map.setView([this.lat, this.lng], 15);
                      }
                  },

                  getCurrentLocation() {
                      if (navigator.geolocation) {
                          this.isLocating = true;
                          navigator.geolocation.getCurrentPosition((pos) => {
                              this.isLocating = false;
                              this.updateCoords(pos.coords.latitude, pos.coords.longitude);
                              this.reverseGeocode(pos.coords.latitude, pos.coords.longitude);
                          }, (err) => {
                              this.isLocating = false;
                              alert('Gagal mengambil lokasi GPS perangkat: ' + err.message);
                          }, { enableHighAccuracy: true });
                      } else {
                          alert('Browser Anda tidak mendukung geolokasi.');
                      }
                  },

                  async reverseGeocode(lat, lng) {
                      try {
                          const res = await fetch(`{{ route('api.regions.reverse_geocode') }}?lat=${lat}&lng=${lng}`);
                          const json = await res.json();
                          if (json.success && json.data) {
                              const d = json.data;
                              if (d.street && !this.fullAddress) {
                                  this.fullAddress = d.street;
                              } else if (d.display_name && !this.fullAddress) {
                                  this.fullAddress = d.display_name;
                              }
                              if (d.postal_code) {
                                  this.postalCode = d.postal_code;
                              }
                          }
                      } catch (e) {
                          console.error('Reverse geocode error:', e);
                      }
                  }
              }">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-2xl border border-slate-200 shadow-card p-6 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-cyan-50 text-cyan-700 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900">1. Lokasi &amp; Asal Pengiriman Toko</h2>
                            <p class="text-[11px] text-slate-400">Tentukan kota asal pengiriman untuk penghitungan ongkir dan promo gratis ongkir 1 kota.</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-800 text-[10px] font-bold rounded-lg border border-emerald-200 flex items-center gap-1">
                        <i class="fa-solid fa-truck-fast text-emerald-600"></i> Gratis Ongkir 1 Kota
                    </span>
                </div>

                <div class="p-3.5 bg-gradient-to-r from-emerald-50 via-teal-50 to-cyan-50 border border-emerald-200 rounded-xl text-xs text-emerald-950 flex items-start gap-3">
                    <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-xs shrink-0 mt-0.5 shadow-xs">
                        <i class="fa-solid fa-gift"></i>
                    </div>
                    <div class="space-y-0.5">
                        <span class="font-bold block">Bagaimana Cara Kerja Gratis Ongkir 1 Kota?</span>
                        <p class="text-[11px] text-slate-600 leading-relaxed">
                            Setiap pelanggan NitipDong yang alamat pengirimannya berada di <strong>Kota / Kabupaten yang sama</strong> dengan toko Anda akan otomatis mendapatkan <strong>Gratis Ongkos Kirim (Rp 0)</strong> di checkout!
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="province" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Provinsi Asal Toko <span class="text-rose-500">*</span>
                        </label>
                        <select id="province" name="province" x-model="selectedProvince" @change="onProvinceChange()" required
                                class="input text-xs rounded-xl focus:border-cyan-600 focus:ring-cyan-500 bg-white">
                            <template x-for="p in provincesList" :key="p.id">
                                <option :value="p.name" :selected="p.name === selectedProvince" x-text="p.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label for="city" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kota / Kabupaten Asal Pengiriman <span class="text-rose-500">*</span>
                        </label>
                        <select id="city" name="city" x-model="selectedCity" @change="onCityChange()" required
                                class="input text-xs rounded-xl font-bold text-slate-900 border-cyan-300 focus:border-cyan-600 focus:ring-cyan-500 bg-cyan-50/20" :disabled="regenciesList.length === 0">
                            <template x-for="c in regenciesList" :key="c.id">
                                <option :value="c.name" :selected="c.name === selectedCity" x-text="c.name"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="district" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kecamatan Asal Toko
                        </label>
                        <select id="district" name="district" x-model="selectedDistrict"
                                class="input text-xs rounded-xl bg-white" :disabled="districtsList.length === 0">
                            <option value="">-- Pilih Kecamatan --</option>
                            <template x-for="d in districtsList" :key="d.id">
                                <option :value="d.name" :selected="d.name === selectedDistrict" x-text="d.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label for="postal_code" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kode Pos Gudang
                        </label>
                        <input type="text" id="postal_code" name="postal_code" x-model="postalCode"
                               placeholder="Contoh: 10110" maxlength="10"
                               class="input text-xs rounded-xl font-mono">
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Alamat Lengkap Gudang / Toko Pengambilan Paket <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="address" name="address" x-model="fullAddress" rows="3" required
                              placeholder="Nama jalan, nomor gedung/ruko, RT/RW, nomor kontak gudang, dan instruksi penjemputan paket kurir ekspedisi..."
                              class="input text-xs rounded-xl leading-relaxed"></textarea>
                </div>

                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Titik GPS Lokasi Toko / Gudang (Pinpoint Maps)
                            </label>
                            <span class="text-[10px] text-slate-400">Geser pin biru atau klik pada peta untuk menentukan titik penjemputan kurir akurat</span>
                        </div>
                        <button type="button" @click="getCurrentLocation()" :disabled="isLocating"
                                class="text-xs font-bold text-cyan-700 hover:text-cyan-800 bg-cyan-50 hover:bg-cyan-100 px-3 py-1.5 rounded-lg border border-cyan-200 flex items-center gap-1.5 transition-all cursor-pointer disabled:opacity-50">
                            <i class="fa-solid fa-location-crosshairs text-xs" :class="isLocating ? 'animate-spin' : ''"></i>
                            <span x-text="isLocating ? 'Mendeteksi...' : 'Gunakan Lokasi Saya Sekarang'"></span>
                        </button>
                    </div>

                    <div id="seller-store-map" class="w-full h-64 rounded-xl border border-slate-200 shadow-inner z-0"></div>

                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                            <span class="text-[10px] text-slate-400 block font-bold uppercase">Latitude:</span>
                            <input type="text" name="latitude" x-model="lat" readonly class="bg-transparent font-mono font-bold text-xs text-slate-800 p-0 border-none w-full focus:ring-0">
                        </div>
                        <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                            <span class="text-[10px] text-slate-400 block font-bold uppercase">Longitude:</span>
                            <input type="text" name="longitude" x-model="lng" readonly class="bg-transparent font-mono font-bold text-xs text-slate-800 p-0 border-none w-full focus:ring-0">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-card p-6 space-y-5">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-cyan-50 text-cyan-700 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-id-card"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">2. Identitas & Tampilan Toko</h2>
                        <p class="text-[11px] text-slate-400">Informasi yang akan dilihat oleh calon pembeli di NitipDong.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Toko / Brand <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $store->name) }}" required
                               class="input text-xs rounded-xl font-bold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Slug URL Toko
                        </label>
                        <input type="text" value="{{ $store->slug }}" readonly disabled
                               class="input text-xs rounded-xl bg-slate-100 text-slate-500 font-mono">
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Deskripsi & Slogan Toko
                    </label>
                    <textarea id="description" name="description" rows="3"
                              placeholder="Ceritakan keunggulan toko Anda, jam operasional, dan produk unggulan..."
                              class="input text-xs rounded-xl leading-relaxed">{{ old('description', $store->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2 border-t border-slate-100">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Logo Toko (Rasio 1:1)
                        </label>
                        <div class="flex items-center gap-4">
                            <img src="{{ $store->logo_url }}" class="w-16 h-16 rounded-xl border border-slate-200 object-cover shadow-2xs shrink-0" alt="Logo Toko">
                            <div class="flex-1">
                                <input type="file" name="logo" accept="image/*" class="text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100 cursor-pointer">
                                <span class="text-[10px] text-slate-400 block mt-1">PNG, JPG atau WEBP (Maks 2MB)</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Banner Header Toko (Rasio 3:1)
                        </label>
                        <div class="flex items-center gap-4">
                            <div class="w-24 h-16 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0">
                                @if($store->banner_url)
                                    <img src="{{ $store->banner_url }}" class="w-full h-full object-cover" alt="Banner">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-400 text-xs font-bold">Banner</div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" name="banner" accept="image/*" class="text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100 cursor-pointer">
                                <span class="text-[10px] text-slate-400 block mt-1">PNG, JPG atau WEBP (Maks 4MB)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-card p-6 space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-cyan-50 text-cyan-700 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">3. Rekening Bank Pencairan Saldo Dompet</h2>
                        <p class="text-[11px] text-slate-400">Rekening tujuan ketika Anda melakukan penarikan saldo hasil penjualan toko.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="bank_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Bank
                        </label>
                        <input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name', $store->bank_name) }}"
                               placeholder="Contoh: BCA, Mandiri, BRI, BNI"
                               class="input text-xs rounded-xl">
                    </div>

                    <div>
                        <label for="bank_account_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nomor Rekening
                        </label>
                        <input type="text" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number', $store->bank_account_number) }}"
                               placeholder="Contoh: 1234567890"
                               class="input text-xs rounded-xl font-mono">
                    </div>

                    <div>
                        <label for="bank_account_holder" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Pemilik Rekening
                        </label>
                        <input type="text" id="bank_account_holder" name="bank_account_holder" value="{{ old('bank_account_holder', $store->bank_account_holder) }}"
                               placeholder="Sesuai buku tabungan"
                               class="input text-xs rounded-xl">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('seller.dashboard') }}" class="btn-secondary text-xs h-10 px-5 rounded-xl">
                    Batal
                </a>
                <button type="submit" class="btn-primary text-xs h-10 px-6 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-bold flex items-center gap-2 shadow-xs cursor-pointer">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span>Simpan Pengaturan Toko & Alamat</span>
                </button>
            </div>
        </form>
    </div>
</x-seller-layout>
