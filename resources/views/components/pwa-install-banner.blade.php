<div x-data="{
    deferredPrompt: null,
    showBanner: false,
    isIOS: false,
    showIOSGuide: false,
    init() {
        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('PWA Service Worker registered:', reg.scope))
                    .catch(err => console.log('Service Worker registration failed:', err));
            });
        }

        // Check if already installed (standalone mode)
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
        if (isStandalone) return;

        // Check dismissal cooldown (7 days)
        const dismissedAt = localStorage.getItem('nitipdong_pwa_dismissed');
        if (dismissedAt && (Date.now() - parseInt(dismissedAt)) < 7 * 24 * 60 * 60 * 1000) {
            return;
        }

        // Detect iOS
        const userAgent = window.navigator.userAgent.toLowerCase();
        this.isIOS = /iphone|ipad|ipod/.test(userAgent);

        // Catch Android / Desktop Chrome PWA Install Prompt
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.deferredPrompt = e;
            // Delay banner slightly for better UX after page loads
            setTimeout(() => {
                this.showBanner = true;
            }, 3000);
        });

        // Trigger for iOS Safari if visited 2+ times
        if (this.isIOS && !isStandalone) {
            const visits = parseInt(localStorage.getItem('nitipdong_visits') || '0') + 1;
            localStorage.setItem('nitipdong_visits', visits);
            if (visits >= 2) {
                setTimeout(() => {
                    this.showBanner = true;
                }, 4000);
            }
        }
    },
    async installPWA() {
        if (this.deferredPrompt) {
            this.deferredPrompt.prompt();
            const { outcome } = await this.deferredPrompt.userChoice;
            console.log('User response to install prompt:', outcome);
            this.deferredPrompt = null;
            this.showBanner = false;
        } else if (this.isIOS) {
            this.showIOSGuide = true;
        }
    },
    dismissBanner() {
        this.showBanner = false;
        this.showIOSGuide = false;
        localStorage.setItem('nitipdong_pwa_dismissed', Date.now().toString());
    }
}" x-cloak>

    {{-- Bottom Floating Install Banner --}}
    <div x-show="showBanner"
         x-transition:enter="transition ease-out duration-400"
         x-transition:enter-start="opacity-0 translate-y-12 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-12 scale-95"
         class="fixed bottom-4 left-4 right-4 sm:left-auto sm:right-6 sm:max-w-sm z-[9990] bg-slate-900/95 backdrop-blur-md text-white border border-cyan-500/30 rounded-2xl shadow-2xl p-4">
        
        <div class="flex items-start gap-3.5">
            {{-- App Icon --}}
            <div class="w-12 h-12 rounded-xl bg-white p-1.5 border border-cyan-300 shadow-md shrink-0 overflow-hidden">
                <img src="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}" alt="NitipDong Icon" class="w-full h-full object-contain">
            </div>

            {{-- Info & Text --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-black tracking-tight text-white flex items-center gap-1.5">
                        NitipDong App
                        <span class="px-1.5 py-0.5 rounded-full bg-cyan-500/20 text-cyan-300 text-[9px] font-bold border border-cyan-400/30 uppercase">Gratis</span>
                    </h4>
                    <button type="button" @click="dismissBanner()" class="text-slate-400 hover:text-white transition-colors cursor-pointer text-xs p-0.5">
                        ✕
                    </button>
                </div>
                <p class="text-[11px] text-sky-100/80 mt-1 leading-snug">
                    Pasang di Layar Utama HP untuk belanja lebih cepat, hemat kuota &amp; notifikasi pesanan.
                </p>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-2 mt-3">
                    <button type="button" 
                            @click="installPWA()" 
                            class="flex-1 py-1.5 px-3 rounded-lg bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-black text-xs shadow-md transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-download text-[11px]"></i>
                        <span>Install Sekarang</span>
                    </button>
                    <button type="button" 
                            @click="dismissBanner()" 
                            class="py-1.5 px-2.5 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white text-xs font-semibold transition-all cursor-pointer">
                        Nanti
                    </button>
                </div>
            </div>
        </div>

        {{-- iOS Safari Helper Modal/Tooltip --}}
        <div x-show="showIOSGuide" x-cloak class="mt-3 pt-3 border-t border-white/15 text-[11px] text-sky-200">
            <p class="font-bold text-white mb-1">Cara Pasang di iPhone/iPad:</p>
            <ol class="list-decimal list-inside space-y-1 text-slate-300">
                <li>Ketuk tombol <strong class="text-white">Share</strong> (<i class="fa-solid fa-arrow-up-from-bracket text-xs text-cyan-300"></i>) di bawah layar Safari.</li>
                <li>Gulir ke bawah dan pilih <strong class="text-white">"Add to Home Screen"</strong> (<i class="fa-regular fa-square-plus text-xs text-cyan-300"></i>).</li>
                <li>Ketuk <strong class="text-white">"Add"</strong> di pojok kanan atas.</li>
            </ol>
        </div>
    </div>

</div>
