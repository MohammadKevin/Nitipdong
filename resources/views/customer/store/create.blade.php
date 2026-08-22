<x-app-layout>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <div class="py-8 bg-slate-50 min-h-[calc(100vh-200px)]">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-cyan-700 transition-colors font-medium">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    Kembali ke Dashboard Saya
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-card border border-slate-200/80 overflow-hidden">
                <div class="p-6 bg-slate-950 text-white border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-cyan-950 border border-cyan-400/30 flex items-center justify-center text-cyan-400 text-lg shadow-xs">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-white tracking-tight">Formulir Pendaftaran Toko Resmi</h1>
                            <p class="text-xs text-slate-300 mt-0.5">Buka toko Anda sendiri dan mulai berjualan ke jutaan pelanggan di NitipDong.</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('store.store') }}" method="POST" class="p-6 sm:p-7 space-y-4"
                      x-data="{
                          provincesList: [],
                          regenciesList: [],
                          districtsList: [],
                          selectedProvince: '{{ old('province', 'DKI Jakarta') }}',
                          selectedCity: '{{ old('city', 'Jakarta Pusat') }}',
                          selectedDistrict: '{{ old('district', '') }}',
                          postalCode: '{{ old('postal_code', '10110') }}',
                          fullAddress: '{{ old('address', '') }}',
                          lat: '{{ old('latitude', '-6.2088') }}',
                          lng: '{{ old('longitude', '106.8456') }}',
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
                              const mapContainer = document.getElementById('store-register-map');
                              if (!mapContainer) return;

                              const curLat = parseFloat(this.lat) || -6.2088;
                              const curLng = parseFloat(this.lng) || 106.8456;

                              this.map = L.map('store-register-map').setView([curLat, curLng], 14);
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
                                      alert('Gagal mengambil lokasi GPS: ' + err.message);
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

                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Toko / Brand <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            placeholder="Contoh: Toko Berkah Elektronik"
                            class="input text-xs rounded-xl">
                        <p class="text-[10px] text-slate-400 mt-1">Nama toko akan digunakan sebagai identitas resmi dan URL etalase Anda.</p>
                        @error('name')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Deskripsi Toko &amp; Profil Produk <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="description" name="description" rows="3" required
                            placeholder="Jelaskan kategori produk yang Anda jual, komitmen kualitas, dan keunggulan toko Anda..."
                            class="input text-xs rounded-xl">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Cascading Regions --}}
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3.5">
                        <span class="font-extrabold text-slate-900 text-xs flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot text-cyan-600"></i>
                            <span>Wilayah Asal Pengiriman Toko:</span>
                        </span>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label for="province" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Provinsi Asal Toko <span class="text-rose-500">*</span>
                                </label>
                                <select id="province" name="province" x-model="selectedProvince" @change="onProvinceChange()" required
                                    class="input text-xs rounded-xl bg-white">
                                    <template x-for="p in provincesList" :key="p.id">
                                        <option :value="p.name" :selected="p.name === selectedProvince" x-text="p.name"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <label for="city" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Kota / Kabupaten Asal Toko <span class="text-rose-500">*</span>
                                </label>
                                <select id="city" name="city" x-model="selectedCity" @change="onCityChange()" required
                                    class="input text-xs rounded-xl font-bold text-slate-900 border-cyan-300 bg-cyan-50/30" :disabled="regenciesList.length === 0">
                                    <template x-for="c in regenciesList" :key="c.id">
                                        <option :value="c.name" :selected="c.name === selectedCity" x-text="c.name"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label for="district" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
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
                                <label for="postal_code" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Kode Pos Gudang
                                </label>
                                <input type="text" id="postal_code" name="postal_code" x-model="postalCode"
                                    placeholder="Contoh: 10110" maxlength="10"
                                    class="input text-xs rounded-xl font-mono bg-white">
                            </div>
                        </div>

                        <div>
                            <label for="address" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Alamat Lengkap Toko / Gudang <span class="text-rose-500">*</span>
                            </label>
                            <textarea id="address" name="address" x-model="fullAddress" rows="2" required
                                placeholder="Jalan, nomor ruko/gedung, RT/RW, dan patokan lokasi gudang asal barang..."
                                class="input text-xs rounded-xl bg-white">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Leaflet Map Pinpoint --}}
                        <div class="space-y-2 pt-2 border-t border-slate-200/80">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                                    Titik GPS Lokasi Toko (Pinpoint Maps)
                                </label>
                                <button type="button" @click="getCurrentLocation()" :disabled="isLocating"
                                        class="text-xs font-bold text-cyan-700 hover:text-cyan-800 bg-white hover:bg-cyan-50 px-3 py-1 rounded-lg border border-cyan-300 flex items-center gap-1.5 transition-all cursor-pointer disabled:opacity-50">
                                    <i class="fa-solid fa-location-crosshairs text-xs" :class="isLocating ? 'animate-spin' : ''"></i>
                                    <span x-text="isLocating ? 'Mendeteksi...' : 'Gunakan Lokasi GPS'"></span>
                                </button>
                            </div>

                            <div id="store-register-map" class="w-full h-48 rounded-xl border border-slate-300 shadow-inner z-0"></div>

                            <input type="hidden" name="latitude" x-model="lat">
                            <input type="hidden" name="longitude" x-model="lng">
                        </div>
                    </div>

                    <div class="p-3 bg-cyan-50 border border-cyan-200 rounded-xl text-xs text-cyan-900 flex items-start gap-2.5">
                        <i class="fa-solid fa-circle-info text-cyan-600 text-sm mt-0.5 shrink-0"></i>
                        <p>Setelah formulir dikirim, tim operasional NitipDong akan meninjau pengajuan Anda dalam waktu 1x24 jam kerja.</p>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <a href="{{ route('customer.dashboard') }}" class="btn-secondary text-xs h-9 px-4 rounded-xl">
                            Batal
                        </a>
                        <button type="submit" class="btn-primary text-xs h-9 px-5 rounded-xl bg-cyan-700 hover:bg-cyan-800 flex items-center gap-1.5 font-bold shadow-xs">
                            <i class="fa-solid fa-paper-plane text-[10px]"></i>
                            Kirim Pengajuan Buka Toko
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
