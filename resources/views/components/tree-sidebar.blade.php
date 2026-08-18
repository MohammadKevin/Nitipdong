<aside class="w-64 h-full shrink-0 bg-[#f4f5f6] text-slate-600 flex flex-col justify-between p-4 overflow-y-auto border-r border-slate-200/80 font-sans select-none">
    <div class="space-y-6">
        <div class="flex items-center justify-between px-2 pt-1">
            <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-3 group">
                <div class="w-8 h-8 rounded-xl bg-white shadow-xs border border-slate-200/80 flex items-center justify-center text-slate-800 shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-cube text-slate-900 text-sm"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-sm font-bold text-slate-900 tracking-tight leading-tight">Belanja<span class="text-cyan-700">In</span></h1>
                    <p class="text-[10px] text-slate-600 font-medium leading-none mt-0.5">Seller Center</p>
                </div>
            </a>
            <button type="button" class="text-slate-600 hover:text-slate-800 p-1.5 rounded-lg hover:bg-slate-200/60 transition-colors">
                <i class="fa-solid fa-ellipsis text-xs"></i>
            </button>
        </div>

        <nav class="space-y-1">
            <div>
                <a href="{{ route('seller.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('seller.dashboard') ? 'bg-white text-slate-900 shadow-[0_2px_8px_rgba(0,0,0,0.06)]' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50' }}">
                    <i class="fa-solid fa-house w-4 text-center text-xs text-slate-600"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <div x-data="{ open: {{ request()->routeIs('seller.products.*') ? 'true' : 'true' }} }" class="space-y-1">
                <button type="button"
                        @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-800 hover:bg-slate-200/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-bag-shopping w-4 text-center text-xs text-slate-600"></i>
                        <span>Product</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-[10px] text-slate-600 transition-transform duration-200"
                       :class="{ 'rotate-90 text-slate-900': open }"></i>
                </button>

                <div x-show="open"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="border-l-2 border-slate-200/80 ml-5 pl-3.5 space-y-1 my-1 relative">
                    
                    <a href="{{ route('seller.products.index') }}"
                       class="flex items-center justify-between text-xs transition-all relative before:content-[''] before:absolute before:-left-3.5 before:top-1/2 before:-translate-y-1/2 before:w-2.5 before:h-px before:bg-slate-200 {{ request()->routeIs('seller.products.index') ? 'bg-white shadow-[0_2px_8px_rgba(0,0,0,0.06)] rounded-xl font-semibold text-slate-900 px-3.5 py-2' : 'text-slate-600 hover:text-slate-900 px-3.5 py-1.5 font-medium' }}">
                        <span>Overview</span>
                    </a>

                    <a href="{{ route('seller.products.create') }}"
                       class="flex items-center justify-between text-xs transition-all relative before:content-[''] before:absolute before:-left-3.5 before:top-1/2 before:-translate-y-1/2 before:w-2.5 before:h-px before:bg-slate-200 {{ request()->routeIs('seller.products.create') ? 'bg-white shadow-[0_2px_8px_rgba(0,0,0,0.06)] rounded-xl font-semibold text-slate-900 px-3.5 py-2' : 'text-slate-600 hover:text-slate-900 px-3.5 py-1.5 font-medium' }}">
                        <span>Drafts</span>
                        <span class="ml-auto px-2 py-0.5 text-[11px] font-bold rounded-lg bg-orange-200 text-orange-950">3</span>
                    </a>

                    <a href="{{ route('seller.products.index') }}"
                       class="flex items-center justify-between text-xs transition-all text-slate-600 hover:text-slate-900 px-3.5 py-1.5 font-medium relative before:content-[''] before:absolute before:-left-3.5 before:top-1/2 before:-translate-y-1/2 before:w-2.5 before:h-px before:bg-slate-200">
                        <span>Released</span>
                    </a>

                    <a href="{{ route('chat.index') }}"
                       class="flex items-center justify-between text-xs transition-all text-slate-600 hover:text-slate-900 px-3.5 py-1.5 font-medium relative before:content-[''] before:absolute before:-left-3.5 before:top-1/2 before:-translate-y-1/2 before:w-2.5 before:h-px before:bg-slate-200">
                        <span>Comments</span>
                    </a>

                    <a href="{{ route('seller.products.index') }}"
                       class="flex items-center justify-between text-xs transition-all text-slate-600 hover:text-slate-900 px-3.5 py-1.5 font-medium relative before:content-[''] before:absolute before:-left-3.5 before:top-1/2 before:-translate-y-1/2 before:w-2.5 before:h-px before:bg-slate-200">
                        <span>Scheduled</span>
                        <span class="ml-auto px-2 py-0.5 text-[11px] font-bold rounded-lg bg-emerald-200 text-emerald-950">8</span>
                    </a>
                </div>
            </div>

            <div x-data="{ open: {{ request()->routeIs('seller.orders.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button type="button"
                        @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-800 hover:bg-slate-200/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-user-group w-4 text-center text-xs text-slate-600"></i>
                        <span>Customers</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-[10px] text-slate-600 transition-transform duration-200"
                       :class="{ 'rotate-90 text-slate-900': open }"></i>
                </button>

                <div x-show="open"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="border-l-2 border-slate-200/80 ml-5 pl-3.5 space-y-1 my-1 relative">
                    
                    <a href="{{ route('seller.orders.index') }}"
                       class="flex items-center justify-between text-xs transition-all relative before:content-[''] before:absolute before:-left-3.5 before:top-1/2 before:-translate-y-1/2 before:w-2.5 before:h-px before:bg-slate-200 {{ request()->routeIs('seller.orders.*') ? 'bg-white shadow-[0_2px_8px_rgba(0,0,0,0.06)] rounded-xl font-semibold text-slate-900 px-3.5 py-2' : 'text-slate-600 hover:text-slate-900 px-3.5 py-1.5 font-medium' }}">
                        <span>Pesanan Masuk</span>
                    </a>

                    <a href="{{ route('chat.index') }}"
                       class="flex items-center justify-between text-xs transition-all relative before:content-[''] before:absolute before:-left-3.5 before:top-1/2 before:-translate-y-1/2 before:w-2.5 before:h-px before:bg-slate-200 {{ request()->routeIs('chat.*') ? 'bg-white shadow-[0_2px_8px_rgba(0,0,0,0.06)] rounded-xl font-semibold text-slate-900 px-3.5 py-2' : 'text-slate-600 hover:text-slate-900 px-3.5 py-1.5 font-medium' }}">
                        <span>Pesan Chat</span>
                    </a>
                </div>
            </div>

            <div>
                <a href="/" target="_blank"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-slate-800 hover:text-slate-900 hover:bg-slate-200/50 transition-colors">
                    <i class="fa-solid fa-store w-4 text-center text-xs text-slate-600"></i>
                    <span>Shop</span>
                </a>
            </div>

            <div x-data="{ open: {{ request()->routeIs('seller.vouchers.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button type="button"
                        @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-800 hover:bg-slate-200/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-wallet w-4 text-center text-xs text-slate-600"></i>
                        <span>Income</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-[10px] text-slate-600 transition-transform duration-200"
                       :class="{ 'rotate-90 text-slate-900': open }"></i>
                </button>

                <div x-show="open"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="border-l-2 border-slate-200/80 ml-5 pl-3.5 space-y-1 my-1 relative">
                    
                    <a href="{{ route('seller.vouchers.index') }}"
                       class="flex items-center justify-between text-xs transition-all relative before:content-[''] before:absolute before:-left-3.5 before:top-1/2 before:-translate-y-1/2 before:w-2.5 before:h-px before:bg-slate-200 {{ request()->routeIs('seller.vouchers.*') ? 'bg-white shadow-[0_2px_8px_rgba(0,0,0,0.06)] rounded-xl font-semibold text-slate-900 px-3.5 py-2' : 'text-slate-600 hover:text-slate-900 px-3.5 py-1.5 font-medium' }}">
                        <span>Voucher Toko</span>
                    </a>
                </div>
            </div>

            <div>
                <a href="{{ route('seller.vouchers.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-slate-800 hover:text-slate-900 hover:bg-slate-200/50 transition-colors">
                    <i class="fa-solid fa-bullhorn w-4 text-center text-xs text-slate-600"></i>
                    <span>Promote</span>
                </a>
            </div>
        </nav>
    </div>

    <div class="pt-4 border-t border-slate-200/80">
        <div class="flex items-center gap-3 p-2 rounded-xl bg-white shadow-xs border border-slate-200/70">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0891b2&color=fff&size=50" class="w-8 h-8 rounded-lg object-cover shrink-0" alt="User">
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-slate-900 truncate leading-tight">{{ auth()->user()->name }}</p>
                <p class="text-[10px] text-slate-600 truncate capitalize">{{ auth()->user()->role }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                @csrf
                <button type="submit" class="text-slate-600 hover:text-rose-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors" title="Keluar">
                    <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</aside>
