@auth
<div x-data="aiChatWidget()" class="fixed bottom-6 right-6 z-50">
    <div x-show="openChat"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="bg-white rounded-2xl shadow-xl border border-slate-200 w-80 sm:w-96 overflow-hidden mb-4 flex flex-col h-[480px]">

        <div class="bg-slate-900 px-4 py-3.5 text-white flex justify-between items-center shrink-0 border-b border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-sm shadow-xs">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div>
                    <h4 class="font-bold text-xs text-white leading-tight">Asisten AI BelanjaIn</h4>
                    <p class="text-[10px] text-emerald-400 font-medium flex items-center gap-1 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block animate-pulse"></span>
                        Online & Siap Membantu
                    </p>
                </div>
            </div>
            <button @click="openChat = false" class="text-slate-400 hover:text-white transition-colors p-1 text-base leading-none">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div x-ref="chatBox" class="p-4 bg-slate-50/70 flex-1 overflow-y-auto flex flex-col gap-3 text-xs">
            <div class="text-[10px] text-slate-400 text-center my-1">
                Hari ini
            </div>

            <template x-for="(msg, index) in messages" :key="index">
                <div class="flex gap-2 items-start" :class="msg.role === 'user' ? 'flex-row-reverse' : ''">
                    <template x-if="msg.role === 'ai'">
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-600 shrink-0 flex items-center justify-center text-xs mt-0.5 shadow-2xs">
                            <i class="fa-solid fa-robot"></i>
                        </div>
                    </template>
                    <div class="p-3 shadow-xs max-w-[82%] text-xs leading-relaxed"
                         :class="msg.role === 'user' ? 'bg-emerald-600 text-white rounded-2xl rounded-tr-xs' : 'bg-white border border-slate-200/80 text-slate-800 rounded-2xl rounded-tl-xs'"
                         x-html="msg.text">
                    </div>
                </div>
            </template>

            <div x-show="isLoading" class="flex gap-2 items-start">
                <div class="w-7 h-7 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-600 shrink-0 flex items-center justify-center text-xs mt-0.5">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div class="bg-white border border-slate-200 text-slate-500 text-xs p-3 rounded-2xl rounded-tl-xs shadow-xs flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce"></span>
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.15s"></span>
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.3s"></span>
                </div>
            </div>
        </div>

        <div class="px-3 pt-2 pb-1 bg-white border-t border-slate-100 flex gap-1.5 overflow-x-auto scrollbar-none shrink-0">
            <button type="button" @click="askQuick('Bagaimana cara buka toko?')" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-slate-600 text-[10px] font-medium shrink-0 transition-colors">
                Buka Toko
            </button>
            <button type="button" @click="askQuick('Apa itu Flash Sale BelanjaIn?')" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-slate-600 text-[10px] font-medium shrink-0 transition-colors">
                Flash Sale
            </button>
            <button type="button" @click="askQuick('Bagaimana cara pakai voucher?')" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-slate-600 text-[10px] font-medium shrink-0 transition-colors">
                Voucher Diskon
            </button>
        </div>

        <div class="p-3 bg-white shrink-0">
            <form @submit.prevent="sendMessage()" class="flex gap-2">
                <input x-model="inputText" type="text" placeholder="Ketik pertanyaan ke AI..."
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 focus:bg-white transition-all outline-none"
                       :disabled="isLoading">
                <button type="submit"
                        class="w-9 h-9 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white shrink-0 flex items-center justify-center transition-colors shadow-xs disabled:opacity-50"
                        :disabled="isLoading">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                </button>
            </form>
        </div>
    </div>

    <button @click="openChat = !openChat; if(openChat) scrollToBottom()"
            class="w-13 h-13 p-3.5 bg-slate-900 hover:bg-slate-800 text-white rounded-full shadow-lg border border-slate-700 flex items-center justify-center transition-transform hover:scale-105 active:scale-95 focus:outline-none relative group"
            title="Tanya Asisten AI">
        <i class="fa-solid fa-sparkles text-emerald-400 text-lg group-hover:rotate-12 transition-transform"></i>
        <span class="absolute -top-1 -right-1 w-3 h-3 bg-emerald-400 rounded-full border-2 border-white animate-pulse"></span>
    </button>
</div>

<script>
function aiChatWidget() {
    return {
        openChat: false,
        inputText: '',
        isLoading: false,
        messages: [
            { 
                role: 'ai', 
                text: 'Halo! 👋 Saya <strong>Asisten AI BelanjaIn</strong>. Ada yang bisa saya bantu hari ini seputar toko, belanja, atau promo flash sale?' 
            }
        ],
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