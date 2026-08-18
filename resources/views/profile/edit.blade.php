<x-dynamic-component :component="auth()->user()->role === 'super_admin' ? 'super-admin-layout' : (auth()->user()->role === 'admin' ? 'admin-layout' : 'app-layout')">
    @if(in_array(auth()->user()->role, ['super_admin', 'admin']))
        <x-slot name="title">
            Pengaturan Akun - {{ config('app.name', 'BelanjaIn') }}
        </x-slot>
        <div class="mb-4">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Pengaturan Profil & Akun</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola informasi data pribadi dan keamanan akun Anda.</p>
        </div>
    @else
        <x-slot name="header">
            <h2 class="font-bold text-lg text-slate-900 leading-tight">
                {{ __('Pengaturan Akun') }}
            </h2>
        </x-slot>
    @endif

    <div class="{{ in_array(auth()->user()->role, ['super_admin', 'admin']) ? '' : 'py-8' }}">
        <div class="max-w-4xl mx-auto {{ in_array(auth()->user()->role, ['super_admin', 'admin']) ? '' : 'sm:px-6 lg:px-8' }} space-y-6">
            <div class="p-6 bg-white border border-slate-200/80 shadow-card rounded-xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-6 bg-white border border-slate-200/80 shadow-card rounded-xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-6 bg-white border border-slate-200/80 shadow-card rounded-xl">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
