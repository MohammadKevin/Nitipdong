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
        Obrolan dengan {{ $partner->name }} - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="{{ $isSidebarLayout ? 'space-y-3' : 'page-container py-6 min-h-[75vh]' }}">
        <div class="bg-white rounded-2xl shadow-card border border-slate-200/80 flex flex-col h-[calc(100vh-210px)] min-h-[520px] overflow-hidden">
            
            {{-- Chat Header --}}
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-white shrink-0">
                <div class="flex items-center gap-3">
                    <a href="{{ route('chat.index') }}" class="w-9 h-9 rounded-xl bg-slate-50 flex items-center justify-center text-slate-500 hover:bg-cyan-50 hover:text-cyan-800 transition-colors border border-slate-200 shadow-2xs" title="Kembali ke Daftar Obrolan">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                    </a>
                    <div class="relative">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($partner->name) }}&background=0e7490&color=fff&size=80" class="w-10 h-10 rounded-2xl object-cover shrink-0 border border-slate-200 shadow-2xs" alt="Avatar">
                        <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-emerald-500 border-2 border-white"></span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="font-bold text-sm text-slate-900 leading-tight">{{ $partner->name }}</h2>
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
                        <p class="text-[11px] text-emerald-600 mt-0.5 font-medium flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                            Online & Siap merespon
                        </p>
                    </div>
                </div>
            </div>

            {{-- Messages Body --}}
            <div class="flex-1 p-5 overflow-y-auto space-y-3 bg-slate-50/50" id="chat-messages">
                <div class="text-center my-2">
                    <span class="text-[10px] font-medium text-slate-400 bg-white/80 border border-slate-200/60 px-3 py-1 rounded-full shadow-2xs">
                        Percakapan dimulai secara privat dan aman
                    </span>
                </div>

                @foreach ($messages as $msg)
                    @php $isMe = $msg->sender_id === auth()->id(); @endphp
                    <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                        <div class="max-w-[80%] md:max-w-[65%] px-4 py-2.5 text-xs leading-relaxed {{ $isMe ? 'bg-cyan-700 text-white rounded-2xl rounded-br-xs shadow-xs' : 'bg-white text-slate-800 border border-slate-200/80 rounded-2xl rounded-bl-xs shadow-2xs' }}">
                            {{ $msg->message }}
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1 px-1 font-mono flex items-center gap-1">
                            {{ $msg->created_at->format('H:i') }}
                            @if($isMe)
                                <i class="fa-solid fa-check-double text-[9px] text-cyan-600"></i>
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>

            {{-- Message Input Form --}}
            <form action="{{ route('chat.send', $conversation) }}" method="POST" class="p-3.5 bg-white border-t border-slate-100 shrink-0">
                @csrf
                <div class="flex items-center gap-2 relative">
                    <input type="text" name="message" id="message-input" required placeholder="Tulis pesan balasan..." autocomplete="off"
                        class="input text-xs rounded-xl pl-4 pr-12 h-11 border border-slate-200 bg-slate-50/50 focus:bg-white transition-all w-full">
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