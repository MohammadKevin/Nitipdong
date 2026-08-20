<x-app-layout>
    <div class="py-8 bg-slate-50 min-h-[calc(100vh-200px)]">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-cyan-700 transition-colors font-medium">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    Kembali ke Dashboard Saya
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-card border border-slate-200/80 overflow-hidden">
                <div class="p-6 bg-slate-950 text-white border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-cyan-950 border border-cyan-400/30 flex items-center justify-center text-cyan-400 text-lg">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-white tracking-tight">Formulir Pendaftaran Toko Resmi</h1>
                            <p class="text-xs text-slate-300 mt-0.5">Buka toko Anda sendiri dan mulai berjualan ke jutaan pelanggan di SakserShop.</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('store.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Toko / Brand <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            placeholder="Contoh: Toko Berkah Elektronik"
                            class="input text-xs rounded-md">
                        <p class="text-[10px] text-slate-400 mt-1">Nama toko akan digunakan sebagai identitas resmi dan URL etalase Anda.</p>
                        @error('name')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Deskripsi Toko & Profil Produk <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="description" name="description" rows="4" required
                            placeholder="Jelaskan kategori produk yang Anda jual, komitmen kualitas, dan keunggulan toko Anda..."
                            class="input text-xs rounded-md">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="address" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Alamat Operasional & Pengiriman Toko <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="address" name="address" rows="3" required
                            placeholder="Tuliskan alamat lengkap gudang / toko asal pengiriman paket..."
                            class="input text-xs rounded-md">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="p-3 bg-cyan-50 border border-cyan-200 rounded-md text-xs text-cyan-900 flex items-start gap-2.5">
                        <i class="fa-solid fa-circle-info text-cyan-600 text-sm mt-0.5"></i>
                        <p>Setelah formulir dikirim, tim operasional SakserShop akan meninjau pengajuan Anda dalam waktu 1x24 jam kerja.</p>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <a href="{{ route('customer.dashboard') }}" class="btn-secondary text-xs h-9 px-4 rounded-md">
                            Batal
                        </a>
                        <button type="submit" class="btn-primary text-xs h-9 px-5 rounded-md bg-cyan-700 hover:bg-cyan-800 flex items-center gap-1.5">
                            <i class="fa-solid fa-paper-plane text-[10px]"></i>
                            Kirim Pengajuan Buka Toko
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
