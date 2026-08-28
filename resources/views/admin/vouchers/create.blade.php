<x-admin-layout>
    <x-slot name="title">
        Buat Voucher Platform Baru - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Buat Voucher Platform
    </x-slot>

    @php
        $isSuperAdmin = auth()->user()->role === 'super_admin';
        $indexRoute = $isSuperAdmin ? route('super_admin.vouchers.index') : route('admin.vouchers.index');
        $storeRoute = $isSuperAdmin ? route('super_admin.vouchers.store') : route('admin.vouchers.store');
    @endphp

    <div class="mb-3">
        <a href="{{ $indexRoute }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-blue-700 transition-colors font-medium">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
            </svg>
            Kembali ke Daftar Voucher
        </a>
    </div>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                Buat Voucher Platform Baru
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Voucher ini berlaku untuk seluruh pembeli dan dapat diklaim saat checkout.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs">
            <p class="font-bold mb-1">Terdapat kesalahan pengisian formulir:</p>
            <ul class="list-disc list-inside space-y-0.5 text-rose-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 max-w-3xl"
         x-data="{
             type: '{{ old('type', 'percent') }}',
             amount: '{{ old('amount', 20) }}',
             minSpend: '{{ old('min_spend', 500000) }}',
             code: '{{ old('code', 'NEWUSER20') }}',
             name: '{{ old('name', 'Diskon 20% Pengguna Baru') }}'
         }">

        <form action="{{ $storeRoute }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kode Voucher <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="code" x-model="code" required maxlength="50"
                           class="w-full h-10 px-3 text-xs uppercase font-mono font-bold bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                           placeholder="CONTOH: NEWUSER20">
                    <p class="text-[11px] text-slate-400 mt-1">Kode unik huruf &amp; angka tanpa spasi.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Promo / Voucher <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" x-model="name" required maxlength="150"
                           class="w-full h-10 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                           placeholder="Contoh: Diskon 20% Min Belanja 500k">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Deskripsi / Syarat &amp; Ketentuan Promo
                </label>
                <textarea name="description" rows="2"
                          class="w-full p-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                          placeholder="Contoh: Berlaku untuk semua produk dengan minimum belanja Rp 500.000 khusus transaksi pertama.">{{ old('description', 'Kupon diskon 20% khusus pengguna baru dengan minimum transaksi Rp 500.000.') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tipe Potongan <span class="text-rose-500">*</span>
                    </label>
                    <select name="type" x-model="type" required
                            class="w-full h-10 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 cursor-pointer">
                        <option value="percent">Persentase (%)</option>
                        <option value="fixed">Nominal Tetap (Rp)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Besaran Diskon <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="amount" x-model="amount" required min="1" step="any"
                               class="w-full h-10 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-bold"
                               placeholder="20">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400"
                              x-text="type === 'percent' ? '%' : 'Rp'"></span>
                    </div>
                </div>

                <div x-show="type === 'percent'">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Maksimal Diskon (Rp)
                    </label>
                    <input type="number" name="max_discount" value="{{ old('max_discount', 100000) }}" min="0" step="1000"
                           class="w-full h-10 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                           placeholder="Contoh: 100000">
                    <p class="text-[10px] text-slate-400 mt-1">Kosongkan jika tanpa batas.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Minimum Belanja (Rp)
                    </label>
                    <input type="number" name="min_spend" x-model="minSpend" min="0" step="1000"
                           class="w-full h-10 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-semibold"
                           placeholder="500000">
                    <p class="text-[10px] text-slate-400 mt-1">0 jika tanpa minimum belanja.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kuota Klaim <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="quota" value="{{ old('quota', 100) }}" required min="1"
                           class="w-full h-10 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-bold"
                           placeholder="100">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kedaluwarsa
                    </label>
                    <input type="date" name="expires_at" value="{{ old('expires_at') }}"
                           class="w-full h-10 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 cursor-pointer">
                    <p class="text-[10px] text-slate-400 mt-1">Kosongkan jika berlaku selamanya.</p>
                </div>
            </div>

            <div class="pt-2">
                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                    <span class="text-xs font-bold text-slate-800">Aktifkan Voucher Langsung</span>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ $indexRoute }}" class="h-10 px-4 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold text-xs transition-colors flex items-center">
                    Batal
                </a>
                <button type="submit" class="h-10 px-5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs flex items-center gap-2 shadow-xs transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan &amp; Terbitkan Voucher</span>
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
