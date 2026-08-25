<x-guest-layout>
    
    <div class="hidden lg:flex items-center justify-center gap-2.5 mb-8">
        <img src="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}" alt="NitipDong" class="w-8 h-8 rounded-lg object-contain">
        <span class="font-extrabold text-xl tracking-tight text-slate-900">
            Nitip<span class="text-cyan-600">Dong</span>
        </span>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
            Lupa Kata Sandi?
        </h1>
        <p class="text-xs text-slate-500 mt-1">
            Masukkan alamat email Anda untuk menerima tautan pemulihan kata sandi.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email Terdaftar</label>
            <input id="email" class="input text-xs rounded-md" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-500" />
        </div>

        <button type="submit" class="w-full btn-primary h-10 text-xs bg-cyan-700 hover:bg-cyan-800">
            Kirim Tautan Reset Sandi
        </button>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-100 text-center">
        <a href="{{ route('login') }}" class="text-xs font-semibold text-cyan-700 hover:underline">
            &larr; Kembali ke Halaman Masuk
        </a>
    </div>
</x-guest-layout>
