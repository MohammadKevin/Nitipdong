<x-guest-layout>
    <div x-data x-on:submit="$dispatch('auth-submitting')" class="relative">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
                Buat Akun NitipDong
            </h1>
            <p class="text-xs text-slate-500 mt-1">Daftar sekarang untuk mulai berbelanja dan menikmati voucher eksklusif</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            {{-- Name --}}
            <div>
                <label for="name" class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                    Nama Lengkap
                </label>
                <input id="name"
                       type="text"
                       name="name"
                       value="{{ old('name') }}"
                       required
                       autofocus
                       autocomplete="name"
                       class="w-full px-4 py-3 text-sm rounded-xl border border-slate-200 bg-slate-50
                              focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600
                              transition-all placeholder-slate-400"
                       placeholder="Budi Santoso">
                <x-input-error :messages="$errors->get('name')" class="mt-1.5 text-xs text-rose-500"/>
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                    Alamat Email
                </label>
                <input id="email"
                       type="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       autocomplete="username"
                       class="w-full px-4 py-3 text-sm rounded-xl border border-slate-200 bg-slate-50
                              focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600
                              transition-all placeholder-slate-400"
                       placeholder="yourmail@mail.com">
                <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-500"/>
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                    Kata Sandi
                </label>
                <div class="relative">
                    <input id="password"
                           type="password"
                           name="password"
                           required
                           autocomplete="new-password"
                           class="w-full px-4 py-3 text-sm rounded-xl border border-slate-200 bg-slate-50
                                  focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600
                                  transition-all placeholder-slate-400 pr-12"
                           placeholder="Minimal 8 karakter">
                    <button type="button"
                            onclick="const i=document.getElementById('password'); i.type=i.type==='password'?'text':'password';"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <i class="fa-regular fa-eye-slash text-sm"></i>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-500"/>
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="password_confirmation" class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                    Konfirmasi Kata Sandi
                </label>
                <div class="relative">
                    <input id="password_confirmation"
                           type="password"
                           name="password_confirmation"
                           required
                           autocomplete="new-password"
                           class="w-full px-4 py-3 text-sm rounded-xl border border-slate-200 bg-slate-50
                                  focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600
                                  transition-all placeholder-slate-400 pr-12"
                           placeholder="Ulangi kata sandi">
                    <button type="button"
                            onclick="const i=document.getElementById('password_confirmation'); i.type=i.type==='password'?'text':'password';"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <i class="fa-regular fa-eye-slash text-sm"></i>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs text-rose-500"/>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="w-full h-11 rounded-xl font-bold text-xs text-white tracking-wider uppercase
                           transition-all hover:bg-cyan-800 bg-cyan-700 active:scale-[0.98] shadow-md mt-2 cursor-pointer">
                Daftar Akun
            </button>
        </form>

        {{-- Login link --}}
        <div class="mt-6 text-center">
            <p class="text-xs text-slate-500">
                Sudah memiliki akun?
                <a href="{{ route('login') }}" class="text-cyan-700 font-bold hover:underline ml-1">Masuk sekarang</a>
            </p>
        </div>
    </div>
</x-guest-layout>
