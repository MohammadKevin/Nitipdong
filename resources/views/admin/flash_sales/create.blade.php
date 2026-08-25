<x-admin-layout>
    <x-slot name="title">
        Buat Flash Sale Baru - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Buat Flash Sale Baru
    </x-slot>

    <div class="mb-3">
        <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.flash_sales.index') : route('admin.flash_sales.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-blue-700 transition-colors font-medium">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
            </svg>
            Kembali ke Daftar Flash Sale
        </a>
    </div>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                Buat Sesi Flash Sale Baru
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Tentukan periode aktif Flash Sale. Anda dapat memilih produk dan mengatur harga diskon setelah membuat event.</p>
        </div>
    </div>

    <div class="max-w-2xl bg-white rounded-xl shadow-xs border border-slate-200/90 overflow-hidden" x-data="{
        start: '{{ old('start_time', now()->format('Y-m-d\TH:i')) }}',
        end: '{{ old('end_time', now()->addHours(6)->format('Y-m-d\TH:i')) }}',
        setDuration(hours) {
            let s = new Date(this.start);
            if (isNaN(s.getTime())) s = new Date();
            let e = new Date(s.getTime() + hours * 60 * 60 * 1000);
            this.end = e.toISOString().slice(0, 16);
        }
    }">
        <form action="{{ auth()->user()->role === 'super_admin' ? route('super_admin.flash_sales.store') : route('admin.flash_sales.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div>
                <label for="title" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Event Flash Sale <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="title" name="title" required placeholder="Contoh: Flash Sale Spesial Gajian, Midnight Sale 50% OFF" value="{{ old('title') }}"
                    class="w-full h-9 px-3 rounded-lg border border-slate-200 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                @error('title')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="start_time" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Waktu Mulai <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" id="start_time" name="start_time" x-model="start" required
                        class="w-full h-9 px-3 rounded-lg border border-slate-200 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    @error('start_time')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_time" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Waktu Selesai <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" id="end_time" name="end_time" x-model="end" required
                        class="w-full h-9 px-3 rounded-lg border border-slate-200 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    @error('end_time')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                <span class="text-[11px] font-semibold text-slate-600 block mb-1.5">Preset Durasi Cepat:</span>
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="setDuration(2)" class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 transition-colors">2 Jam</button>
                    <button type="button" @click="setDuration(4)" class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 transition-colors">4 Jam</button>
                    <button type="button" @click="setDuration(6)" class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 transition-colors">6 Jam</button>
                    <button type="button" @click="setDuration(12)" class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 transition-colors">12 Jam</button>
                    <button type="button" @click="setDuration(24)" class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 transition-colors">24 Jam (1 Hari)</button>
                </div>
            </div>

            <div>
                <label class="flex items-center gap-2 cursor-pointer mt-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                        class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-xs font-medium text-slate-700">Langsung aktifkan sesi event ini di platform</span>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.flash_sales.index') : route('admin.flash_sales.index') }}" class="px-4 py-2 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold text-xs transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition-colors shadow-xs cursor-pointer">
                    Simpan &amp; Lanjut Pilih Produk &rarr;
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
