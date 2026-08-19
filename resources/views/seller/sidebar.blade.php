@php
    $store = $store ?? Auth::user()->store;
    $totalProductCount = $store ? \App\Models\Product::where('store_id', $store->id)->count() : 0;
    $totalOrderCount = $store ? \App\Models\Order::where('store_id', $store->id)->count() : 0;
    $pendingOrderCount = $store ? \App\Models\Order::where('store_id', $store->id)->where('status', 'pending')->count() : 0;
    $voucherCount = $store ? \App\Models\Voucher::where('store_id', $store->id)->count() : 0;
@endphp

<aside class="w-full lg:w-64 xl:w-72 shrink-0 space-y-4">

    <div class="card p-5">
        <div class="flex items-center gap-4 mb-5">
            <div class="relative shrink-0">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($store->name ?? Auth::user()->name) }}&background=0284c7&color=fff&size=100"
                     class="w-14 h-14 rounded-2xl ring-2 ring-brand-100 object-cover" alt="{{ $store->name ?? 'Toko' }}">
                <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-400 rounded-full border-2 border-white animate-pulse-dot"></span>
            </div>
            <div class="min-w-0">
                <h3 class="font-bold text-slate-900 truncate font-display">{{ $store->name ?? 'Toko Saya' }}</h3>
                <p class="text-xs text-slate-400 truncate mt-0.5">{{ Auth::user()->name }}</p>
                <span class="badge-brand mt-1.5 inline-flex items-center gap-1">
                    <i class="fa-solid fa-shield-check text-brand-500 text-xs"></i> Official Seller
                </span>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-2 pt-4 border-t border-slate-50">
            <div class="text-center py-2.5 rounded-xl bg-slate-50">
                <p class="font-bold text-slate-900 text-lg font-display">{{ $totalProductCount }}</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Produk</p>
            </div>
            <div class="text-center py-2.5 rounded-xl bg-slate-50">
                <p class="font-bold text-slate-900 text-lg font-display">{{ $totalOrderCount }}</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Pesanan</p>
            </div>
            <div class="text-center py-2.5 rounded-xl bg-slate-50">
                <p class="font-bold text-amber-500 text-lg font-display flex items-center justify-center gap-1">
                    <i class="fa-solid fa-star text-xs"></i> 5.0
                </p>
                <p class="text-[10px] text-slate-400 mt-0.5">Rating</p>
            </div>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="px-4 pt-4 pb-2">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Menu Toko</p>
        </div>
        <nav class="pb-2 space-y-1">
            @php
            $sellerNav = [
                [
                    'route'  => 'seller.dashboard',
                    'label'  => 'Dashboard Toko',
                    'icon'   => 'fa-solid fa-chart-pie',
                    'active' => request()->routeIs('seller.dashboard'),
                ],
                [
                    'route'  => 'seller.products.index',
                    'label'  => 'Katalog Produk',
                    'icon'   => 'fa-solid fa-boxes-stacked',
                    'active' => request()->routeIs('seller.products.*') && !request()->routeIs('seller.products.create'),
                ],
                [
                    'route'  => 'seller.products.create',
                    'label'  => 'Tambah Produk',
                    'icon'   => 'fa-solid fa-plus-circle',
                    'active' => request()->routeIs('seller.products.create'),
                ],
                [
                    'route'  => 'seller.orders.index',
                    'label'  => 'Pesanan Masuk',
                    'icon'   => 'fa-solid fa-clipboard-list',
                    'active' => request()->routeIs('seller.orders.*'),
                    'badge'  => $pendingOrderCount > 0 ? $pendingOrderCount : null,
                ],
                [
                    'route'  => 'seller.vouchers.index',
                    'label'  => 'Voucher Toko',
                    'icon'   => 'fa-solid fa-ticket',
                    'active' => request()->routeIs('seller.vouchers.*'),
                    'badge'  => $voucherCount > 0 ? $voucherCount : null,
                ],
                [
                    'route'  => 'seller.reviews.index',
                    'label'  => 'Ulasan Pembeli',
                    'icon'   => 'fa-solid fa-star',
                    'active' => request()->routeIs('seller.reviews.*'),
                ],
            ];
            @endphp

            @foreach($sellerNav as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium mx-2 rounded-xl transition-all
                          {{ $item['active'] ? 'bg-brand-50 text-brand-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-brand-600' }}">
                    <i class="{{ $item['icon'] }} w-4 text-center text-sm {{ $item['active'] ? 'text-brand-600' : 'text-slate-400' }}"></i>
                    <span>{{ $item['label'] }}</span>
                    @if(!empty($item['badge']))
                        <span class="ml-auto badge-brand text-[10px]">{{ $item['badge'] }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="px-4 pt-3 pb-2 border-t border-slate-50">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Komunikasi & Akun</p>
        </div>
        <nav class="pb-2 space-y-1">
            <a href="{{ route('chat.index') }}"
               class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium mx-2 rounded-xl transition-all
                      {{ request()->routeIs('chat.*') ? 'bg-brand-50 text-brand-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-brand-600' }}">
                <i class="fa-solid fa-comments w-4 text-center text-sm {{ request()->routeIs('chat.*') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                <span>Chat & Pesan</span>
            </a>
            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium mx-2 rounded-xl transition-all
                      {{ request()->routeIs('profile.edit') ? 'bg-brand-50 text-brand-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-brand-600' }}">
                <i class="fa-solid fa-user-gear w-4 text-center text-sm {{ request()->routeIs('profile.edit') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                <span>Pengaturan Profil</span>
            </a>
        </nav>

        <div class="border-t border-slate-50 py-1.5 px-2">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-rose-500 hover:bg-rose-50 hover:text-rose-600 rounded-xl transition-colors">
                    <i class="fa-solid fa-arrow-right-from-bracket w-4 text-center text-sm"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </div>

    <div class="card p-5 bg-gradient-to-br from-slate-900 to-slate-800 text-white relative overflow-hidden">
        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/5 rounded-full"></div>
        <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-white text-lg mb-3">
            <i class="fa-solid fa-bag-shopping"></i>
        </div>
        <h4 class="font-bold text-white mb-1 font-display">Mode Pembeli</h4>
        <p class="text-slate-300 text-xs mb-3 leading-relaxed">Jelajahi marketplace dan cari produk pilihan terbaik.</p>
        <a href="{{ url('/products') }}"
           class="block text-center bg-white/10 hover:bg-white/20 text-white text-xs font-semibold py-2 rounded-xl transition-colors">
            Ke Marketplace →
        </a>
    </div>

</aside>
