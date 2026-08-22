<x-app-layout>
    <div class="py-8 bg-slate-50 min-h-[calc(100vh-200px)]">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-cyan-700 transition-colors font-medium">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    Kembali ke Dashboard Saya
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-card border border-slate-200/80 overflow-hidden">
                <div class="p-6 bg-slate-950 text-white border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-cyan-950 border border-cyan-400/30 flex items-center justify-center text-cyan-400 text-lg">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-white tracking-tight">Formulir Pendaftaran Toko Resmi</h1>
                            <p class="text-xs text-slate-300 mt-0.5">Buka toko Anda sendiri dan mulai berjualan ke jutaan pelanggan di NitipDong.</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('store.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Toko / Brand <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            placeholder="Contoh: Toko Berkah Elektronik"
                            class="input text-xs rounded-md">
                        <p class="text-[10px] text-slate-400 mt-1">Nama toko akan digunakan sebagai identitas resmi dan URL etalase Anda.</p>
                        @error('name')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Deskripsi Toko & Profil Produk <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="description" name="description" rows="4" required
                            placeholder="Jelaskan kategori produk yang Anda jual, komitmen kualitas, dan keunggulan toko Anda..."
                            class="input text-xs rounded-md">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    @php
                        $provincesData = \App\Services\IndonesianRegionService::PROVINCES_DATA;
                    @endphp

                    <div x-data="{
                        provinces: {{ json_encode($provincesData) }},
                        selectedProvince: 'DKI Jakarta',
                        selectedCity: 'Jakarta Pusat',
                        cities: [],
                        init() {
                            this.updateCities();
                        },
                        updateCities() {
                            if (this.provinces[this.selectedProvince]) {
                                this.cities = Object.keys(this.provinces[this.selectedProvince].cities);
                                if (!this.cities.includes(this.selectedCity)) {
                                    this.selectedCity = this.cities[0] || '';
                                }
                            } else {
                                this.cities = [];
                                this.selectedCity = '';
                            }
                        }
                    }" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label for="province" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Provinsi Asal Toko <span class="text-rose-500">*</span>
                                </label>
                                <select id="province" name="province" x-model="selectedProvince" @change="updateCities()" required
                                    class="input text-xs rounded-md">
                                    @foreach(array_keys($provincesData) as $prov)
                                        <option value="{{ $prov }}" {{ old('province', 'DKI Jakarta') === $prov ? 'selected' : '' }}>{{ $prov }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="city" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Kota / Kabupaten Asal Toko <span class="text-rose-500">*</span>
                                </label>
                                <select id="city" name="city" x-model="selectedCity" required
                                    class="input text-xs rounded-md">
                                    <template x-for="c in cities" :key="c">
                                        <option :value="c" x-text="c"></option>
                                    </template>
                                </select>
                                <p class="text-[10px] text-cyan-700 font-semibold mt-1">
                                    💡 Pelanggan di kota ini otomatis mendapatkan <strong>Gratis Ongkir Rp0</strong>.
                                </p>
                            </div>
                        </div>

                        <div>
                            <label for="address" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Alamat Lengkap Toko / Gudang <span class="text-rose-500">*</span>
                            </label>
                            <textarea id="address" name="address" rows="3" required
                                placeholder="Jalan, nomor ruko/gedung, RT/RW, kecamatan, dan detail lokasi gudang asal barang..."
                                class="input text-xs rounded-md">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="postal_code" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Kode Pos Toko
                            </label>
                            <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', '10110') }}"
                                placeholder="Contoh: 10110" maxlength="10"
                                class="input text-xs rounded-md sm:w-48">
                        </div>
                    </div>

                    <div class="p-3 bg-cyan-50 border border-cyan-200 rounded-md text-xs text-cyan-900 flex items-start gap-2.5">
                        <i class="fa-solid fa-circle-info text-cyan-600 text-sm mt-0.5"></i>
                        <p>Setelah formulir dikirim, tim operasional NitipDong akan meninjau pengajuan Anda dalam waktu 1x24 jam kerja.</p>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <a href="{{ route('customer.dashboard') }}" class="btn-secondary text-xs h-9 px-4 rounded-md">
                            Batal
                        </a>
                        <button type="submit" class="btn-primary text-xs h-9 px-5 rounded-md bg-cyan-700 hover:bg-cyan-800 flex items-center gap-1.5">
                            <i class="fa-solid fa-paper-plane text-[10px]"></i>
                            Kirim Pengajuan Buka Toko
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
