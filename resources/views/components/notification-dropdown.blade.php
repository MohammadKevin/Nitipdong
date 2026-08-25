@auth
@php
    $unreadCount = Auth::user()->unreadNotificationsCount();
    $notifications = Auth::user()->appNotifications()->take(8)->get();
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    
    <button @click="open = !open" type="button"
            class="relative p-2 text-slate-400 hover:text-cyan-700 hover:bg-slate-100 rounded-xl transition-all cursor-pointer" title="Notifikasi">
        <i class="fa-regular fa-bell text-base"></i>
        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-500 text-white text-[10px] font-bold flex items-center justify-center border-2 border-white shadow-xs">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 overflow-hidden text-xs">
        
        <div class="p-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-1.5 font-bold text-slate-900">
                <i class="fa-solid fa-bell text-cyan-600"></i>
                <span>Notifikasi</span>
                @if($unreadCount > 0)
                    <span class="px-1.5 py-0.2 rounded-full bg-cyan-100 text-cyan-800 text-[10px] font-bold">
                        {{ $unreadCount }} Baru
                    </span>
                @endif
            </div>

            @if($unreadCount > 0)
                <form action="{{ route('notifications.read_all') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-[11px] text-cyan-700 font-semibold hover:underline cursor-pointer">
                        Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
            @forelse($notifications as $notif)
            @php
                $typeIcons = [
                    'order'     => ['icon' => 'fa-receipt', 'bg' => 'bg-cyan-50 text-cyan-700 border-cyan-200'],
                    'wallet'    => ['icon' => 'fa-wallet', 'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                    'complaint' => ['icon' => 'fa-triangle-exclamation', 'bg' => 'bg-rose-50 text-rose-600 border-rose-200'],
                    'info'      => ['icon' => 'fa-circle-info', 'bg' => 'bg-blue-50 text-blue-700 border-blue-200'],
                ];
                $ico = $typeIcons[$notif->type] ?? $typeIcons['info'];
            @endphp
            <form action="{{ route('notifications.read', $notif) }}" method="POST" class="block">
                @csrf
                <button type="submit" class="w-full text-left p-3.5 hover:bg-slate-50 transition-colors flex items-start gap-3 {{ !$notif->is_read ? 'bg-cyan-50/30' : '' }}">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs shrink-0 border {{ $ico['bg'] }}">
                        <i class="fa-solid {{ $ico['icon'] }}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1">
                            <span class="font-bold text-slate-900 truncate block">{{ $notif->title }}</span>
                            @if(!$notif->is_read)
                                <span class="w-2 h-2 rounded-full bg-cyan-600 shrink-0"></span>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-600 mt-0.5 line-clamp-2 leading-relaxed">{{ $notif->message }}</p>
                        <span class="text-[10px] text-slate-400 mt-1 block">{{ $notif->created_at->diffForHumans() }}</span>
                    </div>
                </button>
            </form>
            @empty
            <div class="py-8 text-center text-slate-400">
                <i class="fa-regular fa-bell-slash text-2xl mb-1 text-slate-300"></i>
                <p class="text-[11px] font-semibold text-slate-500">Belum Ada Notifikasi</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Pemberitahuan aktivitas pesanan akan muncul di sini.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endauth
