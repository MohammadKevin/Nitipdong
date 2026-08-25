@php
    $layout = match(auth()->user()->role) {
        'super_admin' => 'super-admin-layout',
        'admin'       => 'admin-layout',
        'seller'      => 'seller-layout',
        default       => 'app-layout',
    };
    $isSidebarLayout = in_array(auth()->user()->role, ['super_admin', 'admin', 'seller']);
    $user = auth()->user();
@endphp

<x-dynamic-component :component="$layout">
    <x-slot name="title">
        Pengaturan Profil & Akun - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Pengaturan Profil & Akun
    </x-slot>

    <div class="{{ $isSidebarLayout ? 'space-y-6' : 'page-container py-6 min-h-[75vh] space-y-6' }}">
        
        <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-200/80 relative overflow-hidden">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5">
                <div class="relative shrink-0">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-2xl object-cover border-2 border-cyan-100 shadow-sm">
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center text-[10px] text-white" title="Akun Aktif">
                        <i class="fa-solid fa-check"></i>
                    </span>
                </div>

                <div class="flex-1 text-center sm:text-left min-w-0">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 flex-wrap justify-center sm:justify-start">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">{{ $user->name }}</h1>
                        @php
                            $roleBadges = [
                                'super_admin' => 'bg-purple-50 text-purple-700 border-purple-200',
                                'admin'       => 'bg-blue-50 text-blue-700 border-blue-200',
                                'seller'      => 'bg-amber-50 text-amber-700 border-amber-200',
                                'customer'    => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            ];
                            $roleLabels = [
                                'super_admin' => 'Super Administrator',
                                'admin'       => 'Admin Official',
                                'seller'      => 'Seller Toko Resmi',
                                'customer'    => 'Member Pembeli',
                            ];
                        @endphp
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $roleBadges[$user->role] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $user->role === 'seller' ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                            {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                        </span>
                    </div>

                    <div class="flex items-center gap-4 text-xs text-slate-500 mt-2 flex-wrap justify-center sm:justify-start">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-regular fa-envelope text-slate-400"></i>
                            {{ $user->email }}
                        </span>
                        @if($user->phone)
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-phone text-slate-400"></i>
                                {{ $user->phone }}
                            </span>
                        @endif
                        <span class="flex items-center gap-1.5">
                            <i class="fa-regular fa-calendar-check text-slate-400"></i>
                            Bergabung {{ $user->created_at->translatedFormat('d F Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <div class="lg:col-span-7 space-y-6">
                <div class="p-6 bg-white border border-slate-200/80 shadow-card rounded-2xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="lg:col-span-5 space-y-6">
                <div class="p-6 bg-white border border-slate-200/80 shadow-card rounded-2xl">
                    @include('profile.partials.update-password-form')
                </div>

                <div class="p-6 bg-white border border-rose-100 shadow-card rounded-2xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
