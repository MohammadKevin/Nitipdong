{{-- Sonner Toast Notification System (Top Right Positioned) --}}
<div x-data="sonnerNotificationComponent()"
     x-init="initSonner()"
     @notify.window="addToast($event.detail.title || defaultTitle($event.detail.type), $event.detail.message || $event.detail, $event.detail.type || 'success', $event.detail.duration || 4500, $event.detail.action || null)"
     @toast.window="addToast($event.detail.title || defaultTitle($event.detail.type), $event.detail.message || $event.detail, $event.detail.type || 'success', $event.detail.duration || 4500, $event.detail.action || null)"
     class="fixed top-4 right-4 sm:top-5 sm:right-5 z-[999999] flex flex-col gap-2.5 max-w-[400px] w-[calc(100vw-2rem)] sm:w-full pointer-events-none"
     aria-live="polite">

    <template x-for="t in toasts" :key="t.id">
        <div x-show="t.visible"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 -translate-y-3 translate-x-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 translate-x-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-x-0 scale-100"
             x-transition:leave-end="opacity-0 translate-x-8 scale-95"
             @mouseenter="pauseToast(t.id)"
             @mouseleave="resumeToast(t.id)"
             class="pointer-events-auto p-4 rounded-2xl backdrop-blur-xl border flex flex-col gap-2 shadow-2xl transition-all relative overflow-hidden group select-none text-xs"
             :class="{
                'bg-slate-900/95 text-white border-emerald-500/40 shadow-emerald-950/30': t.type === 'success',
                'bg-slate-900/95 text-white border-rose-500/40 shadow-rose-950/30': t.type === 'error',
                'bg-slate-900/95 text-white border-amber-500/40 shadow-amber-950/30': t.type === 'warning',
                'bg-slate-900/95 text-white border-cyan-500/40 shadow-cyan-950/30': t.type === 'info'
             }">

            {{-- Ambient Accent Glow --}}
            <div class="absolute -top-10 -left-10 w-24 h-24 rounded-full blur-2xl pointer-events-none opacity-20"
                 :class="{
                    'bg-emerald-400': t.type === 'success',
                    'bg-rose-500': t.type === 'error',
                    'bg-amber-400': t.type === 'warning',
                    'bg-cyan-400': t.type === 'info'
                 }"></div>

            {{-- Main Toast Body --}}
            <div class="flex items-start gap-3 relative z-10">
                {{-- Type Icon Badge --}}
                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 border"
                     :class="{
                        'bg-emerald-500/15 border-emerald-400/30 text-emerald-400': t.type === 'success',
                        'bg-rose-500/15 border-rose-400/30 text-rose-400': t.type === 'error',
                        'bg-amber-500/15 border-amber-400/30 text-amber-400': t.type === 'warning',
                        'bg-cyan-500/15 border-cyan-400/30 text-cyan-400': t.type === 'info'
                     }">
                    <template x-if="t.type === 'success'">
                        <i class="fa-solid fa-circle-check text-sm"></i>
                    </template>
                    <template x-if="t.type === 'error'">
                        <i class="fa-solid fa-circle-xmark text-sm"></i>
                    </template>
                    <template x-if="t.type === 'warning'">
                        <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                    </template>
                    <template x-if="t.type === 'info'">
                        <i class="fa-solid fa-circle-info text-sm"></i>
                    </template>
                </div>

                {{-- Text Content --}}
                <div class="flex-1 min-w-0 pr-1 pt-0.5">
                    <div class="flex items-center justify-between gap-2">
                        <h4 class="font-bold text-xs tracking-tight text-white" x-text="t.title"></h4>
                        <span class="text-[9px] text-slate-500 font-mono tracking-tighter" x-text="t.timestamp"></span>
                    </div>
                    <p class="text-[11px] text-slate-300 mt-0.5 leading-relaxed break-words" x-html="t.message"></p>

                    {{-- Action CTA Button (Optional) --}}
                    <template x-if="t.action">
                        <div class="mt-2.5 flex items-center gap-2">
                            <a :href="t.action.url || '#'"
                               @click="if(t.action.onClick) { t.action.onClick(); removeToast(t.id); }"
                               class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-bold transition-all shadow-xs"
                               :class="{
                                   'bg-emerald-500 hover:bg-emerald-400 text-slate-950': t.type === 'success',
                                   'bg-rose-500 hover:bg-rose-400 text-white': t.type === 'error',
                                   'bg-amber-500 hover:bg-amber-400 text-slate-950': t.type === 'warning',
                                   'bg-cyan-500 hover:bg-cyan-400 text-slate-950': t.type === 'info'
                               }">
                                <span x-text="t.action.label || 'Lihat'"></span>
                                <i class="fa-solid fa-arrow-right text-[8px]"></i>
                            </a>
                        </div>
                    </template>
                </div>

                {{-- Close Button --}}
                <button type="button"
                        @click="removeToast(t.id)"
                        class="w-6 h-6 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 flex items-center justify-center transition-colors cursor-pointer shrink-0"
                        title="Tutup">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            {{-- Countdown Progress Bar --}}
            <div class="w-full bg-slate-800/80 h-0.5 rounded-full overflow-hidden mt-0.5">
                <div class="h-full transition-all duration-100 ease-linear rounded-full"
                     :style="`width: ${t.progress}%;`"
                     :class="{
                        'bg-emerald-400': t.type === 'success',
                        'bg-rose-400': t.type === 'error',
                        'bg-amber-400': t.type === 'warning',
                        'bg-cyan-400': t.type === 'info'
                     }"></div>
            </div>
        </div>
    </template>
