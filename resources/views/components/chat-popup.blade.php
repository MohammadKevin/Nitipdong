@auth
<div x-data="chatPopupComponent()"
     x-init="initChatPopup()"
     @open-chat.window="handleOpenChat($event)"
     @close-seller-chat.window="isOpen = false"
     @keydown.escape.window="if(isOpen) isOpen = false"
     class="relative">

    <div class="fixed bottom-20 md:bottom-5 right-4 sm:right-5 z-40 flex items-center gap-2 select-none">
        
        <button type="button"
                @click="$dispatch('toggle-ai-chat')"
                :class="isAiOpen ? 'bg-cyan-700 text-white shadow-lg ring-2 ring-cyan-400' : 'bg-slate-900 hover:bg-slate-800 text-white shadow-md'"
                class="h-11 px-3.5 sm:px-4 rounded-full border border-slate-700/80 flex items-center gap-2 text-xs font-bold transition-all hover:scale-105 active:scale-95 cursor-pointer"
                title="Tanya Asisten AI NitipDong">
            <i class="fa-solid fa-sparkles text-cyan-400 text-xs"></i>
            <span class="text-xs">Asisten AI</span>
            <span x-show="isAiOpen" class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block animate-pulse"></span>
        </button>

        <button type="button"
                @click="togglePopup()"
                :class="isOpen ? 'bg-cyan-800 text-white shadow-lg ring-2 ring-cyan-400' : 'bg-cyan-700 hover:bg-cyan-800 text-white shadow-md hover:shadow-lg'"
                class="h-11 px-3.5 sm:px-4 rounded-full border border-cyan-600 flex items-center gap-2 text-xs font-bold transition-all hover:scale-105 active:scale-95 cursor-pointer relative"
                title="Pesan & Obrolan Toko">
            <div class="relative">
                <i class="fa-solid fa-comments text-sm"></i>
                <span x-show="totalUnread > 0"
                      class="absolute -top-2 -right-2 min-w-[16px] h-4 px-1 rounded-full bg-rose-500 text-white text-[9px] font-bold flex items-center justify-center ring-2 ring-white"
                      x-text="totalUnread > 99 ? '99+' : totalUnread">
                </span>
            </div>
            <span class="text-xs">Chat</span>
            <span x-show="isOpen" class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>
        </button>
    </div>

    <div x-show="isOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-200 transform"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="fixed bottom-36 md:bottom-20 right-4 md:right-5 z-50 w-[calc(100vw-2rem)] sm:w-96 h-[500px] max-h-[calc(100vh-10.5rem)] md:max-h-[calc(100vh-6.5rem)] bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col text-xs font-sans">

        <div class="bg-cyan-700 text-white px-4 py-3 flex items-center justify-between shadow-xs shrink-0 select-none">
            
            <div class="flex items-center gap-2.5 min-w-0">
                <template x-if="activeConversation">
                    <button type="button" @click="backToList()" class="w-7 h-7 -ml-1 rounded-lg hover:bg-white/15 flex items-center justify-center transition-colors cursor-pointer" title="Kembali ke daftar pesan">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                    </button>
                </template>

                <template x-if="!activeConversation">
                    <div class="w-7 h-7 rounded-lg bg-white/15 flex items-center justify-center">
                        <i class="fa-solid fa-comments text-xs text-white"></i>
                    </div>
                </template>

                <template x-if="activeConversation">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="relative shrink-0">
                            <img :src="activeConversation.partner.avatar" :alt="activeConversation.partner.name" class="w-7 h-7 rounded-full object-cover border border-white/40 bg-white" onerror="this.src='/img/saksershop-logo.png'">
                            <span class="absolute bottom-0 right-0 w-2 h-2 rounded-full bg-emerald-400 ring-1 ring-white"></span>
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-bold text-xs truncate leading-tight" x-text="activeConversation.partner.name"></h4>
                            <span class="text-[10px] text-cyan-200 block truncate">Online</span>
                        </div>
                    </div>
                </template>

                <template x-if="!activeConversation">
                    <div>
                        <h4 class="font-bold text-xs leading-tight">Pesan & Chat</h4>
                        <span class="text-[10px] text-cyan-200" x-text="totalUnread > 0 ? totalUnread + ' pesan belum dibaca' : 'Obrolan Penjual'"></span>
                    </div>
                </template>
            </div>

            <div class="flex items-center gap-1 shrink-0">
                
                <a :href="activeConversation ? activeConversation.full_url : '{{ route('chat.index') }}'"
                   title="Buka Halaman Penuh"
                   class="w-7 h-7 rounded-lg hover:bg-white/15 text-cyan-100 hover:text-white flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-solid fa-up-right-and-down-left-from-center text-[11px]"></i>
                </a>

                <button type="button" @click="isOpen = false" title="Tutup" class="w-7 h-7 rounded-lg hover:bg-white/15 text-cyan-100 hover:text-white flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>

        <div class="flex-1 flex flex-col min-h-0 bg-slate-50 relative">

            <div x-show="!activeConversation" class="flex-1 flex flex-col min-h-0">
                
                <div class="p-2.5 bg-white border-b border-slate-100">
                    <div class="relative">
                        <input type="text" x-model="searchQuery" placeholder="Cari nama toko / pesan..."
                               class="w-full h-8 pl-8 pr-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-cyan-600 focus:ring-1 focus:ring-cyan-600 transition-all">
                        <i class="fa-solid fa-magnifying-glass text-[10px] text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2"></i>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto divide-y divide-slate-100 p-1.5 scrollbar-thin">
                    <template x-if="filteredConversations.length > 0">
                        <div>
                            <template x-for="c in filteredConversations" :key="c.id">
                                <div @click="openConversation(c)"
                                     class="p-2.5 hover:bg-white rounded-xl transition-all flex items-center gap-3 cursor-pointer group border border-transparent hover:border-slate-200 hover:shadow-2xs">
                                    <div class="relative shrink-0">
                                        <img :src="c.partner.avatar" :alt="c.partner.name" class="w-10 h-10 rounded-full object-cover border border-slate-200 bg-white" onerror="this.src='/img/saksershop-logo.png'">
                                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-1">
                                            <span class="font-bold text-slate-900 truncate group-hover:text-cyan-700 transition-colors" x-text="c.partner.name"></span>
                                            <span class="text-[10px] text-slate-400 shrink-0" x-text="c.last_message_time"></span>
                                        </div>
                                        <div class="flex items-center justify-between gap-2 mt-0.5">
                                            <p class="text-[11px] text-slate-500 truncate" :class="c.unread_count > 0 ? 'font-bold text-slate-900' : ''" x-text="c.last_message"></p>
                                            <span x-show="c.unread_count > 0"
                                                  class="min-w-[16px] h-4 px-1 rounded-full bg-cyan-700 text-white font-bold text-[9px] flex items-center justify-center shrink-0"
                                                  x-text="c.unread_count"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="filteredConversations.length === 0 && !isLoadingList">
                        <div class="py-16 text-center text-slate-400 p-4">
                            <i class="fa-regular fa-comments text-3xl mb-2 text-slate-300"></i>
                            <p class="font-bold text-slate-700 text-xs">Belum ada percakapan</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Kirim pesan ke penjual melalui halaman produk untuk memulai obrolan.</p>
                        </div>
                    </template>

                    <template x-if="isLoadingList">
                        <div class="py-16 text-center text-slate-400">
                            <i class="fa-solid fa-spinner animate-spin text-xl text-cyan-600 mb-2"></i>
                            <p class="text-[11px]">Memuat percakapan...</p>
                        </div>
                    </template>
                </div>
            </div>

            <div x-show="activeConversation" class="flex-1 flex flex-col min-h-0 bg-slate-50" x-cloak>
                
                <div id="popup-chat-messages" class="flex-1 overflow-y-auto p-3 space-y-2.5 scrollbar-thin">
                    <template x-if="isLoadingMessages">
                        <div class="py-12 text-center text-slate-400">
                            <i class="fa-solid fa-spinner animate-spin text-lg text-cyan-600 mb-1"></i>
                            <p class="text-[10px]">Memuat pesan...</p>
                        </div>
                    </template>

                    <template x-for="msg in messages" :key="msg.id">
                        <div class="flex flex-col" :class="msg.is_me ? 'items-end' : 'items-start'">
                            <div class="max-w-[80%] rounded-2xl px-3.5 py-2 text-xs shadow-2xs leading-relaxed break-words"
                                 :class="msg.is_me
                                    ? 'bg-cyan-700 text-white rounded-tr-xs'
                                    : 'bg-white text-slate-800 border border-slate-200 rounded-tl-xs'">
                                <p x-text="msg.message"></p>
                            </div>
                            <div class="flex items-center gap-1 mt-0.5 px-1 text-[9px] text-slate-400 font-mono">
                                <span x-text="msg.time"></span>
                                <template x-if="msg.is_me">
                                    <span class="inline-flex items-center ml-0.5" :title="msg.is_read ? 'Sudah dibaca' : 'Terkirim (belum dibaca)'">
                                        <i class="fa-solid fa-check-double text-[9px]"
                                           :class="msg.is_read ? 'text-sky-500 font-bold' : 'text-slate-300'"></i>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="p-2.5 bg-white border-t border-slate-200/90 shrink-0">
                    <form @submit.prevent="sendMessage()" class="flex items-center gap-1.5">
                        <input type="text"
                               x-model="newMessageText"
                               @keydown.enter.prevent="sendMessage()"
                               placeholder="Tulis pesan ke penjual..."
                               :disabled="isSending"
                               class="flex-1 h-9 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-cyan-600 focus:ring-1 focus:ring-cyan-600 transition-all disabled:opacity-50 outline-none">
                        <button type="button"
                                @click="sendMessage()"
                                :disabled="isSending || !newMessageText.trim()"
                                class="w-9 h-9 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white flex items-center justify-center transition-all shadow-xs disabled:opacity-50 disabled:cursor-not-allowed shrink-0 cursor-pointer"
                                title="Kirim Pesan">
                            <i class="fa-solid fa-paper-plane text-xs" :class="isSending ? 'animate-pulse' : ''"></i>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

