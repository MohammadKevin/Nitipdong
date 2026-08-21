<x-guest-layout>
    <div x-data x-on:submit="$dispatch('auth-submitting')" class="relative">
    {{-- Brand Logo (Desktop only) --}}
    <div class="hidden lg:flex items-center justify-center gap-2.5 -mt-6 mb-12">
        <img src="{{ asset('img/belanjain-logo.svg') }}" alt="BelanjaIn" class="w-10 h-10 object-contain">
        <span class="font-extrabold text-2xl tracking-tight text-slate-900">
            Belanja<span style="color:#0891b2;">In</span>
        </span>
    </div>
    <h1 class="text-2xl font-bold text-slate-900 leading-snug mb-7 text-center lg:text-left">
        Create your account<br>to get started
    </h1>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        {{-- Name --}}
        <div>
            <label for="name" class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                Full Name
            </label>
            <input id="name"
                   type="text"
                   name="name"
                   value="{{ old('name') }}"
                   required
                   autofocus
                   autocomplete="name"
                   class="w-full px-4 py-3 text-sm rounded-xl border border-slate-200 bg-slate-50
                          focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                          transition-all placeholder-slate-400"
                   placeholder="Budi Santoso">
            <x-input-error :messages="$errors->get('name')" class="mt-1.5 text-xs text-rose-500"/>
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                Email Address
            </label>
            <input id="email"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   required
                   autocomplete="username"
                   class="w-full px-4 py-3 text-sm rounded-xl border border-slate-200 bg-slate-50
                          focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                          transition-all placeholder-slate-400"
                   placeholder="yourmail@mail.com">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-500"/>
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                Password
            </label>
            <div class="relative">
                <input id="password"
                       type="password"
                       name="password"
                       required
                       autocomplete="new-password"
                       class="w-full px-4 py-3 text-sm rounded-xl border border-slate-200 bg-slate-50
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
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
                Confirm Password
            </label>
            <div class="relative">
                <input id="password_confirmation"
                       type="password"
                       name="password_confirmation"
                       required
                       autocomplete="new-password"
                       class="w-full px-4 py-3 text-sm rounded-xl border border-slate-200 bg-slate-50
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
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
                class="w-full h-12 rounded-xl font-bold text-sm text-white tracking-widest uppercase
                       transition-all hover:opacity-90 active:scale-[0.98] shadow-lg mt-2"
                style="background: linear-gradient(90deg, #1a3fac 0%, #2563eb 100%);">
            REGISTER
        </button>
    </form>

    {{-- Login link --}}
    <div class="mt-6 text-center">
        <p class="text-sm text-slate-600">
            Already have an account?
            <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline ml-1">Log in here</a>
        </p>
    </div>
    </div>
</x-guest-layout>
