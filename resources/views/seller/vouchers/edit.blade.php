<x-seller-layout>
    <x-slot name="title">
        Edit Voucher: {{ $voucher->code }} - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="mb-2">
        <a href="{{ route('seller.vouchers.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-[#12A57F] transition-colors font-medium">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali ke Daftar Voucher
        </a>
    </div>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-2">
        <div>
            <h1 class="text-xl font-bold text-[#14213D] flex items-center gap-2" style="font-family:'Poppins',sans-serif;">
                <i class="fa-solid fa-pen-to-square text-[#12A57F]"></i>
                Edit Voucher Toko: {{ $voucher->code }}
            </h1>
            <p class="text-xs text-[#8A93A6] mt-0.5">Perbarui detail kupon, kuota, atau masa aktif promosi toko Anda.</p>
        </div>
    </div>

    <div class="max-w-3xl bg-white rounded-2xl shadow-sm border border-[#EFEBDF] overflow-hidden"
         x-data="{
             type: '{{ old('type', $voucher->type) }}',
             amount: {{ old('amount', $voucher->amount) }},
             minSpend: {{ old('min_spend', $voucher->min_spend) }},
             maxDiscount: {{ old('max_discount', $voucher->max_discount ?? 'null') }}
         }">
        <form action="{{ route('seller.vouchers.update', $voucher) }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="code" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Kode Voucher Promo <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="code" id="code" value="{{ old('code', $voucher->code) }}" required
                        class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] text-sm font-mono uppercase text-[#14213D] focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] transition-all">
                    @error('code')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Nama Promosi Voucher <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $voucher->name) }}" required
                        class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] text-sm text-[#14213D] focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] transition-all">
                    @error('name')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="description" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                    Deskripsi Syarat & Ketentuan (Opsional)
                </label>
                <textarea name="description" id="description" rows="3"
                    class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] text-sm text-[#14213D] focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] transition-all">{{ old('description', $voucher->description) }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/60 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Tipe Diskon <span class="text-rose-500">*</span>
                        </label>
                        <select name="type" x-model="type" required
                            class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] text-sm text-[#14213D] focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] bg-white transition-all">
                            <option value="percent">Persentase (%)</option>
                            <option value="fixed">Nominal Tetap (Rp)</option>
                        </select>
                    </div>

                    <div>
                        <label for="amount" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            <span x-text="type === 'percent' ? 'Persentase Diskon (%)' : 'Nominal Potongan (Rp)'"></span> <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="amount" id="amount" x-model.number="amount" value="{{ old('amount', $voucher->amount) }}" required min="1"
                            class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] text-sm text-[#14213D] focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] bg-white transition-all">
                        @error('amount')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="min_spend" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Minimal Belanja (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="min_spend" id="min_spend" x-model.number="minSpend" value="{{ old('min_spend', $voucher->min_spend) }}" required min="0"
                            class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] text-sm text-[#14213D] focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] bg-white transition-all">
                        @error('min_spend')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-show="type === 'percent'">
                        <label for="max_discount" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Maksimal Potongan (Rp) <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span>
                        </label>
                        <input type="number" name="max_discount" id="max_discount" x-model.number="maxDiscount" value="{{ old('max_discount', $voucher->max_discount) }}" min="1"
                            placeholder="Kosongkan jika tanpa batas"
                            class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] text-sm text-[#14213D] focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] bg-white transition-all">
                        @error('max_discount')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="quota" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Kuota Klaim <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="quota" id="quota" value="{{ old('quota', $voucher->quota) }}" required min="1"
                        class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] text-sm text-[#14213D] focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] transition-all">
                    @error('quota')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="start_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Tanggal Mulai
                    </label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $voucher->start_date ? $voucher->start_date->format('Y-m-d') : '') }}"
                        class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] text-sm text-[#14213D] focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] transition-all">
                </div>

                <div>
                    <label for="end_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Tanggal Selesai
                    </label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $voucher->end_date ? $voucher->end_date->format('Y-m-d') : '') }}"
                        class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] text-sm text-[#14213D] focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] transition-all">
                </div>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $voucher->is_active) ? 'checked' : '' }}
                        class="w-4 h-4 text-[#12A57F] rounded border-slate-300 focus:ring-[#12A57F]">
                    <div>
                        <span class="text-xs font-semibold text-slate-800">Status Voucher Aktif</span>
                        <p class="text-[11px] text-slate-400">Pembeli dapat menggunakan kode voucher ini pada keranjang belanja jika dicentang.</p>
                    </div>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('seller.vouchers.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#12A57F] hover:bg-[#0f8b6a] text-white text-xs font-semibold shadow-md shadow-[#12A57F]/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    Simpan Perubahan Voucher
                </button>
            </div>
        </form>
    </div>
</x-seller-layout>
