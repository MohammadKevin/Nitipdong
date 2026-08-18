<x-admin-layout>
    <x-slot name="title">
        Edit Flash Sale: {{ $flashSale->title }} - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="mb-3">
        <a href="{{ route('admin.flash_sales.show', $flashSale) }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-cyan-700 transition-colors font-medium">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            Kembali ke Manajemen Produk Flash Sale
        </a>
    </div>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-5">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-cyan-700"></i>
                Edit Event Flash Sale: {{ $flashSale->title }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Perbarui jadwal waktu pelaksanaan atau status aktif sesi Flash Sale.</p>
        </div>
    </div>

    <div class="max-w-2xl bg-white rounded-xl shadow-card border border-slate-200/80 overflow-hidden" x-data="{
        start: '{{ old('start_time', $flashSale->start_time->format('Y-m-d\TH:i')) }}',
        end: '{{ old('end_time', $flashSale->end_time->format('Y-m-d\TH:i')) }}',
        setDuration(hours) {
            let s = new Date(this.start);
            if (isNaN(s.getTime())) s = new Date();
            let e = new Date(s.getTime() + hours * 60 * 60 * 1000);
            this.end = e.toISOString().slice(0, 16);
        }
    }">
        <form action="{{ route('admin.flash_sales.update', $flashSale) }}" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Event Flash Sale <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="title" name="title" required placeholder="Contoh: Flash Sale Spesial Gajian" value="{{ old('title', $flashSale->title) }}"
                    class="input text-xs rounded-md">
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
                        class="input text-xs rounded-md">
                    @error('start_time')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_time" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Waktu Berakhir <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" id="end_time" name="end_time" x-model="end" required
                        class="input text-xs rounded-md">
                    @error('end_time')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-amber-50 p-3 rounded-md border border-amber-200">
                <p class="text-[11px] text-amber-800 font-semibold mb-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-clock"></i> Pilih Durasi Cepat:
                </p>
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" @click="setDuration(2)" class="px-2.5 py-0.5 bg-white border border-amber-200 rounded text-xs font-medium text-amber-900 hover:bg-amber-100 transition-colors">
                        2 Jam
                    </button>
                    <button type="button" @click="setDuration(6)" class="px-2.5 py-0.5 bg-white border border-amber-200 rounded text-xs font-medium text-amber-900 hover:bg-amber-100 transition-colors">
                        6 Jam
                    </button>
                    <button type="button" @click="setDuration(12)" class="px-2.5 py-0.5 bg-white border border-amber-200 rounded text-xs font-medium text-amber-900 hover:bg-amber-100 transition-colors">
                        12 Jam
                    </button>
                    <button type="button" @click="setDuration(24)" class="px-2.5 py-0.5 bg-white border border-amber-200 rounded text-xs font-medium text-amber-900 hover:bg-amber-100 transition-colors">
                        24 Jam (1 Hari)
                    </button>
                </div>
            </div>

            <div>
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $flashSale->is_active) ? 'checked' : '' }}
                        class="w-4 h-4 text-cyan-600 rounded border-slate-300 focus:ring-cyan-500">
                    <div>
                        <span class="text-xs font-semibold text-slate-800">Status Aktif</span>
                        <p class="text-[11px] text-slate-400">Jika dinonaktifkan, event tidak akan muncul di etalase pembeli.</p>
                    </div>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <a href="{{ route('admin.flash_sales.show', $flashSale) }}" class="btn-secondary text-xs h-9 px-4 rounded-md">
                    Batal
                </a>
                <button type="submit" class="btn-primary text-xs h-9 px-5 rounded-md bg-cyan-700 hover:bg-cyan-800">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
