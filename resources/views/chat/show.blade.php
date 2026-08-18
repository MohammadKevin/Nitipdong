<x-dynamic-component :component="auth()->user()->role === 'super_admin' ? 'super-admin-layout' : (auth()->user()->role === 'admin' ? 'admin-layout' : 'app-layout')">
    @if(in_array(auth()->user()->role, ['super_admin', 'admin']))
        <x-slot name="title">
            Obrolan dengan {{ $partner->name }} - {{ config('app.name', 'BelanjaIn') }}
        </x-slot>
    @else
        <x-slot name="header">
            <h2 class="font-bold text-lg text-slate-900 leading-tight">
                {{ __('Pesan') }}
            </h2>
        </x-slot>
    @endif

    <div class="py-6 sm:py-8 h-[calc(100vh-100px)] flex">
        <div class="w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col h-full">
            <div class="bg-white rounded-xl shadow-card border border-slate-200/80 flex flex-col flex-1 overflow-hidden">
                
                <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-white shrink-0">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('chat.index') }}" class="w-8 h-8 rounded-md bg-slate-50 flex items-center justify-center text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition-colors border border-slate-200">
                            <i class="fa-solid fa-arrow-left text-xs"></i>
                        </a>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($partner->name) }}&background=0891b2&color=fff&size=50" class="w-9 h-9 rounded-full object-cover shrink-0 border border-slate-200" alt="Avatar">
                        <div>
                            <h2 class="font-bold text-xs text-slate-900 leading-tight">{{ $partner->name }}</h2>
                            <p class="text-[10px] text-cyan-700 mt-0.5 font-semibold capitalize">{{ $partner->role }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex-1 p-5 overflow-y-auto space-y-3 bg-slate-50/50" id="chat-messages">
                    @foreach ($messages as $msg)
                        @php $isMe = $msg->sender_id === auth()->id(); @endphp
                        <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                            <div class="max-w-[75%] md:max-w-[60%] px-3.5 py-2 text-xs {{ $isMe ? 'bg-cyan-700 text-white rounded-xl rounded-br-xs shadow-xs' : 'bg-white text-slate-800 border border-slate-200 rounded-xl rounded-bl-xs shadow-xs' }}">
                                {{ $msg->message }}
                            </div>
                            <span class="text-[9px] text-slate-400 mt-1 px-1 font-mono">{{ $msg->created_at->format('H:i') }}</span>
                        </div>
                    @endforeach
                </div>

                <form action="{{ route('chat.send', $conversation) }}" method="POST" class="p-3.5 bg-white border-t border-slate-100 shrink-0">
                    @csrf
                    <div class="flex items-center gap-2 relative">
                        <input type="text" name="message" id="message-input" required placeholder="Ketik pesan Anda..." autocomplete="off" class="input text-xs rounded-full pl-4 pr-12 h-10">
                        <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 w-7.5 h-7.5 rounded-full bg-cyan-700 flex items-center justify-center text-white hover:bg-cyan-800 transition-colors shadow-xs">
                            <i class="fa-solid fa-paper-plane text-[10px]"></i>
                        </button>
                    </div>
                </form>
                
            </div>
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