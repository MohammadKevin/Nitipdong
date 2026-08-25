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
        Obrolan dengan {{ $partner->name }} - {{ config('app.name', 'NitipDong') }}
    </x-slot>

    <div class="{{ $isSidebarLayout ? 'max-w-4xl mx-auto w-full' : 'page-container py-4 sm:py-6 max-w-4xl mx-auto min-h-[80vh]' }}">
        <div class="bg-white rounded-2xl shadow-card border border-slate-200/90 flex flex-col h-[calc(100vh-180px)] min-h-[560px] overflow-hidden">

            <div class="px-4 sm:px-6 py-3.5 border-b border-slate-100 flex items-center justify-between bg-white shrink-0 shadow-2xs">
                <div class="flex items-center gap-3">
                    <a href="{{ route('chat.index') }}" class="w-9 h-9 rounded-xl bg-slate-50 flex items-center justify-center text-slate-600 hover:bg-cyan-50 hover:text-cyan-800 transition-colors border border-slate-200 shadow-2xs" title="Kembali ke Kotak Masuk">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                    </a>
                    <div class="relative">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($partner->name) }}&background=0e7490&color=fff&size=80" class="w-10 h-10 rounded-2xl object-cover shrink-0 border border-slate-200 shadow-2xs" alt="Avatar">
                        <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-emerald-500 border-2 border-white"></span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="font-bold text-sm sm:text-base text-slate-900 leading-tight">{{ $partner->name }}</h2>
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
                        <p class="text-[11px] text-emerald-600 font-medium flex items-center gap-1 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                            Online & Siap merespon
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('chat.index') }}" class="text-xs font-semibold text-slate-500 hover:text-cyan-700 hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-all">
                        <i class="fa-solid fa-inbox text-xs"></i>
                        <span>Semua Obrolan</span>
                    </a>
                </div>
            </div>

            <div class="flex-1 p-4 sm:p-6 overflow-y-auto space-y-3 bg-slate-50/70" id="chat-messages">
                <div class="text-center my-2">
                    <span class="text-[10px] font-medium text-slate-400 bg-white/90 border border-slate-200/80 px-3.5 py-1 rounded-full shadow-2xs">
                        <i class="fa-solid fa-lock text-[9px] mr-1 text-slate-400"></i> Percakapan dilindungi enkripsi sistem NitipDong
                    </span>
                </div>

                @foreach ($messages as $msg)
                    @php $isMe = $msg->sender_id === auth()->id(); @endphp
                    <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                        <div class="max-w-[85%] sm:max-w-[70%] px-4 py-2.5 text-xs sm:text-[13px] leading-relaxed break-words shadow-2xs {{ $isMe ? 'bg-cyan-700 text-white rounded-2xl rounded-tr-xs' : 'bg-white text-slate-800 border border-slate-200/90 rounded-2xl rounded-tl-xs' }}">
                            {{ $msg->message }}
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1 px-1 font-mono flex items-center gap-1">
                            {{ $msg->created_at->format('H:i') }}
                            @if($isMe)
                                <i class="fa-solid fa-check-double text-[9px] {{ $msg->is_read ? 'text-sky-500 font-bold' : 'text-slate-300' }}"
                                   title="{{ $msg->is_read ? 'Sudah dibaca' : 'Terkirim (belum dibaca)' }}"></i>
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>

            <form action="{{ route('chat.send', $conversation) }}" method="POST" class="p-3 sm:p-4 bg-white border-t border-slate-100 shrink-0">
                @csrf
                <div class="flex items-center gap-2 relative">
                    <input type="text" name="message" id="message-input" required placeholder="Tulis pesan balasan ke {{ $partner->name }}..." autocomplete="off"
                        class="input text-xs sm:text-sm rounded-xl pl-4 pr-12 h-11 border border-slate-200 bg-slate-50/50 focus:bg-white transition-all w-full outline-none">
                    <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 w-8 h-8 rounded-lg bg-cyan-700 flex items-center justify-center text-white hover:bg-cyan-800 transition-all shadow-xs" title="Kirim Pesan">
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                    </button>
                </div>
            </form>
            
        </div>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const chatContainer = document.getElementById('chat-messages');
            if(chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
            
            const messageInput = document.getElementById('message-input');
            if(messageInput) {
                messageInput.focus();
            }
        });
    </script>
</x-dynamic-component>