</div>

@pushOnce('scripts')
<script>
    function sonnerNotificationComponent() {
        return {
            toasts: [],
            defaultTitle(type) {
                switch(type) {
                    case 'success': return 'Berhasil';
                    case 'error': return 'Terjadi Kesalahan';
                    case 'warning': return 'Pemberitahuan';
                    case 'info': default: return 'Informasi';
                }
            },
            initSonner() {
                // Register global window helper
                window.toast = {
                    success: (msg, title = 'Berhasil', options = {}) => {
                        this.addToast(title, msg, 'success', options.duration, options.action);
                    },
                    error: (msg, title = 'Terjadi Kesalahan', options = {}) => {
                        this.addToast(title, msg, 'error', options.duration, options.action);
                    },
                    warning: (msg, title = 'Pemberitahuan', options = {}) => {
                        this.addToast(title, msg, 'warning', options.duration, options.action);
                    },
                    info: (msg, title = 'Informasi', options = {}) => {
                        this.addToast(title, msg, 'info', options.duration, options.action);
                    },
                    dismiss: (id) => {
                        this.removeToast(id);
                    }
                };
                window.sonner = window.toast;

                // Auto-read Laravel session flashes
                @if(session('success'))
                    this.addToast('Berhasil', '{{ addslashes(session('success')) }}', 'success', 4500);
                @endif
                @if(session('error'))
                    this.addToast('Terjadi Kesalahan', '{{ addslashes(session('error')) }}', 'error', 5000);
                @endif
                @if(session('warning'))
                    this.addToast('Perhatian', '{{ addslashes(session('warning')) }}', 'warning', 5000);
                @endif
                @if(session('info'))
                    this.addToast('Informasi', '{{ addslashes(session('info')) }}', 'info', 4500);
                @endif
                @if(session('status'))
                    this.addToast('Status', '{{ addslashes(session('status')) }}', 'info', 4500);
                @endif
                @if(isset($errors) && $errors->any())
                    @php
                        $errorMessages = addslashes(implode('<br>• ', $errors->all()));
                    @endphp
                    this.addToast('Validasi Gagal', '• {!! $errorMessages !!}', 'error', 6000);
                @endif
            },
            addToast(title, message, type = 'success', duration = 4500, action = null) {
                if (!message) return;
                const id = 'sonner_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5);
                const now = new Date();
                const timestamp = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

                const toastItem = {
                    id,
                    title: title || this.defaultTitle(type),
                    message: String(message),
                    type,
                    duration,
                    remaining: duration,
                    progress: 100,
                    action,
                    timestamp,
                    visible: true,
                    isPaused: false,
                    intervalId: null
                };

                // Limit maximum active toasts to 4
                if (this.toasts.length >= 4) {
                    this.removeToast(this.toasts[0].id);
                }

                this.toasts.push(toastItem);

                // Start progress timer
                const step = 50; // update every 50ms
                toastItem.intervalId = setInterval(() => {
                    if (!toastItem.isPaused) {
                        toastItem.remaining -= step;
                        toastItem.progress = Math.max(0, (toastItem.remaining / toastItem.duration) * 100);

                        if (toastItem.remaining <= 0) {
                            clearInterval(toastItem.intervalId);
                            this.removeToast(id);
                        }
                    }
                }, step);
            },
            pauseToast(id) {
                const t = this.toasts.find(item => item.id === id);
                if (t) t.isPaused = true;
            },
            resumeToast(id) {
                const t = this.toasts.find(item => item.id === id);
                if (t) t.isPaused = false;
            },
            removeToast(id) {
                const index = this.toasts.findIndex(t => t.id === id);
                if (index !== -1) {
                    const t = this.toasts[index];
                    if (t.intervalId) clearInterval(t.intervalId);
                    t.visible = false;
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(item => item.id !== id);
                    }, 250);
                }
            }
        };
    }
</script>
@endPushOnce
