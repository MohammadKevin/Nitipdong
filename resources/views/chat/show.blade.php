<x-dynamic-component :component="auth()->user()->role === 'super_admin' ? 'super-admin-layout' : (auth()->user()->role === 'admin' ? 'admin-layout' : 'app-layout')">
    @if(in_array(auth()->user()->role, ['super_admin', 'admin']))
        <x-slot name="title">
            Obrolan dengan {{ $partner->name }} - {{ config('app.name', 'BelanjaIn') }}
        </x-slot>
    @else
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-[#14213D] leading-tight">
                {{ __('Pesan') }}
            </h2>
        </x-slot>
    @endif

    <div class="py-6 sm:py-8 h-[calc(100vh-100px)] flex">
        <div class="w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col h-full">
            <div class="bg-white rounded-2xl shadow-sm border border-[#EFEBDF] flex flex-col flex-1 overflow-hidden">
                
                <div class="px-6 py-4 border-b border-[#F0EEE6] flex items-center justify-between bg-white shrink-0">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('chat.index') }}" class="w-8 h-8 rounded-full bg-[#FAF9F5] flex items-center justify-center text-[#8A93A6] hover:bg-[#F0EEE6] hover:text-[#14213D] transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </a>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($partner->name) }}&background=12A57F&color=fff" class="w-10 h-10 rounded-full object-cover shrink-0" alt="Avatar">
                        <div>
                            <h2 class="font-bold text-[15px] text-[#14213D] leading-none">{{ $partner->name }}</h2>
                            <p class="text-[11px] text-[#8A93A6] mt-1 capitalize">{{ $partner->role }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex-1 p-6 overflow-y-auto space-y-4 bg-[#FAF9F5]" id="chat-messages">
                    @foreach ($messages as $msg)
                        @php $isMe = $msg->sender_id === auth()->id(); @endphp
                        <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                            <div class="max-w-[75%] md:max-w-[60%] px-4 py-2.5 text-sm {{ $isMe ? 'bg-[#12A57F] text-white rounded-2xl rounded-br-sm' : 'bg-white text-[#4B5566] border border-[#E7E3D8] rounded-2xl rounded-bl-sm shadow-sm' }}">
                                {{ $msg->message }}
                            </div>
                            <span class="text-[10px] text-[#B3ACA0] mt-1.5 px-1">{{ $msg->created_at->format('H:i') }}</span>
                        </div>
                    @endforeach
                </div>

                <form action="{{ route('chat.send', $conversation) }}" method="POST" class="p-4 sm:p-5 bg-white border-t border-[#F0EEE6] shrink-0">
                    @csrf
                    <div class="flex items-center gap-3 relative">
                        <input type="text" name="message" id="message-input" required placeholder="Tulis pesan Anda di sini..." autocomplete="off" class="w-full bg-[#FAF9F5] border border-[#E7E3D8] rounded-full pl-5 pr-14 py-3 text-sm focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] transition-all">
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-[#12A57F] flex items-center justify-center text-white hover:bg-[#0F8E6D] transition-colors shadow-sm">
                            <svg class="w-4 h-4 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
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