@pushOnce('scripts')
<script>
    window.hasChatPopupDock = true;

    function chatPopupComponent() {
        return {
            isOpen: false,
            isAiOpen: false,
            conversations: [],
            totalUnread: 0,
            activeConversation: null,
            messages: [],
            searchQuery: '',
            newMessageText: '',
            isLoadingList: false,
            isLoadingMessages: false,
            isSending: false,
            pollInterval: null,

            initChatPopup() {
                window.addEventListener('ai-chat-state-changed', (e) => {
                    this.isAiOpen = !!e.detail?.isOpen;
                });
                this.fetchConversations();
                // Check for new messages periodically for real-time notifications
                setInterval(() => {
                    this.fetchConversations(false);
                }, 4000);
            },

            togglePopup() {
                if (this.isOpen) {
                    this.isOpen = false;
                } else {
                    this.openPopup();
                }
            },

            openPopup() {
                this.isOpen = true;
                window.dispatchEvent(new CustomEvent('close-ai-chat'));
                if (!this.activeConversation) {
                    this.fetchConversations();
                }
            },

            get filteredConversations() {
                if (!this.searchQuery.trim()) return this.conversations;
                const q = this.searchQuery.toLowerCase();
                return this.conversations.filter(c =>
                    c.partner.name.toLowerCase().includes(q) ||
                    (c.last_message && c.last_message.toLowerCase().includes(q))
                );
            },

            playNotificationSound() {
                try {
                    const AudioCtxClass = window.AudioContext || window.webkitAudioContext;
                    if (!AudioCtxClass) return;
                    const audioCtx = new AudioCtxClass();
                    
                    const playChime = (freq, startTime, duration) => {
                        const osc = audioCtx.createOscillator();
                        const gain = audioCtx.createGain();
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(freq, startTime);
                        gain.gain.setValueAtTime(0.12, startTime);
                        gain.gain.exponentialRampToValueAtTime(0.001, startTime + duration);
                        osc.connect(gain);
                        gain.connect(audioCtx.destination);
                        osc.start(startTime);
                        osc.stop(startTime + duration);
                    };
                    
                    const now = audioCtx.currentTime;
                    // Double-ding (Slack style): C5 then E5
                    playChime(523.25, now, 0.15);
                    playChime(659.25, now + 0.12, 0.25);
                } catch (e) {
                    console.warn('Failed to play synthesized sound', e);
                }
            },

            playChatSound() {
                try {
                    const AudioCtxClass = window.AudioContext || window.webkitAudioContext;
                    if (!AudioCtxClass) return;
                    const audioCtx = new AudioCtxClass();
                    
                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5 note
                    gain.gain.setValueAtTime(0.08, audioCtx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.15);
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    osc.start();
                    osc.stop(audioCtx.currentTime + 0.15);
                } catch (e) {
                    console.warn('Failed to play chat sound', e);
                }
            },

            async fetchConversations(showLoading = true) {
                if (showLoading) this.isLoadingList = true;
                try {
                    const res = await fetch('{{ route('chat.api.conversations') }}');
                    if (res.ok) {
                        const data = await res.json();
                        
                        const oldConversations = this.conversations || [];
                        const newConversations = data.conversations || [];
                        
                        let hasNewMsg = false;
                        newConversations.forEach(newConv => {
                            const oldConv = oldConversations.find(o => o.id === newConv.id);
                            const oldUnread = oldConv ? oldConv.unread_count : 0;
                            
                            // If unread count has increased
                            if (newConv.unread_count > oldUnread) {
                                const isCurrentActive = this.activeConversation && this.activeConversation.id === newConv.id;
                                if (!this.isOpen || !isCurrentActive) {
                                    hasNewMsg = true;
                                    
                                    if (window.toast) {
                                        window.toast.info(
                                            newConv.last_message,
                                            'Pesan baru dari ' + newConv.partner.name,
                                            {
                                                duration: 6000,
                                                action: {
                                                    label: 'Balas',
                                                    onClick: () => {
                                                        this.openPopup();
                                                        this.openConversation(newConv);
                                                    }
                                                }
                                            }
                                        );
                                    }
                                }
                            }
                        });
                        
                        if (hasNewMsg) {
                            this.playNotificationSound();
                        }
                        
                        this.conversations = newConversations;
                        this.totalUnread = data.total_unread || 0;
                    }
                } catch (e) {
                    console.error(e);
                } finally {
                    if (showLoading) this.isLoadingList = false;
                }
            },

            async openConversation(c) {
                this.activeConversation = c;
                this.messages = [];
                this.isLoadingMessages = true;
                await this.fetchMessages(c.id);
                this.startPolling(c.id);
            },

            async fetchMessages(convId) {
                try {
                    const res = await fetch(`/chat/api/${convId}/messages`);
                    if (res.ok) {
                        const data = await res.json();
                        const newMessages = data.messages || [];
                        
                        // Check if a new message has arrived in the active chat (from the partner)
                        if (this.messages.length > 0 && newMessages.length > this.messages.length) {
                            const lastNewMsg = newMessages[newMessages.length - 1];
                            const lastOldMsg = this.messages[this.messages.length - 1];
                            
                            if (lastNewMsg.id !== lastOldMsg.id && !lastNewMsg.is_me) {
                                this.playChatSound();
                            }
                        }
                        
                        this.messages = newMessages;
                        if (this.activeConversation) {
                            this.activeConversation.full_url = data.conversation.full_url;
                        }
                        this.$nextTick(() => this.scrollToBottom());
                    }
                } catch (e) {
                    console.error(e);
                } finally {
                    this.isLoadingMessages = false;
                }
            },

            startPolling(convId) {
                if (this.pollInterval) clearInterval(this.pollInterval);
                this.pollInterval = setInterval(() => {
                    if (this.isOpen && this.activeConversation && this.activeConversation.id === convId) {
                        this.fetchMessages(convId);
                    }
                }, 3000);
            },

            backToList() {
                if (this.pollInterval) clearInterval(this.pollInterval);
                this.activeConversation = null;
                this.messages = [];
                this.fetchConversations(false);
            },

            getCsrfToken() {
                return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                    || document.querySelector('input[name="_token"]')?.value 
                    || '{{ csrf_token() }}';
            },

            async sendMessage() {
                const text = this.newMessageText.trim();
                if (!text || !this.activeConversation || this.isSending) return;

                let convId = this.activeConversation.id;

                // If conversation ID is not yet assigned but receiver_id exists, initialize it on-the-fly
                if (!convId && this.activeConversation.receiver_id) {
                    this.isSending = true;
                    try {
                        const initRes = await fetch(`/chat/api/start/${encodeURIComponent(this.activeConversation.receiver_id)}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.getCsrfToken(),
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const initData = await initRes.json();
                        if (initRes.ok && initData.status === 'success') {
                            convId = initData.conversation_id;
                            this.activeConversation.id = convId;
                            this.activeConversation.full_url = initData.full_url;
                            if (initData.partner) this.activeConversation.partner = initData.partner;
                            this.startPolling(convId);
                        }
                    } catch (e) {
                        console.error('Error starting conversation:', e);
                    }
                }

                if (!convId) {
                    if (window.toast) window.toast.error('Sedang menghubungkan ke toko, silakan coba lagi...');
                    this.isSending = false;
                    return;
                }

                this.isSending = true;
                this.newMessageText = '';

                // Optimistic UI push for instant feel
                const tempId = 'temp_' + Date.now();
                const nowTime = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':');
                const optimisticMsg = {
                    id: tempId,
                    sender_id: {{ Auth::id() ?? 0 }},
                    is_me: true,
                    message: text,
                    is_read: false,
                    time: nowTime
                };
                this.messages.push(optimisticMsg);
                this.$nextTick(() => this.scrollToBottom());

                try {
                    const res = await fetch(`/chat/api/${convId}/send`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.getCsrfToken(),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ message: text })
                    });
                    const data = await res.json();
                    if (res.ok && data.status === 'success') {
                        const idx = this.messages.findIndex(m => m.id === tempId);
                        if (idx !== -1) {
                            this.messages[idx] = data.message;
                        }
                        this.$nextTick(() => this.scrollToBottom());
                    } else {
                        if (window.toast) window.toast.error(data.message || 'Pesan gagal terkirim');
                    }
                } catch (e) {
                    console.error(e);
                    if (window.toast) window.toast.error('Tidak dapat mengirim pesan.');
                } finally {
                    this.isSending = false;
                }
            },

            scrollToBottom() {
                const container = document.getElementById('popup-chat-messages');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            },

            async handleOpenChat(event) {
                this.isOpen = true;
                window.dispatchEvent(new CustomEvent('close-ai-chat'));

                if (event.detail && event.detail.receiver_id) {
                    // Set active conversation immediately with receiver_id so user can type immediately
                    this.activeConversation = {
                        id: null,
                        receiver_id: event.detail.receiver_id,
                        full_url: '{{ route('chat.index') }}',
                        partner: {
                            name: event.detail.receiver_name || 'Penjual',
                            avatar: event.detail.receiver_avatar || '/img/saksershop-logo.png',
                            is_online: true
                        }
                    };
                    this.messages = [];
                    this.isLoadingMessages = true;

                    try {
                        const res = await fetch(`/chat/api/start/${encodeURIComponent(event.detail.receiver_id)}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.getCsrfToken(),
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await res.json();
                        if (res.ok && data.status === 'success') {
                            this.activeConversation = {
                                id: data.conversation_id,
                                receiver_id: event.detail.receiver_id,
                                full_url: data.full_url,
                                partner: data.partner || this.activeConversation.partner
                            };
                            await this.fetchMessages(data.conversation_id);
                            this.startPolling(data.conversation_id);
                            this.fetchConversations(false);
                        } else {
                            if (window.toast) {
                                window.toast.error(data.message || 'Gagal memulai percakapan.');
                            }
                            this.backToList();
                        }
                    } catch (e) {
                        console.error(e);
                        this.backToList();
                    } finally {
                        this.isLoadingMessages = false;
                    }
                } else {
                    this.activeConversation = null;
                    this.fetchConversations();
                }
            }
        };
    }
</script>
@endPushOnce
@endauth
