<x-admin-layout>
    <x-slot name="title">
        Buat Flash Sale Baru - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.flash_sales.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-[#12A57F] transition-colors font-medium">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali ke Daftar Flash Sale
        </a>
    </div>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-[#14213D] flex items-center gap-2" style="font-family:'Poppins',sans-serif;">
                <i class="fa-solid fa-bolt text-amber-500"></i>
                Buat Event Flash Sale Baru
            </h1>
            <p class="text-xs text-[#8A93A6] mt-0.5">Tentukan jadwal periode aktif Flash Sale. Anda dapat memilih produk dan mengatur harga diskon setelah membuat event.</p>
        </div>
    </div>

    <div class="max-w-2xl bg-white rounded-2xl shadow-sm border border-[#EFEBDF] overflow-hidden" x-data="{
        start: '{{ old('start_time', now()->format('Y-m-d\TH:i')) }}',
        end: '{{ old('end_time', now()->addHours(6)->format('Y-m-d\TH:i')) }}',
        setDuration(hours) {
            let s = new Date(this.start);
            if (isNaN(s.getTime())) s = new Date();
            let e = new Date(s.getTime() + hours * 60 * 60 * 1000);
            this.end = e.toISOString().slice(0, 16);
        }
    }">
        <form action="{{ route('admin.flash_sales.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf

            <div>
                <label for="title" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                    Nama Event Flash Sale <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="title" name="title" required placeholder="Contoh: Flash Sale Spesial Gajian, Midnight Sale 50% OFF" value="{{ old('title') }}"
                    class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] text-sm text-[#14213D] focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] transition-all">
                @error('title')
                    <p class="text-rose-500 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="start_time" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Waktu Mulai <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" id="start_time" name="start_time" x-model="start" required
                        class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] text-sm text-[#14213D] focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] transition-all">
                    @error('start_time')
                        <p class="text-rose-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_time" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Waktu Berakhir <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" id="end_time" name="end_time" x-model="end" required
                        class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] text-sm text-[#14213D] focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] transition-all">
                    @error('end_time')
                        <p class="text-rose-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-amber-50/60 p-3.5 rounded-xl border border-amber-200/60">
                <p class="text-[11px] text-amber-800 font-semibold mb-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-clock"></i> Pilih Durasi Cepat:
                </p>
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="setDuration(2)" class="px-3 py-1 bg-white border border-amber-200 rounded-lg text-xs font-medium text-amber-900 hover:bg-amber-100 transition-colors">
                        2 Jam
                    </button>
                    <button type="button" @click="setDuration(6)" class="px-3 py-1 bg-white border border-amber-200 rounded-lg text-xs font-medium text-amber-900 hover:bg-amber-100 transition-colors">
                        6 Jam
                    </button>
                    <button type="button" @click="setDuration(12)" class="px-3 py-1 bg-white border border-amber-200 rounded-lg text-xs font-medium text-amber-900 hover:bg-amber-100 transition-colors">
                        12 Jam
                    </button>
                    <button type="button" @click="setDuration(24)" class="px-3 py-1 bg-white border border-amber-200 rounded-lg text-xs font-medium text-amber-900 hover:bg-amber-100 transition-colors">
                        24 Jam (1 Hari)
                    </button>
                    <button type="button" @click="setDuration(72)" class="px-3 py-1 bg-white border border-amber-200 rounded-lg text-xs font-medium text-amber-900 hover:bg-amber-100 transition-colors">
                        3 Hari
                    </button>
                </div>
            </div>

            <div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-4 h-4 text-[#12A57F] rounded border-slate-300 focus:ring-[#12A57F]">
                    <div>
                        <span class="text-xs font-semibold text-slate-800">Aktifkan Event Langsung</span>
                        <p class="text-[11px] text-slate-400">Event akan otomatis aktif pada beranda begitu waktu mulai tiba.</p>
                    </div>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.flash_sales.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#12A57F] hover:bg-[#0f8b6a] text-white text-xs font-semibold shadow-md shadow-[#12A57F]/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-arrow-right"></i>
                    Simpan & Kelola Produk
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
