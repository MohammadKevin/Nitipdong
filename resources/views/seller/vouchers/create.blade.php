<x-seller-layout>
    <x-slot name="title">
        Buat Voucher Toko Baru - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="mb-3">
        <a href="{{ route('seller.vouchers.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-cyan-700 transition-colors font-medium">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            Kembali ke Daftar Voucher
        </a>
    </div>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-5">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-ticket text-cyan-700"></i>
                Buat Voucher Toko Baru
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Atur kode promo, batas potongan, minimal belanja, dan kuota pemakaian untuk toko Anda.</p>
        </div>
    </div>

    <div class="max-w-3xl bg-white rounded-xl shadow-card border border-slate-200/80 overflow-hidden"
         x-data="{
             type: '{{ old('type', 'percent') }}',
             amount: {{ old('amount', 10) }},
             minSpend: {{ old('min_spend', 50000) }},
             maxDiscount: {{ old('max_discount', 20000) }}
         }">
        <form action="{{ route('seller.vouchers.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="code" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kode Voucher Promo <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required
                        placeholder="Misal: TOKOBARU10"
                        class="input text-xs font-mono uppercase rounded-md">
                    <p class="text-[10px] text-slate-400 mt-1">Gunakan huruf & angka tanpa spasi.</p>
                    @error('code')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Promosi Voucher <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        placeholder="Misal: Diskon Pelanggan Baru 10%"
                        class="input text-xs rounded-md">
                    @error('name')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="description" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Deskripsi Syarat & Ketentuan (Opsional)
                </label>
                <textarea name="description" id="description" rows="3"
                    placeholder="Contoh: Potongan khusus 10% untuk semua produk di toko kami minimal belanja Rp50.000."
                    class="input text-xs rounded-md">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 space-y-3.5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tipe Diskon <span class="text-rose-500">*</span>
                        </label>
                        <select name="type" x-model="type" required
                            class="input text-xs rounded-md bg-white">
                            <option value="percent">Persentase (%)</option>
                            <option value="fixed">Nominal Tetap (Rp)</option>
                        </select>
                    </div>

                    <div>
                        <label for="amount" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            <span x-text="type === 'percent' ? 'Persentase Diskon (%)' : 'Nominal Potongan (Rp)'"></span> <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="amount" id="amount" x-model.number="amount" value="{{ old('amount', 10) }}" required min="1"
                            class="input text-xs rounded-md bg-white">
                        @error('amount')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="min_spend" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Minimal Belanja (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="min_spend" id="min_spend" x-model.number="minSpend" value="{{ old('min_spend', 50000) }}" required min="0"
                            class="input text-xs rounded-md bg-white">
                        @error('min_spend')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-show="type === 'percent'">
                        <label for="max_discount" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Maksimal Diskon (Rp)
                        </label>
                        <input type="number" name="max_discount" id="max_discount" x-model.number="maxDiscount" value="{{ old('max_discount', 20000) }}" min="0"
                            placeholder="Dikosongkan jika tanpa batas"
                            class="input text-xs rounded-md bg-white">
                        @error('max_discount')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="quota" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kuota Jumlah Pemakaian <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="quota" id="quota" value="{{ old('quota', 50) }}" required min="1"
                        placeholder="Contoh: 50"
                        class="input text-xs rounded-md bg-white">
                    @error('quota')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tanggal Mulai Berlaku
                    </label>
                    <input type="datetime-local" name="start_date" id="start_date" value="{{ old('start_date', now()->format('Y-m-d\TH:i')) }}"
                        class="input text-xs rounded-md">
                    @error('start_date')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tanggal Berakhir
                    </label>
                    <input type="datetime-local" name="end_date" id="end_date" value="{{ old('end_date', now()->addDays(30)->format('Y-m-d\TH:i')) }}"
                        class="input text-xs rounded-md">
                    @error('end_date')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-4 h-4 text-cyan-600 rounded border-slate-300 focus:ring-cyan-500">
                    <div>
                        <span class="text-xs font-semibold text-slate-800">Aktifkan Voucher Sekarang</span>
                        <p class="text-[11px] text-slate-400">Pembeli dapat langsung mengklaim dan menggunakan voucher ini di checkout.</p>
                    </div>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <a href="{{ route('seller.vouchers.index') }}" class="btn-secondary text-xs h-9 px-4 rounded-md">
                    Batal
                </a>
                <button type="submit" class="btn-primary text-xs h-9 px-5 rounded-md bg-cyan-700 hover:bg-cyan-800 flex items-center gap-1.5">
                    <i class="fa-solid fa-check text-[10px]"></i>
                    Simpan & Terbitkan Voucher
                </button>
            </div>
        </form>
    </div>
</x-seller-layout>
