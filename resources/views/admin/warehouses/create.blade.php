<x-admin-layout>
    <x-slot name="title">
        Tambah Gudang Hub NDX - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Tambah Gudang Hub NDX
    </x-slot>

    @php
        $isSuperAdmin = auth()->user()->role === 'super_admin';
        $prefix = $isSuperAdmin ? 'super_admin' : 'admin';
    @endphp

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center gap-3">
            <a href="{{ route($prefix . '.warehouses.index') }}" class="w-9 h-9 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 flex items-center justify-center text-slate-600 transition-colors shadow-2xs">
                <i class="fa-solid fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">Tambah Gudang Hub DC Baru</h1>
                <p class="text-xs text-slate-500 mt-0.5">Daftarkan pusat distribusi & sortir ekspedisi NitipDongExpress.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
            <form action="{{ route($prefix . '.warehouses.store') }}" method="POST" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Kode Hub NDX <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="code" value="{{ old('code', 'NDX-' . strtoupper(\Illuminate\Support\Str::random(3)) . '-01') }}" required
                               placeholder="Contoh: NDX-JKT-01"
                               class="w-full py-2.5 px-3.5 rounded-xl border border-slate-300 font-mono text-xs focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        @error('code')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nama Gudang Hub <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="Contoh: NDX Hub DC Surabaya"
                               class="w-full py-2.5 px-3.5 rounded-xl border border-slate-300 text-xs focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        @error('name')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Kota / Kabupaten <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="city" value="{{ old('city') }}" required
                               placeholder="Contoh: Surabaya"
                               class="w-full py-2.5 px-3.5 rounded-xl border border-slate-300 text-xs focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        @error('city')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Provinsi <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="province" value="{{ old('province') }}" required
                               placeholder="Contoh: Jawa Timur"
                               class="w-full py-2.5 px-3.5 rounded-xl border border-slate-300 text-xs focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        @error('province')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Alamat Lengkap Gudang <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="address" rows="3" required
                              placeholder="Alamat jalan, nomor gudang, kawasan industri, kode pos..."
                              class="w-full py-2.5 px-3.5 rounded-xl border border-slate-300 text-xs focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Latitude (GPS) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" step="any" name="lat" value="{{ old('lat', '-6.2088') }}" required
                               placeholder="-6.2088"
                               class="w-full py-2.5 px-3.5 rounded-xl border border-slate-300 font-mono text-xs focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        @error('lat')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Longitude (GPS) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" step="any" name="lng" value="{{ old('lng', '106.8456') }}" required
                               placeholder="106.8456"
                               class="w-full py-2.5 px-3.5 rounded-xl border border-slate-300 font-mono text-xs focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        @error('lng')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            No. Telepon / Hotline
                        </label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               placeholder="Contoh: 021-34567890"
                               class="w-full py-2.5 px-3.5 rounded-xl border border-slate-300 text-xs focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nama PIC / Kepala Gudang
                        </label>
                        <input type="text" name="pic_name" value="{{ old('pic_name') }}"
                               placeholder="Contoh: Budi Santoso"
                               class="w-full py-2.5 px-3.5 rounded-xl border border-slate-300 text-xs focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                    </div>
                </div>

                <div class="pt-2">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="rounded text-cyan-600 focus:ring-cyan-500">
                        <span class="text-xs font-semibold text-slate-700">Aktifkan gudang hub ini untuk operasional pengiriman</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route($prefix . '.warehouses.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white text-xs font-bold shadow-xs transition-colors">
                        Simpan Gudang Hub
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
