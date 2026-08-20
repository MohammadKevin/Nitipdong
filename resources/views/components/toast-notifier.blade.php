<div x-data="{
        toasts: [],
        addToast(title, message, type = 'success') {
            const id = Date.now();
            this.toasts.push({ id, title, message, type });
            setTimeout(() => this.removeToast(id), 5000);
        },
        removeToast(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
     }"
     x-init="
        @if(session('success'))
            addToast('Berhasil', '{{ addslashes(session('success')) }}', 'success');
        @endif
        @if(session('error'))
            addToast('Perhatian', '{{ addslashes(session('error')) }}', 'error');
        @endif
        @if(session('info'))
            addToast('Informasi', '{{ addslashes(session('info')) }}', 'info');
        @endif
     "
     @notify.window="addToast($event.detail.title, $event.detail.message, $event.detail.type || 'success')"
     class="fixed bottom-5 right-5 z-50 flex flex-col gap-2.5 max-w-sm w-full pointer-events-none px-3 sm:px-0">
    <template x-for="t in toasts" :key="t.id">
        <div x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="pointer-events-auto p-4 rounded-2xl shadow-xl border flex items-start gap-3 backdrop-blur-md transition-all text-xs"
             :class="{
                'bg-emerald-950/95 text-white border-emerald-500/40 shadow-emerald-950/20': t.type === 'success',
                'bg-rose-950/95 text-white border-rose-500/40 shadow-rose-950/20': t.type === 'error',
                'bg-slate-950/95 text-white border-cyan-500/40 shadow-cyan-950/20': t.type === 'info'
             }">
            
            {{-- Icon --}}
            <div class="w-7 h-7 rounded-xl flex items-center justify-center shrink-0"
                 :class="{
                    'bg-emerald-500/20 text-emerald-400': t.type === 'success',
                    'bg-rose-500/20 text-rose-400': t.type === 'error',
                    'bg-cyan-500/20 text-cyan-400': t.type === 'info'
                 }">
                <i :class="{
                    'fa-solid fa-circle-check text-sm': t.type === 'success',
                    'fa-solid fa-circle-exclamation text-sm': t.type === 'error',
                    'fa-solid fa-bell text-sm': t.type === 'info'
                }"></i>
            </div>

            <div class="flex-1 min-w-0">
                <h4 class="font-bold text-xs" x-text="t.title"></h4>
                <p class="text-[11px] text-slate-300 mt-0.5 leading-relaxed break-words" x-text="t.message"></p>
            </div>

            <button type="button" @click="removeToast(t.id)" class="text-slate-400 hover:text-white transition-colors cursor-pointer shrink-0">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>
    </template>
</div>
