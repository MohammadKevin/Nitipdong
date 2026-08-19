@php
    $layout = match(auth()->user()->role) {
        'super_admin' => 'super-admin-layout',
        'admin'       => 'admin-layout',
        'seller'      => 'seller-layout',
        default       => 'app-layout',
    };
    $isSidebarLayout = in_array(auth()->user()->role, ['super_admin', 'admin', 'seller']);
@endphp

<x-dynamic-component :component="$layout">
    <x-slot name="title">
        Pesan & Percakapan - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="{{ $isSidebarLayout ? 'space-y-4' : 'page-container py-6 min-h-[75vh]' }}">
        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-2">
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-comments text-cyan-700"></i>
                    Pesan & Percakapan
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    @if(auth()->user()->role === 'seller')
                        {{ ($activeTab ?? '') === 'admin' ? 'Obrolan langsung dan konsultasi bantuan dengan tim Admin Resmi.' : 'Kelola obrolan langsung Anda dengan pembeli dan calon pelanggan toko.' }}
                    @else
                        Kelola obrolan langsung Anda dengan pembeli, pelanggan toko, atau pengelola marketplace.
                    @endif
                </p>
            </div>

            @if(in_array(auth()->user()->role, ['super_admin', 'seller']) && isset($admins) && $admins->isNotEmpty())
                <form id="startAdminChatForm" method="POST" class="flex gap-2 w-full sm:w-auto" onsubmit="
                    const select = document.getElementById('admin_select');
                    if(!select.value) { alert('Pilih admin terlebih dahulu!'); return false; }
                    this.action = '/chat/start/' + select.value;
                    return true;
                ">
                    @csrf
                    <select id="admin_select" class="input text-xs rounded-xl bg-white flex-1 sm:w-52 h-9.5 border border-slate-200">
                        <option value="">-- Hubungi Admin Official --</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->getRouteKey() }}">{{ $admin->name }} ({{ ucfirst($admin->role) }})</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary text-xs h-9.5 px-4 rounded-xl bg-cyan-700 hover:bg-cyan-800 font-semibold shadow-xs whitespace-nowrap flex items-center gap-1.5">
                        <i class="fa-solid fa-paper-plane text-[10px]"></i>
                        Mulai Chat
                    </button>
                </form>
            @endif
        </div>

        @if(auth()->user()->role === 'seller')
            {{-- Seller Chat Navigation Tabs --}}
            <div class="flex items-center gap-2 border-b border-slate-200/80 pb-1">
                <a href="{{ route('seller.chat.cus') }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ ($activeTab ?? 'cus') === 'cus' ? 'bg-cyan-700 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    <i class="fa-regular fa-comment-dots text-xs"></i>
                    Chat Pembeli (Customer)
                </a>
                <a href="{{ route('seller.chat.admin') }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ ($activeTab ?? '') === 'admin' ? 'bg-cyan-700 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    <i class="fa-solid fa-headset text-xs"></i>
                    Chat Bantuan Admin
                </a>
            </div>
        @endif

        {{-- Conversations Card Container --}}
        <div class="bg-white rounded-2xl shadow-card border border-slate-200/80 overflow-hidden flex flex-col min-h-[480px] max-h-[calc(100vh-250px)]">
            <div class="p-4 border-b border-slate-100 bg-slate-50/60 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                    {{ (isset($activeTab) && $activeTab === 'admin') ? 'Daftar Chat dengan Admin Official' : 'Kotak Masuk Chat Pembeli' }}
                </span>
                <span class="text-xs text-slate-500 font-medium">Total <strong class="text-slate-900">{{ count($conversations) }}</strong> Obrolan</span>
            </div>

            <div class="divide-y divide-slate-100 overflow-y-auto flex-1">
                @forelse ($conversations as $conv)
                    @php
                        $partner = $conv->user_one_id === auth()->id() ? $conv->userTwo : $conv->userOne;
                        $lastMsg = $conv->messages->first();
                        $unreadCount = $conv->messages->where('sender_id', '!=', auth()->id())->where('is_read', false)->count();
                    @endphp
                    <a href="{{ route('chat.show', $conv) }}" class="p-4 sm:p-5 flex items-center justify-between hover:bg-cyan-50/30 transition-all block group">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="relative shrink-0">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($partner->name) }}&background=0e7490&color=fff&size=80" class="w-11 h-11 rounded-2xl object-cover border border-slate-200 shadow-2xs group-hover:border-cyan-300 transition-colors" alt="User">
                                @if($unreadCount > 0)
                                    <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 text-white text-[9px] font-bold flex items-center justify-center border-2 border-white">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="font-bold text-slate-900 text-xs sm:text-sm truncate group-hover:text-cyan-800 transition-colors">
                                        {{ $partner->name }}
                                    </h4>
                                    @php
                                        $roleBadges = [
                                            'super_admin' => 'bg-purple-50 text-purple-700 border-purple-200',
                                            'admin'       => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'seller'      => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'customer'    => 'bg-slate-100 text-slate-600 border-slate-200',
                                        ];
                                        $roleLabels = [
                                            'super_admin' => 'Super Admin',
                                            'admin'       => 'Admin Official',
                                            'seller'      => 'Seller Toko',
                                            'customer'    => 'Pembeli',
                                        ];
                                    @endphp
                                    <span class="text-[9px] font-bold px-2 py-0.5 rounded-full border {{ $roleBadges[$partner->role] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                        {{ $roleLabels[$partner->role] ?? ucfirst($partner->role) }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 truncate mt-1 {{ $unreadCount > 0 ? 'font-bold text-slate-800' : '' }}">
                                    {{ $lastMsg ? $lastMsg->message : 'Mulai percakapan baru...' }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right shrink-0 ml-4 flex flex-col items-end gap-1">
                            <span class="text-[10px] text-slate-400 whitespace-nowrap font-mono">{{ $conv->updated_at->diffForHumans() }}</span>
                            <span class="text-xs text-slate-300 group-hover:text-cyan-600 group-hover:translate-x-1 transition-all">
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="p-16 flex flex-col items-center justify-center text-center my-auto">
                        <div class="w-16 h-16 bg-cyan-50 text-cyan-700 rounded-2xl flex items-center justify-center text-2xl mb-3.5 border border-cyan-200 shadow-2xs">
                            <i class="fa-regular fa-comment-dots"></i>
                        </div>
                        <h3 class="text-slate-900 font-bold text-sm sm:text-base">Belum Ada Obrolan Aktif</h3>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm leading-relaxed">Riwayat pesan Anda dengan pembeli, admin, atau pengguna lain akan tersusun rapi di sini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-dynamic-component>