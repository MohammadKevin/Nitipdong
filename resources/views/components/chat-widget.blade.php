@auth
<div x-data="aiChatWidget()"
     x-init="initAiWidget()"
     @toggle-ai-chat.window="toggleAiChat()"
     @open-ai-chat.window="openAiChat()"
     @close-ai-chat.window="closeAiChat()"
     @keydown.escape.window="if(openChat) closeAiChat()"
     class="relative">
    {{-- AI Chat Floating Popup Window (Directly above the floating dock) --}}
    <div x-show="openChat"
         x-cloak
         x-transition:enter="transition ease-out duration-200 transform"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="fixed bottom-20 right-4 sm:bottom-20 sm:right-5 z-50 bg-white rounded-2xl shadow-2xl border border-slate-200 w-[calc(100vw-2rem)] sm:w-96 overflow-hidden flex flex-col h-[500px] max-h-[calc(100vh-6.5rem)] text-xs font-sans">

        {{-- Header --}}
        <div class="bg-slate-900 px-4 py-3 text-white flex justify-between items-center shrink-0 border-b border-slate-800 select-none">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-cyan-500/20 border border-cyan-400/30 text-cyan-300 flex items-center justify-center text-xs shadow-xs">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div>
                    <h4 class="font-bold text-xs text-white leading-tight">Asisten AI SakserShop</h4>
                    <p class="text-[10px] text-cyan-300 font-medium flex items-center gap-1 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>
                        Online & Siap Membantu
                    </p>
                </div>
            </div>
            <button @click="closeAiChat()" class="text-slate-400 hover:text-white transition-colors w-7 h-7 rounded-lg hover:bg-white/10 flex items-center justify-center text-sm leading-none cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- Messages Container --}}
        <div x-ref="chatBox" class="p-4 bg-slate-50/70 flex-1 overflow-y-auto flex flex-col gap-3 text-xs scrollbar-thin">
            <div class="text-[10px] text-slate-400 text-center my-1">
                Hari ini
            </div>

            <template x-for="(msg, index) in messages" :key="index">
                <div class="flex gap-2 items-start" :class="msg.role === 'user' ? 'flex-row-reverse' : ''">
                    <template x-if="msg.role === 'ai'">
                        <div class="w-7 h-7 rounded-lg bg-cyan-50 border border-cyan-200 text-cyan-700 shrink-0 flex items-center justify-center text-xs mt-0.5 shadow-2xs">
                            <i class="fa-solid fa-robot"></i>
                        </div>
                    </template>
                    <div class="p-3 shadow-2xs max-w-[82%] text-xs leading-relaxed"
                         :class="msg.role === 'user' ? 'bg-cyan-700 text-white rounded-2xl rounded-tr-xs' : 'bg-white border border-slate-200/90 text-slate-800 rounded-2xl rounded-tl-xs'"
                         x-html="msg.text">
                    </div>
                </div>
            </template>

            <div x-show="isLoading" class="flex gap-2 items-start">
                <div class="w-7 h-7 rounded-lg bg-cyan-50 border border-cyan-200 text-cyan-700 shrink-0 flex items-center justify-center text-xs mt-0.5">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div class="bg-white border border-slate-200 text-slate-500 text-xs p-3 rounded-2xl rounded-tl-xs shadow-2xs flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce"></span>
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.15s"></span>
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.3s"></span>
                </div>
            </div>
        </div>

        {{-- Quick Prompts --}}
        <div class="px-3 pt-2 pb-1 bg-white border-t border-slate-100 flex gap-1.5 overflow-x-auto scrollbar-none shrink-0">
            <button type="button" @click="askQuick('Bagaimana cara buka toko?')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-cyan-50 hover:text-cyan-800 text-slate-600 text-[10px] font-medium shrink-0 transition-colors cursor-pointer">
                Buka Toko
            </button>
            <button type="button" @click="askQuick('Apa itu Flash Sale SakserShop?')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-cyan-50 hover:text-cyan-800 text-slate-600 text-[10px] font-medium shrink-0 transition-colors cursor-pointer">
                Flash Sale
            </button>
            <button type="button" @click="askQuick('Bagaimana cara pakai voucher?')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-cyan-50 hover:text-cyan-800 text-slate-600 text-[10px] font-medium shrink-0 transition-colors cursor-pointer">
                Voucher Diskon
            </button>
        </div>

        {{-- Input Form --}}
        <div class="p-3 bg-white border-t border-slate-100 shrink-0">
            <form @submit.prevent="sendMessage()" class="flex gap-2">
                <input x-model="inputText" type="text" placeholder="Ketik pertanyaan ke AI..."
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:ring-1 focus:ring-cyan-600 focus:border-cyan-600 focus:bg-white transition-all outline-none"
                       :disabled="isLoading">
                <button type="submit"
                        class="w-9 h-9 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white shrink-0 flex items-center justify-center transition-colors shadow-xs disabled:opacity-50 cursor-pointer"
                        :disabled="isLoading">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- Fallback Standalone Trigger (Only displayed if unified dock is not present on the page) --}}
    <div x-show="!hasUnifiedDock"
         class="fixed bottom-5 right-5 z-40">
        <button @click="toggleAiChat()"
                class="h-11 px-4 bg-slate-900 hover:bg-slate-800 text-white rounded-full shadow-lg border border-slate-700 flex items-center gap-2 transition-all hover:scale-105 active:scale-95 text-xs font-bold cursor-pointer group"
                title="Tanya Asisten AI">
            <i class="fa-solid fa-sparkles text-cyan-400 text-xs group-hover:rotate-12 transition-transform"></i>
            <span>Asisten AI</span>
        </button>
    </div>
</div>

<script>
function aiChatWidget() {
    return {
        openChat: false,
        inputText: '',
        isLoading: false,
        hasUnifiedDock: false,
        messages: [
            { 
                role: 'ai', 
                text: 'Halo! 👋 Saya <strong>Asisten AI SakserShop</strong>. Ada yang bisa saya bantu hari ini seputar toko, belanja, atau promo flash sale?' 
            }
        ],
        initAiWidget() {
            setTimeout(() => {
                this.hasUnifiedDock = !!window.hasChatPopupDock;
            }, 100);
        },
        toggleAiChat() {
            if (this.openChat) {
                this.closeAiChat();
            } else {
                this.openAiChat();
            }
        },
        openAiChat() {
            this.openChat = true;
            window.dispatchEvent(new CustomEvent('close-seller-chat'));
            window.dispatchEvent(new CustomEvent('ai-chat-state-changed', { detail: { isOpen: true } }));
            this.scrollToBottom();
        },
        closeAiChat() {
            this.openChat = false;
            window.dispatchEvent(new CustomEvent('ai-chat-state-changed', { detail: { isOpen: false } }));
        },
        askQuick(promptText) {
            this.inputText = promptText;
            this.sendMessage();
        },
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
                if(res.ok && data.reply) {
                    this.messages.push({ role: 'ai', text: data.reply });
                } else {
                    this.messages.push({ role: 'ai', text: 'Maaf, terjadi kendala teknis pada server.' });
                }
            } catch(e) {
                this.messages.push({ role: 'ai', text: 'Maaf, tidak dapat terhubung ke server saat ini. Silakan periksa koneksi Anda.' });
            } finally {
                this.isLoading = false;
                this.scrollToBottom();
            }
        },
        scrollToBottom() {
            setTimeout(() => {
                const chatBox = this.$refs.chatBox;
                if(chatBox) chatBox.scrollTop = chatBox.scrollHeight;
            }, 60);
        }
    };
}
</script>
@endauth