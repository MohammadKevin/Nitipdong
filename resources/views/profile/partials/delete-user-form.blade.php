<section class="space-y-4">
    <header class="border-b border-rose-100 pb-4">
        <h2 class="text-sm font-bold text-rose-700 uppercase tracking-wider flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
            Zona Berbahaya: Hapus Akun
        </h2>
        <p class="mt-1 text-xs text-slate-500">
            Setelah akun Anda dihapus, semua data profil, riwayat transaksi, dan akses toko akan dihapus permanen.
        </p>
    </header>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-4 py-2.5 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 font-semibold text-xs transition-colors border border-rose-200 shadow-2xs flex items-center gap-2"
    >
        <i class="fa-solid fa-trash-can text-xs"></i>
        Hapus Akun Saya
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-200 shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-base"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">
                        Konfirmasi Penghapusan Akun
                    </h2>
                    <p class="text-xs text-slate-500">
                        Tindakan ini permanen dan tidak dapat dibatalkan.
                    </p>
                </div>
            </div>

            <p class="mt-3 text-xs text-slate-600">
                Silakan masukkan kata sandi akun Anda untuk mengonfirmasi permintaan penghapusan akun:
            </p>

            <div class="mt-3">
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="input text-xs rounded-xl w-full h-10"
                    placeholder="Masukkan kata sandi akun Anda..."
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1.5 text-xs text-rose-500" />
            </div>

            <div class="mt-5 flex justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" x-on:click="$dispatch('close')" class="btn-secondary text-xs h-9.5 px-4 rounded-xl border border-slate-200 font-semibold text-slate-700">
                    Batal
                </button>

                <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs shadow-xs transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-trash text-xs"></i>
                    Konfirmasi Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</section>
