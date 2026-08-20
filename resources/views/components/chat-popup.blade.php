@auth
<div x-data="chatPopupComponent()"
     x-init="initChatPopup()"
     @open-chat.window="handleOpenChat($event)"
     @close-seller-chat.window="isOpen = false"
     @keydown.escape.window="if(isOpen) isOpen = false"
     class="relative">

    {{-- Unified Floating Dock (Bottom Right): AI Button + Chat Button Side-by-Side --}}
    <div class="fixed bottom-5 right-4 sm:right-5 z-40 flex items-center gap-2 select-none">
        {{-- Button 1: Asisten AI --}}
        <button type="button"
                @click="$dispatch('toggle-ai-chat')"
                :class="isAiOpen ? 'bg-cyan-700 text-white shadow-lg ring-2 ring-cyan-400' : 'bg-slate-900 hover:bg-slate-800 text-white shadow-md'"
                class="h-11 px-3.5 sm:px-4 rounded-full border border-slate-700/80 flex items-center gap-2 text-xs font-bold transition-all hover:scale-105 active:scale-95 cursor-pointer"
                title="Tanya Asisten AI SakserShop">
            <i class="fa-solid fa-sparkles text-cyan-400 text-xs"></i>
            <span class="text-xs">Asisten AI</span>
            <span x-show="isAiOpen" class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block animate-pulse"></span>
        </button>

        {{-- Button 2: Chat Penjual --}}
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

    {{-- Floating Chat Popup Window (Directly above the dock) --}}
    <div x-show="isOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-200 transform"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="fixed bottom-20 right-4 sm:bottom-20 sm:right-5 z-50 w-[calc(100vw-2rem)] sm:w-96 h-[500px] max-h-[calc(100vh-6.5rem)] bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col text-xs font-sans">

        {{-- 1. HEADER --}}
        <div class="bg-cyan-700 text-white px-4 py-3 flex items-center justify-between shadow-xs shrink-0 select-none">
            {{-- Header Left: If in Active Chat vs List --}}
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

            {{-- Header Actions --}}
            <div class="flex items-center gap-1 shrink-0">
                {{-- Expand / Go to Full Page CTA --}}
                <a :href="activeConversation ? activeConversation.full_url : '{{ route('chat.index') }}'"
                   title="Buka Halaman Penuh"
                   class="w-7 h-7 rounded-lg hover:bg-white/15 text-cyan-100 hover:text-white flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-solid fa-up-right-and-down-left-from-center text-[11px]"></i>
                </a>

                {{-- Minimize / Close Button --}}
                <button type="button" @click="isOpen = false" title="Tutup" class="w-7 h-7 rounded-lg hover:bg-white/15 text-cyan-100 hover:text-white flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>

        {{-- 2. BODY CONTENT --}}
        <div class="flex-1 flex flex-col min-h-0 bg-slate-50 relative">

            {{-- VIEW A: CONVERSATIONS LIST --}}
            <div x-show="!activeConversation" class="flex-1 flex flex-col min-h-0">
                {{-- Search Filter --}}
                <div class="p-2.5 bg-white border-b border-slate-100">
                    <div class="relative">
                        <input type="text" x-model="searchQuery" placeholder="Cari nama toko / pesan..."
                               class="w-full h-8 pl-8 pr-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-cyan-600 focus:ring-1 focus:ring-cyan-600 transition-all">
                        <i class="fa-solid fa-magnifying-glass text-[10px] text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2"></i>
                    </div>
                </div>

                {{-- Conversations Scroll List --}}
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

            {{-- VIEW B: ACTIVE CHAT MESSAGES --}}
            <div x-show="activeConversation" class="flex-1 flex flex-col min-h-0 bg-slate-50" x-cloak>
                {{-- Message Bubbles Scroll Area --}}
                <div id="popup-chat-messages" class="flex-1 overflow-y-auto p-3 space-y-2.5 scrollbar-thin">
                    <template x-if="isLoadingMessages">
                        <div class="py-12 text-center text-slate-400">
                            <i class="fa-solid fa-spinner animate-spin text-lg text-cyan-600 mb-1"></i>
                            <p class="text-[10px]">Memuat pesan...</p>
                        </div>
                    </template>

                    <template x-for="msg in messages" :key="msg.id">
                        <div class="flex flex-col" :class="msg.is_me ? 'items-end' : 'items-start'">
                            <div class="max-w-[80%] rounded-2xl px-3 py-2 text-xs shadow-2xs leading-relaxed break-words"
                                 :class="msg.is_me
                                    ? 'bg-cyan-700 text-white rounded-tr-xs'
                                    : 'bg-white text-slate-800 border border-slate-200 rounded-tl-xs'">
                                <p x-text="msg.message"></p>
                            </div>
                            <span class="text-[9px] text-slate-400 mt-0.5 px-1" x-text="msg.time"></span>
                        </div>
                    </template>
                </div>

                {{-- Input Bar --}}
                <div class="p-2.5 bg-white border-t border-slate-200/90 shrink-0">
                    <form @submit.prevent="sendMessage()" class="flex items-center gap-1.5">
                        <input type="text"
                               x-model="newMessageText"
                               placeholder="Tulis pesan ke penjual..."
                               :disabled="isSending"
                               class="flex-1 h-9 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-cyan-600 focus:ring-1 focus:ring-cyan-600 transition-all disabled:opacity-50">
                        <button type="submit"
                                :disabled="isSending || !newMessageText.trim()"
                                class="w-9 h-9 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white flex items-center justify-center transition-all shadow-xs disabled:opacity-50 disabled:cursor-not-allowed shrink-0 cursor-pointer">
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
                setInterval(() => {
                    if (!this.activeConversation) {
                        this.fetchConversations(false);
                    }
                }, 10000);
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

            async fetchConversations(showLoading = true) {
                if (showLoading) this.isLoadingList = true;
                try {
                    const res = await fetch('{{ route('chat.api.conversations') }}');
                    if (res.ok) {
                        const data = await res.json();
                        this.conversations = data.conversations || [];
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
                        this.messages = data.messages || [];
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

            async sendMessage() {
                const text = this.newMessageText.trim();
                if (!text || !this.activeConversation || this.isSending) return;

                this.isSending = true;
                try {
                    const res = await fetch(`/chat/api/${this.activeConversation.id}/send`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ message: text })
                    });
                    const data = await res.json();
                    if (res.ok && data.status === 'success') {
                        this.newMessageText = '';
                        this.messages.push(data.message);
                        this.$nextTick(() => this.scrollToBottom());
                    }
                } catch (e) {
                    console.error(e);
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
                    // Set active conversation immediately so user is taken straight into the seller chat room
                    this.activeConversation = {
                        id: null,
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
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await res.json();
                        if (res.ok && data.status === 'success') {
                            this.activeConversation = {
                                id: data.conversation_id,
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
