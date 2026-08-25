<x-app-layout>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <div class="page-container py-6" x-data="addressManagerComponent()">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ route('customer.dashboard') }}" class="hover:text-cyan-700 transition-colors">Dashboard</a>
                    <span>/</span>
                    <span class="text-slate-800 font-semibold">Buku Alamat</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                    <i class="fa-solid fa-location-dot text-cyan-600"></i>
                    Daftar Alamat Pengiriman
                </h1>
                <p class="text-xs text-slate-500 mt-1">Kelola alamat tujuan belanja Anda dengan data resmi wilayah Indonesia &amp; titik koordinat kurir presisi.</p>
            </div>

            <button type="button" @click="openCreateModal()"
                    class="px-4 py-2.5 bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs rounded-xl shadow-xs flex items-center gap-2 transition-all active:scale-95 cursor-pointer shrink-0">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Alamat Baru</span>
            </button>
        </div>

        @if(session('success'))
            <div class="mb-6 flex items-center gap-2.5 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl text-xs font-semibold animate-fade-up">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl text-xs font-medium space-y-1 animate-fade-up">
                <div class="font-bold flex items-center gap-1.5 text-rose-800">
                    <i class="fa-solid fa-circle-exclamation text-sm"></i> Terjadi Kesalahan:
                </div>
                <ul class="list-disc list-inside pl-1 text-[11px] text-rose-700">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($addresses as $addr)
                <div class="bg-white rounded-2xl border transition-all p-5 flex flex-col justify-between {{ $addr->is_default ? 'border-cyan-500 ring-2 ring-cyan-100 shadow-sm' : 'border-slate-200 hover:border-slate-300 shadow-2xs' }}">
                    <div>
                        
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold uppercase tracking-wider {{ $addr->label === 'Kantor' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-cyan-50 text-cyan-800 border border-cyan-200' }}">
                                    {{ $addr->label ?: 'Rumah' }}
                                </span>
                                @if($addr->is_default)
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                        <i class="fa-solid fa-check text-[9px]"></i> Utama
                                    </span>
                                @endif
                            </div>

                            @if($addr->latitude && $addr->longitude)
                                <span class="text-[10px] font-mono text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md flex items-center gap-1" title="Titik Koordinat Kurir Terpasang">
                                    <i class="fa-solid fa-location-crosshairs text-cyan-600"></i>
                                    <span>Pinpoint OK</span>
                                </span>
                            @endif
                        </div>

                        <div class="mb-2">
                            <h3 class="font-extrabold text-sm text-slate-900 leading-snug">{{ $addr->recipient_name }}</h3>
                            <p class="font-mono text-xs text-slate-500 mt-0.5">{{ $addr->phone }}</p>
                        </div>

                        <p class="text-xs text-slate-700 leading-relaxed mt-2">
                            {{ $addr->full_address }}
                        </p>
                        <p class="text-[11px] text-slate-500 font-medium mt-1">
                            @php
                                $regionParts = array_filter([
                                    $addr->village ? 'Kel. ' . $addr->village : null,
                                    $addr->district ? 'Kec. ' . $addr->district : null,
                                    $addr->city,
                                    $addr->province,
                                    $addr->postal_code
                                ]);
                            @endphp
                            {{ implode(', ', $regionParts) }}
                        </p>

                        @if($addr->notes)
                            <div class="mt-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200/80 text-[11px] text-slate-600 flex items-start gap-2">
                                <i class="fa-solid fa-map-pin text-cyan-600 text-xs mt-0.5 shrink-0"></i>
                                <span><strong class="text-slate-800">Patokan:</strong> {{ $addr->notes }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between gap-2 flex-wrap text-xs">
                        <div>
                            @if(!$addr->is_default)
                                <form action="{{ route('customer.addresses.set_default', $addr) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="font-semibold text-cyan-700 hover:text-cyan-800 hover:underline cursor-pointer text-xs">
                                        Jadikan Alamat Utama
                                    </button>
                                </form>
                            @else
                                <span class="text-[11px] font-semibold text-slate-400">Alamat Pengiriman Utama</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" @click="openEditModal({{ $addr->toJson() }}, '{{ route('customer.addresses.update', $addr) }}')"
                                    class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-cyan-50 hover:text-cyan-800 text-slate-700 font-semibold transition-colors flex items-center gap-1 cursor-pointer">
                                <i class="fa-solid fa-pen text-[10px]"></i> Edit
                            </button>

                            @if(!$addr->is_default || $addresses->count() === 1)
                                <form action="{{ route('customer.addresses.destroy', $addr) }}" method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus alamat ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-400 hover:text-rose-600 transition-colors cursor-pointer" title="Hapus Alamat">
                                        <i class="fa-regular fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-slate-200 shadow-2xs p-8">
                    <div class="w-14 h-14 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center mx-auto mb-3 text-xl">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm">Belum Ada Alamat Tersimpan</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Tambahkan alamat pengiriman Anda untuk memudahkan proses checkout dan kalkulasi ongkir ekspedisi.</p>
                    <button type="button" @click="openCreateModal()" class="mt-4 px-5 py-2.5 rounded-xl bg-cyan-700 text-white font-bold text-xs hover:bg-cyan-800 shadow-xs transition-colors cursor-pointer">
                        + Tambah Alamat Sekarang
                    </button>
                </div>
            @endforelse
        </div>

        <div x-show="showModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="showModal = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-7 shadow-2xl border border-slate-200 text-xs max-h-[92vh] flex flex-col">

                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 shrink-0">
                    <h3 class="font-extrabold text-base text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-map-location-dot text-cyan-600"></i>
                        <span x-text="isEdit ? 'Edit Alamat Pengiriman' : 'Tambah Alamat Pengiriman Baru'"></span>
                    </h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-xl flex items-center justify-center hover:bg-slate-100 transition-colors cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form :action="formData.actionUrl" method="POST" class="mt-4 space-y-4 overflow-y-auto pr-1 flex-1 scrollbar-thin">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                                <i class="fa-solid fa-map-pin text-rose-500"></i>
                                <span>Titik Lokasi Pengiriman (Pinpoint Peta):</span>
                            </label>
                            <button type="button" @click="getCurrentLocation()" :disabled="isLocating"
                                    class="px-2.5 py-1 rounded-lg bg-cyan-50 hover:bg-cyan-100 text-cyan-800 font-bold text-[11px] border border-cyan-200 flex items-center gap-1.5 transition-colors cursor-pointer disabled:opacity-50">
                                <i class="fa-solid fa-crosshairs text-cyan-600 text-xs" :class="isLocating ? 'animate-spin' : ''"></i>
                                <span x-text="isLocating ? 'Mendeteksi...' : 'Lokasi Saat Ini (GPS)'"></span>
                            </button>
                        </div>

                        <div class="relative">
                            <input type="text" x-model="searchMapQuery" @input.debounce.400ms="searchMapLocation()"
                                   placeholder="Ketik nama jalan, gedung, atau perumahan untuk mencari di peta..."
                                   class="w-full h-8 pl-8 pr-3 rounded-xl border border-slate-300 text-[11px] bg-slate-50 focus:bg-white focus:border-cyan-600">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-2.5 top-2.5 text-[10px]"></i>

                            <div x-show="searchResults.length > 0" x-cloak
                                 class="absolute left-0 right-0 top-full mt-1 bg-white rounded-xl shadow-xl border border-slate-200 z-50 overflow-hidden divide-y divide-slate-100 max-h-48 overflow-y-auto text-[11px]">
                                <template x-for="res in searchResults" :key="res.place_id">
                                    <button type="button" @click="selectSearchResult(res)"
                                            class="w-full text-left p-2.5 hover:bg-cyan-50/80 transition-colors flex items-start gap-2 cursor-pointer">
                                        <i class="fa-solid fa-location-dot text-cyan-600 text-xs mt-0.5 shrink-0"></i>
                                        <span class="text-slate-800 line-clamp-2" x-text="res.display_name"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div class="relative rounded-2xl overflow-hidden border border-slate-300 shadow-2xs">
                            <div id="address-map-container" class="w-full h-44 sm:h-52 bg-slate-100 z-10"></div>
                            <div class="absolute bottom-2 left-2 right-2 z-20 bg-slate-900/80 backdrop-blur-xs text-white p-2 rounded-xl text-[10px] flex items-center justify-between">
                                <span class="truncate">Geser pin untuk menentukan titik kurir presisi</span>
                                <span class="font-mono text-cyan-300 shrink-0" x-text="formData.latitude + ', ' + formData.longitude"></span>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="latitude" x-model="formData.latitude">
                    <input type="hidden" name="longitude" x-model="formData.longitude">

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1 text-[11px]">Label Alamat <span class="text-rose-500">*</span></label>
                            <select name="label" x-model="formData.label" required class="input text-xs">
                                <option value="Rumah">Rumah</option>
                                <option value="Kantor">Kantor</option>
                                <option value="Apartemen">Apartemen</option>
                                <option value="Kost">Kost</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1 text-[11px]">Nama Penerima <span class="text-rose-500">*</span></label>
                            <input type="text" name="recipient_name" x-model="formData.recipient_name" required placeholder="Contoh: Budi Santoso" class="input text-xs">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1 text-[11px]">No. WhatsApp / HP <span class="text-rose-500">*</span></label>
                            <input type="text" name="phone" x-model="formData.phone" required placeholder="081234567890" class="input text-xs font-mono">
                        </div>
                    </div>

                    <div class="p-3.5 bg-slate-50/80 rounded-2xl border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-slate-900 text-xs flex items-center gap-1.5">
                                <i class="fa-solid fa-landmark text-cyan-700"></i>
                                <span>Wilayah Administratif Indonesia (Resmi Kemendagri / BPS):</span>
                            </span>
                            <span class="text-[10px] text-cyan-700 font-semibold" x-show="isLoadingRegions" x-cloak>
                                <i class="fa-solid fa-spinner animate-spin"></i> Memuat wilayah...
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1 text-[11px]">Provinsi <span class="text-rose-500">*</span></label>
                                <select name="province" x-model="formData.province" @change="onProvinceSelect()" required class="input text-xs bg-white">
                                    <option value="">-- Pilih Provinsi --</option>
                                    <template x-for="p in provincesList" :key="p.id">
                                        <option :value="p.name" :selected="p.name === formData.province" x-text="p.name"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1 text-[11px]">Kota / Kabupaten <span class="text-rose-500">*</span></label>
                                <select name="city" x-model="formData.city" @change="onCitySelect()" required class="input text-xs bg-white" :disabled="regenciesList.length === 0">
                                    <option value="">-- Pilih Kota / Kabupaten --</option>
                                    <template x-for="r in regenciesList" :key="r.id">
                                        <option :value="r.name" :selected="r.name === formData.city" x-text="r.name"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1 text-[11px]">Kecamatan</label>
                                <select name="district" x-model="formData.district" @change="onDistrictSelect()" class="input text-xs bg-white" :disabled="districtsList.length === 0">
                                    <option value="">-- Pilih Kecamatan --</option>
                                    <template x-for="d in districtsList" :key="d.id">
                                        <option :value="d.name" :selected="d.name === formData.district" x-text="d.name"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1 text-[11px]">Kelurahan / Desa</label>
                                    <select name="village" x-model="formData.village" @change="onVillageSelect()" class="input text-xs bg-white" :disabled="villagesList.length === 0">
                                        <option value="">-- Pilih Desa --</option>
                                        <template x-for="v in villagesList" :key="v.id">
                                            <option :value="v.name" :selected="v.name === formData.village" x-text="v.name"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1 text-[11px]">Kode Pos</label>
                                    <input type="text" name="postal_code" x-model="formData.postal_code" placeholder="10110" class="input text-xs font-mono bg-white">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1 text-[11px]">Alamat Jalan Lengkap (Nama Jalan, No. Rumah, RT/RW, Blok) <span class="text-rose-500">*</span></label>
                        <textarea name="full_address" x-model="formData.full_address" required rows="2"
                                  placeholder="Contoh: Jl. Sudirman No. 45, RT 02/RW 05, Kelurahan Gambir"
                                  class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500"></textarea>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1 text-[11px]">Patokan Lokasi / Catatan Khusus Kurir (Opsional)</label>
                        <input type="text" name="notes" x-model="formData.notes"
                               placeholder="Contoh: Pagar hitam depan Alfamart, titip di pos satpam jika tidak ada orang"
                               class="input text-xs">
                    </div>

                    <div class="pt-2 flex items-center gap-2">
                        <input type="checkbox" id="is_default_chk" name="is_default" value="1" x-model="formData.is_default"
                               class="w-4 h-4 text-cyan-600 rounded border-slate-300 focus:ring-cyan-500 cursor-pointer">
                        <label for="is_default_chk" class="text-xs font-semibold text-slate-800 cursor-pointer">
                            Jadikan sebagai alamat pengiriman utama
                        </label>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5 shrink-0">
                        <button type="button" @click="showModal = false" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium text-xs cursor-pointer transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs shadow-xs cursor-pointer transition-all active:scale-95">
                            <span x-text="isEdit ? 'Simpan Perubahan' : 'Simpan Alamat'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        function addressManagerComponent() {
            return {
                showModal: false,
                isEdit: false,
                isLocating: false,
                isLoadingRegions: false,
                map: null,
                marker: null,
                searchMapQuery: '',
                searchResults: [],
                
                provincesList: [],
                regenciesList: [],
                districtsList: [],
                villagesList: [],

                formData: {
                    id: null,
                    label: 'Rumah',
                    recipient_name: '',
                    phone: '',
                    full_address: '',
                    province: 'DKI Jakarta',
                    city: 'Jakarta Pusat',
                    district: '',
                    village: '',
                    postal_code: '',
                    latitude: '-6.2088',
                    longitude: '106.8456',
                    notes: '',
                    is_default: false,
                    actionUrl: ''
                },

                async init() {
                    await this.loadProvinces();
                },

                async loadProvinces() {
                    try {
                        const res = await fetch('{{ route('api.regions.provinces') }}');
                        const json = await res.json();
                        if (json.success && Array.isArray(json.data)) {
                            this.provincesList = json.data;
                        }
                    } catch (e) {
                        console.error('Error loading provinces:', e);
                    }
                },

                async onProvinceSelect() {
                    const prov = this.provincesList.find(p => p.name === this.formData.province);
                    this.regenciesList = [];
                    this.districtsList = [];
                    this.villagesList = [];
                    this.formData.city = '';
                    this.formData.district = '';
                    this.formData.village = '';

                    if (!prov) return;

                    this.isLoadingRegions = true;
                    try {
                        const res = await fetch(`{{ url('/api/regions/regencies') }}/${prov.id}`);
                        const json = await res.json();
                        if (json.success && Array.isArray(json.data)) {
                            this.regenciesList = json.data;
                            if (this.regenciesList.length > 0) {
                                this.formData.city = this.regenciesList[0].name;
                                await this.onCitySelect();
                            }
                        }
                    } catch (e) {
                        console.error('Error loading regencies:', e);
                    } finally {
                        this.isLoadingRegions = false;
                    }
                },

                async onCitySelect() {
                    const reg = this.regenciesList.find(r => r.name === this.formData.city);
                    this.districtsList = [];
                    this.villagesList = [];
                    this.formData.district = '';
                    this.formData.village = '';

                    if (!reg) return;

                    this.isLoadingRegions = true;
                    try {
                        const res = await fetch(`{{ url('/api/regions/districts') }}/${reg.id}`);
                        const json = await res.json();
                        if (json.success && Array.isArray(json.data)) {
                            this.districtsList = json.data;
                        }
                    } catch (e) {
                        console.error('Error loading districts:', e);
                    } finally {
                        this.isLoadingRegions = false;
                    }
                },

                async onDistrictSelect() {
                    const dist = this.districtsList.find(d => d.name === this.formData.district);
                    this.villagesList = [];
                    this.formData.village = '';

                    if (!dist) return;

                    this.isLoadingRegions = true;
                    try {
                        const res = await fetch(`{{ url('/api/regions/villages') }}/${dist.id}`);
                        const json = await res.json();
                        if (json.success && Array.isArray(json.data)) {
                            this.villagesList = json.data;
                        }
                    } catch (e) {
                        console.error('Error loading villages:', e);
                    } finally {
                        this.isLoadingRegions = false;
                    }
                },

                onVillageSelect() {
                    // Village selected
                },

                openCreateModal() {
                    this.isEdit = false;
                    this.formData = {
                        id: null,
                        label: 'Rumah',
                        recipient_name: '{{ auth()->user()->name }}',
                        phone: '{{ auth()->user()->phone ?? '' }}',
                        full_address: '',
                        province: 'DKI Jakarta',
                        city: 'Jakarta Pusat',
                        district: '',
                        village: '',
                        postal_code: '10110',
                        latitude: '-6.2088',
                        longitude: '106.8456',
                        notes: '',
                        is_default: {{ $addresses->count() === 0 ? 'true' : 'false' }},
                        actionUrl: '{{ route('customer.addresses.store') }}'
                    };
                    this.showModal = true;
                    this.onProvinceSelect();
                    this.$nextTick(() => {
                        this.initLeafletMap();
                    });
                },

                async openEditModal(addr, actionUrl) {
                    this.isEdit = true;
                    this.formData = {
                        id: addr.id,
                        label: addr.label || 'Rumah',
                        recipient_name: addr.recipient_name,
                        phone: addr.phone,
                        full_address: addr.full_address,
                        province: addr.province || 'DKI Jakarta',
                        city: addr.city || 'Jakarta Pusat',
                        district: addr.district || '',
                        village: addr.village || '',
                        postal_code: addr.postal_code || '',
                        latitude: addr.latitude || '-6.2088',
                        longitude: addr.longitude || '106.8456',
                        notes: addr.notes || '',
                        is_default: Boolean(addr.is_default),
                        actionUrl: actionUrl
                    };
                    this.showModal = true;

                    // Load cascade options for existing values
                    const prov = this.provincesList.find(p => p.name === this.formData.province);
                    if (prov) {
                        const resReg = await fetch(`{{ url('/api/regions/regencies') }}/${prov.id}`);
                        const jsonReg = await resReg.json();
                        this.regenciesList = jsonReg.data || [];

                        const reg = this.regenciesList.find(r => r.name === this.formData.city);
                        if (reg) {
                            const resDist = await fetch(`{{ url('/api/regions/districts') }}/${reg.id}`);
                            const jsonDist = await resDist.json();
                            this.districtsList = jsonDist.data || [];

                            const dist = this.districtsList.find(d => d.name === this.formData.district);
                            if (dist) {
                                const resVil = await fetch(`{{ url('/api/regions/villages') }}/${dist.id}`);
                                const jsonVil = await resVil.json();
                                this.villagesList = jsonVil.data || [];
                            }
                        }
                    }

                    this.$nextTick(() => {
                        this.initLeafletMap();
                    });
                },

                initLeafletMap() {
                    const lat = parseFloat(this.formData.latitude) || -6.2088;
                    const lng = parseFloat(this.formData.longitude) || 106.8456;
                    const container = document.getElementById('address-map-container');

                    if (!container) return;

                    if (this.map) {
                        this.map.remove();
                    }

                    this.map = L.map('address-map-container').setView([lat, lng], 15);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(this.map);

                    this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map);

                    this.marker.on('dragend', (e) => {
                        const pos = e.target.getLatLng();
                        this.formData.latitude = pos.lat.toFixed(6);
                        this.formData.longitude = pos.lng.toFixed(6);
                        this.reverseGeocode(pos.lat, pos.lng);
                    });

                    this.map.on('click', (e) => {
                        const pos = e.latlng;
                        this.formData.latitude = pos.lat.toFixed(6);
                        this.formData.longitude = pos.lng.toFixed(6);
                        this.marker.setLatLng(pos);
                        this.reverseGeocode(pos.lat, pos.lng);
                    });

                    // Force redraw in modal
                    setTimeout(() => {
                        if (this.map) this.map.invalidateSize();
                    }, 250);
                },

                getCurrentLocation() {
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

                            if (this.map && this.marker) {
                                this.map.setView([lat, lng], 16);
                                this.marker.setLatLng([lat, lng]);
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

                async searchMapLocation() {
                    const q = this.searchMapQuery ? this.searchMapQuery.trim() : '';
                    if (q.length < 3) {
                        this.searchResults = [];
                        return;
                    }
                    try {
                        const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q + ', Indonesia')}&addressdetails=1&limit=5`);
                        const data = await res.json();
                        this.searchResults = Array.isArray(data) ? data : [];
                    } catch (e) {
                        this.searchResults = [];
                    }
                },

                selectSearchResult(res) {
                    const lat = parseFloat(res.lat);
                    const lng = parseFloat(res.lon);
                    this.formData.latitude = lat.toFixed(6);
                    this.formData.longitude = lng.toFixed(6);

                    if (this.map && this.marker) {
                        this.map.setView([lat, lng], 16);
                        this.marker.setLatLng([lat, lng]);
                    }

                    this.searchResults = [];
                    this.searchMapQuery = '';
                    this.reverseGeocode(lat, lng);
                },

                async reverseGeocode(lat, lng) {
                    try {
                        const res = await fetch(`{{ route('api.regions.reverse_geocode') }}?lat=${lat}&lng=${lng}`);
                        const json = await res.json();
                        if (json.success && json.data) {
                            const d = json.data;
                            if (d.street && !this.formData.full_address) {
                                this.formData.full_address = d.street;
                            } else if (d.display_name && !this.formData.full_address) {
                                this.formData.full_address = d.display_name;
                            }
                            if (d.postal_code) this.formData.postal_code = d.postal_code;
                        }
                    } catch (e) {
                        console.error('Reverse geocode error:', e);
                    }
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
