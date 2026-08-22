<x-guest-layout>
    {{-- Brand Logo (Desktop only) --}}
    <div class="hidden lg:flex items-center justify-center gap-2.5 mb-8">
        <img src="{{ asset('img/belanjain-logo.svg') }}" alt="BelanjaIn" class="w-8 h-8 object-contain">
        <span class="font-extrabold text-xl tracking-tight text-slate-900">
            Belanja<span style="color:#0891b2;">In</span>
        </span>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
            Atur Ulang Kata Sandi
        </h1>
        <p class="text-xs text-slate-500 mt-1">
            Buat kata sandi baru yang kuat untuk akun NitipDong Anda.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-3.5">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
            <input id="email" class="input text-xs rounded-md" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-500" />
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Kata Sandi Baru</label>
            <input id="password" class="input text-xs rounded-md" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-500" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Konfirmasi Kata Sandi Baru</label>
            <input id="password_confirmation" class="input text-xs rounded-md" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi baru" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs text-rose-500" />
        </div>

        <button type="submit" class="w-full btn-primary h-10 text-xs bg-cyan-700 hover:bg-cyan-800 mt-2">
            Simpan Kata Sandi Baru
        </button>
    </form>
</x-guest-layout>
