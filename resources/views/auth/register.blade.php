<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-[#14213D] mb-2 flex items-center gap-2" style="font-family:'Poppins',sans-serif;">
            Buat Akun Baru
            <i class="fa-solid fa-wand-magic-sparkles text-amber-500 text-2xl"></i>
        </h2>
        <p class="text-[#8A93A6]">Bergabunglah dan mulai perjalanan bisnis Anda.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-[#3E4658] mb-1.5">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" 
                class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] focus:border-[#12A57F] focus:ring focus:ring-[#12A57F]/20 transition-all text-[#14213D] placeholder-[#A0A8B8]" 
                placeholder="Budi Santoso">
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500" />
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-[#3E4658] mb-1.5">Alamat Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" 
                class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] focus:border-[#12A57F] focus:ring focus:ring-[#12A57F]/20 transition-all text-[#14213D] placeholder-[#A0A8B8]" 
                placeholder="nama@email.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-[#3E4658] mb-1.5">Kata Sandi</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" 
                class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] focus:border-[#12A57F] focus:ring focus:ring-[#12A57F]/20 transition-all text-[#14213D] placeholder-[#A0A8B8]" 
                placeholder="Minimal 8 karakter">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-[#3E4658] mb-1.5">Ulangi Kata Sandi</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" 
                class="w-full px-4 py-3 rounded-xl border border-[#E7E3D8] focus:border-[#12A57F] focus:ring focus:ring-[#12A57F]/20 transition-all text-[#14213D] placeholder-[#A0A8B8]" 
                placeholder="Masukkan ulang kata sandi">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500" />
        </div>

        <button type="submit" class="w-full bg-[#12A57F] hover:bg-[#0f8b6a] text-white font-semibold py-3.5 px-4 rounded-xl shadow-lg shadow-[#12A57F]/30 transition-all active:scale-[0.98] mt-2">
            Daftar Sekarang
        </button>
    </form>

    <div class="mt-8 text-center">
        <p class="text-sm text-[#8A93A6]">Sudah punya akun? 
            <a href="{{ route('login') }}" class="text-[#F2A93B] font-semibold hover:underline">Masuk di sini</a>
        </p>
    </div>
</x-guest-layout>
