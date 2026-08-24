<x-admin-layout>
    <x-slot name="title">
        Buat Flash Sale Baru - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Buat Flash Sale Baru
    </x-slot>

    <div class="mb-3">
        <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.flash_sales.index') : route('admin.flash_sales.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-blue-700 transition-colors font-medium">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
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

    <div class="max-w-2xl bg-white rounded-lg shadow-xs border border-slate-200/90 overflow-hidden" x-data="{
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
                <label for="title" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5 font-mono-num">
                    Nama Event Flash Sale <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="title" name="title" required placeholder="Contoh: Flash Sale Spesial Gajian, Midnight Sale 50% OFF" value="{{ old('title') }}"
                    class="w-full h-8.5 px-3 rounded-lg border border-slate-200 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                @error('title')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="start_time" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5 font-mono-num">
                        Waktu Mulai <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" id="start_time" name="start_time" x-model="start" required
                        class="w-full h-8.5 px-3 rounded-lg border border-slate-200 text-xs font-mono-num focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    @error('start_time')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_time" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5 font-mono-num">
                        Waktu Berakhir <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" id="end_time" name="end_time" x-model="end" required
                        class="w-full h-8.5 px-3 rounded-lg border border-slate-200 text-xs font-mono-num focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    @error('end_time')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-amber-50 p-3 rounded-lg border border-amber-200">
                <p class="text-[11px] text-amber-800 font-semibold mb-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-clock"></i> Pilih Durasi Cepat:
                </p>
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" @click="setDuration(2)" class="px-2.5 py-0.5 bg-white border border-amber-200 rounded text-xs font-medium text-amber-900 hover:bg-amber-100 transition-colors cursor-pointer">
                        2 Jam
                    </button>
                    <button type="button" @click="setDuration(6)" class="px-2.5 py-0.5 bg-white border border-amber-200 rounded text-xs font-medium text-amber-900 hover:bg-amber-100 transition-colors cursor-pointer">
                        6 Jam
                    </button>
                    <button type="button" @click="setDuration(12)" class="px-2.5 py-0.5 bg-white border border-amber-200 rounded text-xs font-medium text-amber-900 hover:bg-amber-100 transition-colors cursor-pointer">
                        12 Jam
                    </button>
                    <button type="button" @click="setDuration(24)" class="px-2.5 py-0.5 bg-white border border-amber-200 rounded text-xs font-medium text-amber-900 hover:bg-amber-100 transition-colors cursor-pointer">
                        24 Jam (1 Hari)
                    </button>
                </div>
            </div>

            <div>
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                    <div>
                        <span class="text-xs font-semibold text-slate-800">Aktifkan Event Langsung</span>
                        <p class="text-[11px] text-slate-400">Event akan otomatis aktif pada beranda begitu waktu mulai tiba.</p>
                    </div>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.flash_sales.index') : route('admin.flash_sales.index') }}" class="h-8.5 px-3.5 flex items-center justify-center rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-semibold">
                    Batal
                </a>
                <button type="submit" class="h-8.5 px-4 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs flex items-center gap-1.5 shadow-xs transition-colors cursor-pointer">
                    <span>Simpan & Kelola Produk</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
