<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
            Buat Akun BelanjaIn
        </h1>
        <p class="text-xs text-slate-500 mt-1">Daftar sekarang untuk mulai berbelanja dan menikmati voucher eksklusif</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
        @csrf

        <div>
            <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" 
                class="input text-xs rounded-md" 
                placeholder="Budi Santoso">
            <x-input-error :messages="$errors->get('name')" class="mt-1.5 text-xs text-rose-500" />
        </div>

        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" 
                class="input text-xs rounded-md" 
                placeholder="nama@email.com">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-500" />
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Kata Sandi</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" 
                class="input text-xs rounded-md" 
                placeholder="Minimal 8 karakter">
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-500" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Konfirmasi Kata Sandi</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" 
                class="input text-xs rounded-md" 
                placeholder="Ulangi kata sandi">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs text-rose-500" />
        </div>

        <button type="submit" class="w-full btn-primary h-10 text-xs bg-cyan-700 hover:bg-cyan-800 mt-2">
            Daftar Akun Baru
        </button>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-100 text-center">
        <p class="text-xs text-slate-500">Sudah memiliki akun? 
            <a href="{{ route('login') }}" class="text-cyan-700 font-bold hover:underline">Masuk di sini</a>
        </p>
    </div>
</x-guest-layout>
