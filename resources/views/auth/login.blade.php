<x-guest-layout>
    <div x-data x-on:submit="$dispatch('auth-submitting')" class="relative">
        {{-- Brand Logo (Desktop only) --}}
        <div class="hidden lg:flex items-center justify-center gap-2.5 -mt-6 mb-12">
            <img src="{{ asset('img/belanjain-logo.svg') }}" alt="BelanjaIn" class="w-10 h-10 object-contain">
            <span class="font-extrabold text-2xl tracking-tight text-slate-900">
                Belanja<span style="color:#0891b2;">In</span>
            </span>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
                Selamat Datang Kembali
            </h1>
            <p class="text-xs text-slate-500 mt-1">Masuk ke akun SakserShop Anda untuk melanjutkan transaksi</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                    class="input text-xs rounded-md" 
                    placeholder="nama@email.com">
                <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-500" />
            </div>

            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Kata Sandi</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs text-cyan-700 font-semibold hover:underline">
                            Lupa sandi?
                        </a>
                    @endif
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password" 
                    class="input text-xs rounded-md" 
                    placeholder="••••••••">
                <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-500" />
            </div>

            <div class="flex items-center">
                <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                <label for="remember_me" class="ml-2 text-xs text-slate-600">Ingat sesi login saya</label>
            </div>

            <button type="submit" class="w-full btn-primary h-10 text-xs bg-cyan-700 hover:bg-cyan-800 cursor-pointer">
                Masuk ke Akun
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500">Belum memiliki akun? 
                <a href="{{ route('register') }}" class="text-cyan-700 font-bold hover:underline">Daftar sekarang</a>
            </p>
        </div>
    </div>
</x-guest-layout>
