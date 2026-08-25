<x-guest-layout>
    
    <div class="hidden lg:flex items-center justify-center gap-2.5 mb-8">
        <img src="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}" alt="NitipDong" class="w-8 h-8 rounded-lg object-contain">
        <span class="font-extrabold text-xl tracking-tight text-slate-900">
            Nitip<span class="text-cyan-600">Dong</span>
        </span>
    </div>

    <div class="mb-4 text-sm text-gray-600">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
