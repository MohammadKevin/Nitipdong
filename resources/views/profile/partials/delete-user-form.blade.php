<section class="space-y-4">
    <header>
        <h2 class="text-sm font-bold text-rose-700 uppercase tracking-wider">
            Hapus Akun Permanen
        </h2>

        <p class="mt-1 text-xs text-slate-500">
            Setelah akun Anda dihapus, semua data profil, riwayat, dan akses toko akan dihapus secara permanen.
        </p>
    </header>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-4 py-2 rounded-md bg-rose-50 text-rose-700 hover:bg-rose-100 font-semibold text-xs transition-colors border border-rose-200"
    >Hapus Akun Saya</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-sm font-bold text-slate-900">
                Apakah Anda yakin ingin menghapus akun Anda?
            </h2>

            <p class="mt-1 text-xs text-slate-500">
                Masukkan kata sandi Anda untuk mengonfirmasi penghapusan akun permanen ini.
            </p>

            <div class="mt-4">
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="input text-xs rounded-md w-3/4"
                    placeholder="Kata Sandi Anda"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1.5 text-xs text-rose-500" />
            </div>

            <div class="mt-5 flex justify-end gap-2.5">
                <button type="button" x-on:click="$dispatch('close')" class="btn-secondary text-xs h-9 px-4 rounded-md">
                    Batal
                </button>

                <button type="submit" class="px-4 py-2 rounded-md bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs shadow-xs transition-colors">
                    Hapus Akun Permanen
                </button>
            </div>
        </form>
    </x-modal>
</section>
