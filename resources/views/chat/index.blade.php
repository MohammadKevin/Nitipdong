@php
    $layout = match(auth()->user()->role) {
        'super_admin' => 'super-admin-layout',
        'admin'       => 'admin-layout',
        'seller'      => 'seller-layout',
        default       => 'app-layout',
    };
    $isSidebarLayout = in_array(auth()->user()->role, ['super_admin', 'admin', 'seller']);
    
    $conversationsData = $conversations->map(function($conv) {
        $userId = auth()->id();
        $partner = $conv->user_one_id === $userId ? $conv->userTwo : $conv->userOne;
        $partnerName = ($partner && $partner->role === 'seller' && $partner->store) ? $partner->store->name : ($partner?->name ?? 'Pengguna');
        $lastMsg = $conv->messages->first();
        $unreadCount = $conv->messages->where('sender_id', '!=', $userId)->where('is_read', false)->count();

        return [
            'id' => $conv->id,
            'partner' => [
                'id' => $partner?->id,
                'name' => $partnerName,
                'role' => $partner?->role ?? 'customer',
                'avatar' => $partner?->avatar_url ?? ('https://ui-avatars.com/api/?name=' . urlencode($partnerName) . '&background=0e7490&color=fff&size=80'),
            ],
            'last_message' => $lastMsg ? $lastMsg->message : 'Mulai percakapan baru...',
            'unread_count' => $unreadCount,
            'time' => $conv->updated_at->diffForHumans(),
        ];
    })->values();
@endphp

