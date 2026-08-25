<x-admin-layout>
    <x-slot name="title">
        Edit Flash Sale: {{ $flashSale->title ?? $flashSale->name }} - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Edit Flash Sale
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
                Edit Sesi Flash Sale
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Perbarui nama, waktu mulai, atau waktu selesai sesi Flash Sale.</p>
        </div>
    </div>

    <div class="max-w-2xl bg-white rounded-xl shadow-xs border border-slate-200/90 overflow-hidden">
        <form action="{{ auth()->user()->role === 'super_admin' ? route('super_admin.flash_sales.update', $flashSale) : route('admin.flash_sales.update', $flashSale) }}" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Event Flash Sale <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="title" name="title" required value="{{ old('title', $flashSale->title ?? $flashSale->name) }}"
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
                    <input type="datetime-local" id="start_time" name="start_time" required value="{{ old('start_time', $flashSale->start_time->format('Y-m-d\TH:i')) }}"
                        class="w-full h-9 px-3 rounded-lg border border-slate-200 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    @error('start_time')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_time" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Waktu Selesai <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" id="end_time" name="end_time" required value="{{ old('end_time', $flashSale->end_time->format('Y-m-d\TH:i')) }}"
                        class="w-full h-9 px-3 rounded-lg border border-slate-200 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    @error('end_time')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="flex items-center gap-2 cursor-pointer mt-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $flashSale->is_active) ? 'checked' : '' }}
                        class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-xs font-medium text-slate-700">Aktifkan sesi event ini di platform</span>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.flash_sales.index') : route('admin.flash_sales.index') }}" class="px-4 py-2 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold text-xs transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition-colors shadow-xs cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
