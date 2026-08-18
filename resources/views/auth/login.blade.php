<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-8">
        <h2 class="text-3xl font-bold text-[#14213D] mb-2 flex items-center gap-2" style="font-family:'Poppins',sans-serif;">
            Selamat Datang Kembali
            <i class="fa-solid fa-hand text-amber-500 text-2xl"></i>
        </h2>
        <p class="text-[#8A93A6]">Silakan masuk ke akun BelanjaIn Anda.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-[#3E4658] mb-1.5">Alamat Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] focus:border-[#12A57F] focus:ring focus:ring-[#12A57F]/20 transition-all text-[#14213D] placeholder-[#A0A8B8]" 
                placeholder="nama@email.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
        </div>

        <div>
            <div class="flex justify-between items-center mb-1.5">
                <label for="password" class="block text-sm font-medium text-[#3E4658]">Kata Sandi</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-[#12A57F] font-medium hover:underline">
                        Lupa sandi?
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password" 
                class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] focus:border-[#12A57F] focus:ring focus:ring-[#12A57F]/20 transition-all text-[#14213D] placeholder-[#A0A8B8]" 
                placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
        </div>

        <div class="flex items-center">
            <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-[#E7E3D8] text-[#12A57F] focus:ring-[#12A57F]">
            <label for="remember_me" class="ml-2 text-sm text-[#8A93A6]">Ingat saya</label>
        </div>

        <button type="submit" class="w-full bg-[#12A57F] hover:bg-[#0f8b6a] text-white font-semibold py-3.5 px-4 rounded-xl shadow-lg shadow-[#12A57F]/30 transition-all active:scale-[0.98]">
            Masuk Sekarang
        </button>
    </form>

    <div class="mt-8 text-center">
        <p class="text-sm text-[#8A93A6]">Belum punya akun? 
            <a href="{{ route('register') }}" class="text-[#F2A93B] font-semibold hover:underline">Daftar di sini</a>
        </p>
    </div>
</x-guest-layout>