<x-dynamic-component :component="$layout">
    <x-slot name="title">
        Pesan & Percakapan - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Pusat Pesan & Komunikasi
    </x-slot>

    <div class="{{ $isSidebarLayout ? 'space-y-4' : 'page-container py-4 sm:py-6 min-h-[80vh]' }}"
         x-data="fullChatPage({{ json_encode($conversationsData) }}, {{ request('conv') ? (int)request('conv') : 'null' }})">

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-comments text-cyan-700"></i>
                    Pesan & Percakapan
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    @if(auth()->user()->role === 'seller')
                        {{ ($activeTab ?? '') === 'admin' ? 'Obrolan langsung dan konsultasi bantuan dengan tim Admin Resmi.' : 'Kelola obrolan langsung Anda dengan pembeli dan calon pelanggan toko.' }}
                    @else
                        Kelola obrolan langsung Anda dengan pembeli, pelanggan toko, atau pengelola marketplace.
                    @endif
                </p>
            </div>

            @if(in_array(auth()->user()->role, ['super_admin', 'seller']) && isset($admins) && $admins->isNotEmpty())
                <form id="startAdminChatForm" method="POST" class="flex gap-2 w-full sm:w-auto" onsubmit="
                    const select = document.getElementById('admin_select');
                    if(!select.value) { alert('Pilih admin terlebih dahulu!'); return false; }
                    this.action = '/chat/start/' + select.value;
                    return true;
                ">
                    @csrf
                    <select id="admin_select" class="input text-xs rounded-xl bg-white flex-1 sm:w-52 h-9.5 border border-slate-200">
                        <option value="">-- Hubungi Admin Official --</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->getRouteKey() }}">{{ $admin->name }} ({{ ucfirst($admin->role) }})</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary text-xs h-9.5 px-4 rounded-xl bg-cyan-700 hover:bg-cyan-800 font-semibold shadow-xs whitespace-nowrap flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-paper-plane text-[10px]"></i>
                        Mulai Chat
                    </button>
                </form>
            @endif
        </div>

        @if(auth()->user()->role === 'seller')
            {{-- Seller Chat Navigation Tabs --}}
            <div class="flex items-center gap-2 border-b border-slate-200/80 pb-1">
                <a href="{{ route('seller.chat.cus') }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ ($activeTab ?? 'cus') === 'cus' ? 'bg-cyan-700 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    <i class="fa-regular fa-comment-dots text-xs"></i>
                    Chat Pembeli (Customer)
                </a>
                <a href="{{ route('seller.chat.admin') }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ ($activeTab ?? '') === 'admin' ? 'bg-cyan-700 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    <i class="fa-solid fa-headset text-xs"></i>
                    Chat Bantuan Admin
                </a>
            </div>
        @endif

        {{-- Main Two-Pane Chat Container --}}
        <div class="bg-white rounded-2xl shadow-card border border-slate-200/90 overflow-hidden flex flex-col md:flex-row h-[calc(100vh-230px)] min-h-[560px]">
            
            {{-- LEFT PANE: Conversation List --}}
            <div class="w-full md:w-80 lg:w-96 border-r border-slate-200/90 flex flex-col bg-white shrink-0"
                 :class="activeConv ? 'hidden md:flex' : 'flex'">
                
                {{-- Search & Counter Header --}}
                <div class="p-3.5 border-b border-slate-100 bg-slate-50/70 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-inbox text-cyan-700 text-xs"></i>
                            {{ (isset($activeTab) && $activeTab === 'admin') ? 'Admin Official' : 'Kotak Masuk' }}
                        </span>
                        <span class="text-[11px] text-slate-500 font-medium">
                            <strong class="text-cyan-800" x-text="filteredConversations.length"></strong> Obrolan
                        </span>
                    </div>
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text"
                               x-model="searchQuery"
                               placeholder="Cari nama atau pesan..."
                               class="w-full h-8.5 pl-8 pr-3 text-xs bg-white border border-slate-200 rounded-xl focus:border-cyan-600 focus:ring-1 focus:ring-cyan-600 transition-all outline-none">
                    </div>
                </div>

                {{-- Conversations Scroll List --}}
                <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
                    <template x-for="conv in filteredConversations" :key="conv.id">
                        <div @click="selectConversation(conv)"
                             class="p-3.5 flex items-center justify-between cursor-pointer transition-all border-l-4 select-none"
                             :class="activeConv && activeConv.id === conv.id 
                                ? 'bg-cyan-50/80 border-cyan-600' 
                                : 'border-transparent hover:bg-slate-50/80'">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="relative shrink-0">
                                    <img :src="conv.partner.avatar" class="w-10 h-10 rounded-2xl object-cover border border-slate-200 shadow-2xs" alt="User">
                                    <template x-if="conv.unread_count > 0">
                                        <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 text-white text-[9px] font-bold flex items-center justify-center border-2 border-white"
                                              x-text="conv.unread_count"></span>
                                    </template>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <h4 class="font-bold text-slate-900 text-xs truncate" x-text="conv.partner.name"></h4>
                                        <span class="text-[8px] font-bold px-1.5 py-0.2 rounded-full border bg-slate-100 text-slate-600 border-slate-200 capitalize"
                                              x-text="conv.partner.role === 'customer' ? 'Pembeli' : (conv.partner.role === 'seller' ? 'Seller' : 'Admin')"></span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 truncate mt-0.5"
                                       :class="conv.unread_count > 0 ? 'font-bold text-slate-900' : ''"
                                       x-text="conv.last_message || 'Belum ada pesan'"></p>
                                </div>
                            </div>
                            <div class="text-right shrink-0 ml-2">
                                <span class="text-[9px] text-slate-400 font-mono" x-text="conv.time"></span>
                            </div>
                        </div>
                    </template>

                    <template x-if="filteredConversations.length === 0">
                        <div class="p-8 text-center text-slate-400 my-auto">
                            <i class="fa-regular fa-comment-dots text-2xl text-slate-300 mb-2"></i>
                            <p class="text-xs font-semibold text-slate-600">Tidak ada obrolan</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Belum ada riwayat pesan yang sesuai.</p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- RIGHT PANE: Active Chat Room --}}
            <div class="flex-1 flex flex-col min-w-0 bg-slate-50/70"
                 :class="activeConv ? 'flex' : 'hidden md:flex'">
                
                {{-- State 1: A conversation is active --}}
                <template x-if="activeConv">
                    <div class="flex-1 flex flex-col min-h-0 bg-white">
                        
                        {{-- Chat Header --}}
                        <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between bg-white shrink-0 shadow-2xs">
                            <div class="flex items-center gap-3 min-w-0">
                                {{-- Back button for mobile --}}
                                <button type="button" @click="activeConv = null"
                                        class="md:hidden w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xs hover:bg-slate-200">
                                    <i class="fa-solid fa-arrow-left"></i>
                                </button>
                                <div class="relative shrink-0">
                                    <img :src="activeConv.partner.avatar" class="w-9 h-9 rounded-2xl object-cover border border-slate-200 shadow-2xs" alt="Avatar">
                                    <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-500 border-2 border-white"></span>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <h3 class="font-bold text-xs sm:text-sm text-slate-900 truncate" x-text="activeConv.partner.name"></h3>
                                        <span class="text-[8px] font-bold px-1.5 py-0.2 rounded-full border bg-slate-100 text-slate-600 border-slate-200 capitalize"
                                              x-text="activeConv.partner.role === 'customer' ? 'Pembeli' : (activeConv.partner.role === 'seller' ? 'Seller' : 'Admin')"></span>
                                    </div>
                                    <p class="text-[10px] text-emerald-600 font-medium flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                                        Online & Siap merespon
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Chat Messages Area --}}
                        <div id="fullchat-messages-container" class="flex-1 p-4 overflow-y-auto space-y-3 bg-slate-50/70">
                            <template x-if="isLoadingMessages">
                                <div class="py-16 text-center text-slate-400">
                                    <i class="fa-solid fa-spinner animate-spin text-xl text-cyan-600 mb-2"></i>
                                    <p class="text-xs">Memuat pesan...</p>
                                </div>
                            </template>

                            <div class="text-center my-1" x-show="!isLoadingMessages && messages.length > 0">
                                <span class="text-[10px] font-medium text-slate-400 bg-white/90 border border-slate-200/80 px-3 py-0.5 rounded-full shadow-2xs">
                                    <i class="fa-solid fa-lock text-[8px] mr-1 text-slate-400"></i> Percakapan dilindungi enkripsi sistem NitipDong
                                </span>
                            </div>

                            <template x-for="msg in messages" :key="msg.id">
                                <div class="flex flex-col" :class="msg.is_me ? 'items-end' : 'items-start'">
                                    <div class="max-w-[85%] sm:max-w-[70%] px-4 py-2 text-xs sm:text-[13px] leading-relaxed break-words shadow-2xs"
                                         :class="msg.is_me
                                            ? 'bg-cyan-700 text-white rounded-2xl rounded-tr-xs'
                                            : 'bg-white text-slate-800 border border-slate-200/90 rounded-2xl rounded-tl-xs'">
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

                            <template x-if="!isLoadingMessages && messages.length === 0">
                                <div class="py-16 text-center text-slate-400">
                                    <i class="fa-regular fa-comment-dots text-3xl text-slate-300 mb-2"></i>
                                    <p class="text-xs font-semibold text-slate-600">Belum ada pesan</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Kirim pesan pertama Anda untuk memulai percakapan.</p>
                                </div>
                            </template>
                        </div>

                        {{-- Chat Input Bar --}}
                        <div class="p-3 bg-white border-t border-slate-100 shrink-0">
                            <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                                <input type="text"
                                       x-model="newMessageText"
                                       @keydown.enter.prevent="sendMessage()"
                                       :placeholder="'Tulis pesan ke ' + activeConv.partner.name + '...'"
                                       :disabled="isSending"
                                       class="flex-1 h-10 px-3.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-cyan-600 focus:ring-1 focus:ring-cyan-600 transition-all disabled:opacity-50 outline-none">
                                <button type="button"
                                        @click="sendMessage()"
                                        :disabled="isSending || !newMessageText.trim()"
                                        class="h-10 px-4 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-semibold text-xs flex items-center justify-center gap-1.5 transition-all shadow-xs disabled:opacity-50 disabled:cursor-not-allowed shrink-0 cursor-pointer"
                                        title="Kirim Pesan">
                                    <span class="hidden sm:inline">Kirim</span>
                                    <i class="fa-solid fa-paper-plane text-xs" :class="isSending ? 'animate-pulse' : ''"></i>
                                </button>
                            </form>
                        </div>

                    </div>
                </template>

                {{-- State 2: No conversation selected (Desktop Empty State) --}}
                <template x-if="!activeConv">
                    <div class="flex-1 flex flex-col items-center justify-center p-8 text-center my-auto">
                        <div class="w-16 h-16 bg-cyan-50 text-cyan-700 rounded-2xl flex items-center justify-center text-2xl mb-3.5 border border-cyan-200 shadow-2xs">
                            <i class="fa-regular fa-comments"></i>
                        </div>
                        <h3 class="text-slate-900 font-bold text-sm sm:text-base">Pilih Percakapan</h3>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm leading-relaxed">
                            Klik salah satu kontak di sebelah kiri untuk membaca dan membalas pesan langsung tanpa berpindah halaman.
                        </p>
                    </div>
                </template>

            </div>

        </div>

    </div>

    @push('scripts')
    <script>
        function fullChatPage(initialConversations, defaultConvId = null) {
            return {
                conversations: initialConversations || [],
                searchQuery: '',
                activeConv: null,
                messages: [],
                newMessageText: '',
                isLoadingMessages: false,
                isSending: false,
                pollInterval: null,

                init() {
                    if (defaultConvId) {
                        const target = this.conversations.find(c => c.id === defaultConvId);
                        if (target) {
                            this.selectConversation(target);
                            return;
                        }
                    }
                    if (this.conversations.length > 0 && window.innerWidth >= 768) {
                        // On desktop, auto select first conversation
                        this.selectConversation(this.conversations[0]);
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

                getCsrfToken() {
                    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                        || document.querySelector('input[name="_token"]')?.value 
                        || '{{ csrf_token() }}';
                },

                async selectConversation(conv) {
                    this.activeConv = conv;
                    this.messages = [];
                    this.isLoadingMessages = true;
                    if (conv.unread_count > 0) {
                        conv.unread_count = 0;
                    }
                    await this.fetchMessages(conv.id);
                    this.startPolling(conv.id);
                },

                async fetchMessages(convId) {
                    try {
                        const res = await fetch(`/chat/api/${convId}/messages`);
                        if (res.ok) {
                            const data = await res.json();
                            this.messages = data.messages || [];
                            this.$nextTick(() => this.scrollToBottom());
                        }
                    } catch (e) {
                        console.error('Failed to fetch messages:', e);
                    } finally {
                        this.isLoadingMessages = false;
                    }
                },

                startPolling(convId) {
                    if (this.pollInterval) clearInterval(this.pollInterval);
                    this.pollInterval = setInterval(() => {
                        if (this.activeConv && this.activeConv.id === convId) {
                            this.fetchMessages(convId);
                        }
                    }, 3000);
                },

                scrollToBottom() {
                    const el = document.getElementById('fullchat-messages-container');
                    if (el) el.scrollTop = el.scrollHeight;
                },

                async sendMessage() {
                    const text = this.newMessageText.trim();
                    if (!text || !this.activeConv || this.isSending) return;

                    const convId = this.activeConv.id;
                    this.isSending = true;
                    this.newMessageText = '';

                    // Optimistic push
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
                    this.activeConv.last_message = text;
                    this.activeConv.time = 'Baru saja';
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
                            if (window.toast) window.toast.error(data.message || 'Pesan gagal dikirim.');
                        }
                    } catch (e) {
                        console.error('Error sending message:', e);
                        if (window.toast) window.toast.error('Tidak dapat mengirim pesan.');
                    } finally {
                        this.isSending = false;
                    }
                }
            };
        }
    </script>
    @endpush
</x-dynamic-component>