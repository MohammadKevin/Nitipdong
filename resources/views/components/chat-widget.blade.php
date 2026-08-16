@auth
<div x-data="{ 
        openChat: false,
        inputText: '',
        isLoading: false,
        messages: [
            { role: 'ai', text: 'Halo {{ explode(' ', auth()->user()->name)[0] }}! 👋<br><br>Saya Asisten AI BelanjaIn. Ada yang bisa saya bantu hari ini terkait toko, pesanan, atau fitur lainnya?' }
        ],
        async sendMessage() {
            if(!this.inputText.trim() || this.isLoading) return;
            
            const msg = this.inputText.trim();
            this.messages.push({ role: 'user', text: msg });
            this.inputText = '';
            this.isLoading = true;
            this.scrollToBottom();

            try {
                const res = await fetch('{{ route('ai.chat') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: msg })
                });

                const data = await res.json();
                if(res.ok) {
                    this.messages.push({ role: 'ai', text: data.reply });
                } else {
                    this.messages.push({ role: 'ai', text: 'Maaf, terjadi kesalahan pada server.' });
                }
            } catch(e) {
                this.messages.push({ role: 'ai', text: 'Maaf, saya tidak dapat merespons saat ini. Periksa koneksi Anda.' });
            } finally {
                this.isLoading = false;
                this.scrollToBottom();
            }
        },
        scrollToBottom() {
            setTimeout(() => {
                const chatBox = this.$refs.chatBox;
                if(chatBox) chatBox.scrollTop = chatBox.scrollHeight;
            }, 50);
        }
    }" 
    class="fixed bottom-6 right-6 z-50">
    
    <!-- Chat Popup Box -->
    <div x-show="openChat" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="bg-white rounded-2xl shadow-2xl border border-[#E7E3D8] w-80 sm:w-96 overflow-hidden mb-4 flex flex-col"
         style="display: none; height: 450px;">
        
        <!-- Header Widget -->
        <div class="bg-[#14213D] p-4 text-white flex justify-between items-center shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-[#12A57F] to-[#F2A93B] flex items-center justify-center shadow-inner">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                </div>
                <div>
                    <h4 class="font-bold text-sm" style="font-family:'Poppins',sans-serif;">Asisten AI</h4>
                    <p class="text-[10px] text-white/70">Online & Siap Membantu</p>
                </div>
            </div>
            <button @click="openChat = false" class="text-white/60 hover:text-white transition-colors text-2xl leading-none">&times;</button>
        </div>

        <!-- Body Widget (Chat History) -->
        <div x-ref="chatBox" class="p-4 bg-[#FAF9F5] flex-1 overflow-y-auto flex flex-col gap-3">
            <p class="text-[10px] text-[#B3ACA0] text-center my-2">Hari ini</p>
            
            <template x-for="(msg, index) in messages" :key="index">
                <div class="flex gap-2 items-end" :class="msg.role === 'user' ? 'flex-row-reverse' : ''">
                    <template x-if="msg.role === 'ai'">
                        <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-[#12A57F] to-[#F2A93B] shrink-0 flex items-center justify-center">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                    </template>
                    <div class="text-xs p-3 shadow-sm max-w-[85%]"
                         :class="msg.role === 'user' ? 'bg-[#12A57F] text-white rounded-2xl rounded-br-none' : 'bg-white border border-[#EFEBDF] text-[#4B5566] rounded-2xl rounded-bl-none'"
                         x-html="msg.text">
                    </div>
                </div>
            </template>

            <!-- Loading Indicator -->
            <div x-show="isLoading" class="flex gap-2 items-end">
                <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-[#12A57F] to-[#F2A93B] shrink-0 flex items-center justify-center">
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                </div>
                <div class="bg-white border border-[#EFEBDF] text-[#4B5566] text-xs p-3 rounded-2xl rounded-bl-none shadow-sm flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 bg-[#B3ACA0] rounded-full animate-bounce"></span>
                    <span class="w-1.5 h-1.5 bg-[#B3ACA0] rounded-full animate-bounce" style="animation-delay: 0.1s"></span>
                    <span class="w-1.5 h-1.5 bg-[#B3ACA0] rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                </div>
            </div>
        </div>

        <!-- Footer Widget (Input) -->
        <div class="p-3 bg-white border-t border-[#EFEBDF] shrink-0">
            <form @submit.prevent="sendMessage()" class="flex gap-2">
                <input x-model="inputText" type="text" placeholder="Tanya sesuatu ke AI..." class="w-full bg-[#FAF9F5] border border-[#E7E3D8] rounded-full px-4 py-2 text-xs focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] transition-all outline-none" :disabled="isLoading">
                <button type="submit" class="w-8 h-8 rounded-full bg-[#12A57F] text-white shrink-0 flex items-center justify-center hover:bg-[#0F8E6D] transition-colors shadow-sm disabled:opacity-50" :disabled="isLoading">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Toggle Button Floating -->
    <button @click="openChat = !openChat; if(openChat) scrollToBottom()" class="w-14 h-14 bg-[#14213D] hover:bg-[#0c1425] text-white rounded-full shadow-lg shadow-[#14213D]/20 flex items-center justify-center transition-transform hover:scale-105 active:scale-95 focus:outline-none border-2 border-white">
        <svg class="w-6 h-6 text-[#F2A93B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
    </button>
</div>
@endauth