<x-admin-layout>
    <x-slot name="title">
        Edit Voucher {{ $voucher->code }} - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Edit Voucher
    </x-slot>

    @php
        $isSuperAdmin = auth()->user()->role === 'super_admin';
        $indexRoute = $isSuperAdmin ? route('super_admin.vouchers.index') : route('admin.vouchers.index');
        $updateRoute = $isSuperAdmin ? route('super_admin.vouchers.update', $voucher) : route('admin.vouchers.update', $voucher);
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
                Edit Voucher: <span class="font-mono text-blue-600">{{ $voucher->code }}</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Perbarui ketentuan diskon, kuota, atau tanggal aktif voucher.</p>
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
             type: '{{ old('type', $voucher->type) }}',
             amount: '{{ old('amount', $voucher->amount) }}'
         }">

        <form action="{{ $updateRoute }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kode Voucher <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code', $voucher->code) }}" required maxlength="50"
                           class="w-full h-10 px-3 text-xs uppercase font-mono font-bold bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Promo / Voucher <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $voucher->name) }}" required maxlength="150"
                           class="w-full h-10 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Deskripsi / Syarat &amp; Ketentuan Promo
                </label>
                <textarea name="description" rows="2"
                          class="w-full p-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ old('description', $voucher->description) }}</textarea>
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
                               class="w-full h-10 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-bold">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400"
                              x-text="type === 'percent' ? '%' : 'Rp'"></span>
                    </div>
                </div>

                <div x-show="type === 'percent'">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Maksimal Diskon (Rp)
                    </label>
                    <input type="number" name="max_discount" value="{{ old('max_discount', $voucher->max_discount) }}" min="0" step="1000"
                           class="w-full h-10 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Minimum Belanja (Rp)
                    </label>
                    <input type="number" name="min_spend" value="{{ old('min_spend', $voucher->min_spend) }}" min="0" step="1000"
                           class="w-full h-10 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-semibold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kuota Klaim <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="quota" value="{{ old('quota', $voucher->quota) }}" required min="0"
                           class="w-full h-10 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-bold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kedaluwarsa
                    </label>
                    <input type="date" name="expires_at" value="{{ old('expires_at', $voucher->expires_at?->format('Y-m-d')) }}"
                           class="w-full h-10 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 cursor-pointer">
                </div>
            </div>

            <div class="pt-2">
                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $voucher->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                    <span class="text-xs font-bold text-slate-800">Status Voucher Aktif</span>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ $indexRoute }}" class="h-10 px-4 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold text-xs transition-colors flex items-center">
                    Batal
                </a>
                <button type="submit" class="h-10 px-5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs flex items-center gap-2 shadow-xs transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
