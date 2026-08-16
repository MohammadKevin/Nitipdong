<x-dynamic-component :component="auth()->user()->role === 'super_admin' ? 'super-admin-layout' : (auth()->user()->role === 'admin' ? 'admin-layout' : 'app-layout')">
    @if(in_array(auth()->user()->role, ['super_admin', 'admin']))
        <x-slot name="title">
            Pengaturan Akun
        </x-slot>
        <div class="mb-6">
            <h1 class="text-xl font-bold text-[#14213D]" style="font-family:'Poppins',sans-serif;">Pengaturan Akun</h1>
            <p class="text-xs text-[#8A93A6] mt-0.5">Kelola informasi profil dan kata sandi Anda.</p>
        </div>
    @else
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Profile') }}
            </h2>
        </x-slot>
    @endif

    <div class="{{ in_array(auth()->user()->role, ['super_admin', 'admin']) ? '' : 'py-12' }}">
        <div class="max-w-7xl mx-auto {{ in_array(auth()->user()->role, ['super_admin', 'admin']) ? '' : 'sm:px-6 lg:px-8' }} space-y-6">
            <div class="p-4 sm:p-8 bg-white border border-[#E7E3D8] shadow-sm sm:rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white border border-[#E7E3D8] shadow-sm sm:rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white border border-[#E7E3D8] shadow-sm sm:rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
