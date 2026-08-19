<section>
    <header class="border-b border-slate-100 pb-4">
        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fa-solid fa-shield-halved text-cyan-700"></i>
            Perbarui Kata Sandi
        </h2>
        <p class="mt-1 text-xs text-slate-500">
            Pastikan akun Anda menggunakan kombinasi kata sandi yang kuat dan aman.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-5 space-y-4">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Kata Sandi Saat Ini</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-lock text-xs"></i>
                </div>
                <input id="update_password_current_password" name="current_password" type="password" class="input text-xs rounded-xl !pl-11 h-10 w-full" autocomplete="current-password" placeholder="Masukkan kata sandi lama" />
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1.5 text-xs text-rose-500" />
        </div>

        <div>
            <label for="update_password_password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Kata Sandi Baru</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-key text-xs"></i>
                </div>
                <input id="update_password_password" name="password" type="password" class="input text-xs rounded-xl !pl-11 h-10 w-full" autocomplete="new-password" placeholder="Minimal 8 karakter" />
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1.5 text-xs text-rose-500" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Konfirmasi Kata Sandi Baru</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-check-double text-xs"></i>
                </div>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="input text-xs rounded-xl !pl-11 h-10 w-full" autocomplete="new-password" placeholder="Ulangi kata sandi baru" />
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1.5 text-xs text-rose-500" />
        </div>

        <div class="flex items-center gap-3 pt-3 border-t border-slate-100">
            <button type="submit" class="btn-primary text-xs h-10 px-5 rounded-xl bg-cyan-700 hover:bg-cyan-800 font-semibold shadow-xs flex items-center gap-2">
                <i class="fa-solid fa-lock text-xs"></i>
                Simpan Kata Sandi
            </button>

            @if (session('status') === 'password-updated')
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-xs font-semibold text-emerald-600 flex items-center gap-1.5"
                >
                    <i class="fa-solid fa-circle-check"></i>
                    Kata sandi diperbarui!
                </div>
            @endif
        </div>
    </form>
</section>
