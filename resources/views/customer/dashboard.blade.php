<x-app-layout>
<div class="min-h-screen" style="background:var(--bg);">
    <div class="max-w-[1280px] mx-auto px-4 md:px-6 py-8">

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="mb-6 flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-medium text-emerald-700 bg-emerald-50 border border-emerald-100">
            <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mb-6 flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-medium text-rose-700 bg-rose-50 border border-rose-100">
            <div class="w-6 h-6 rounded-full bg-rose-100 flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            {{ session('error') }}
        </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-6">

            {{-- ━━━━━━━━━━━━━━ SIDEBAR ━━━━━━━━━━━━━━ --}}
            <aside class="w-full lg:w-64 xl:w-72 shrink-0 space-y-4">

                {{-- Profile Card --}}
                <div class="card p-5">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="relative shrink-0">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0ea5e9&color=fff&size=100"
                                class="w-14 h-14 rounded-2xl ring-2 ring-cyan-100" alt="{{ Auth::user()->name }}">
                            <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-400 rounded-full border-2 border-white"></span>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold text-slate-900 truncate" style="font-family:'Outfit',sans-serif;">{{ Auth::user()->name }}</h3>
                            <p class="text-xs text-slate-400 truncate mt-0.5">{{ Auth::user()->email }}</p>
                            <span class="inline-flex items-center gap-1 mt-1.5 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-cyan-50 text-cyan-600">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                Member BelanjaIn
                            </span>
                        </div>
                    </div>

                    {{-- Quick Stats --}}
                    <div class="grid grid-cols-2 gap-2 pt-4 border-t border-slate-50">
                        <div class="text-center py-2 rounded-xl bg-slate-50">
                            <p class="font-bold text-slate-900 text-lg" style="font-family:'Outfit',sans-serif;">{{ $orders->count() }}</p>
                            <p class="text-xs text-slate-400">Total Pesanan</p>
                        </div>
                        <div class="text-center py-2 rounded-xl bg-cyan-50">
                            <p class="font-bold text-cyan-600 text-lg" style="font-family:'Outfit',sans-serif;">{{ $orders->where('status','completed')->count() }}</p>
                            <p class="text-xs text-slate-400">Selesai</p>
                        </div>
                    </div>
                </div>

                {{-- Navigation Menu --}}
                <div class="card overflow-hidden">
                    <div class="px-4 pt-4 pb-2">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Menu Utama</p>
                    </div>
                    <nav class="pb-2">
                        <a href="{{ route('customer.dashboard') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium mx-2 rounded-xl transition-all
                            {{ request()->routeIs('customer.dashboard') ? 'bg-cyan-50 text-cyan-600' : 'text-slate-600 hover:bg-slate-50 hover:text-cyan-600' }}">
                            <svg class="w-4.5 h-4.5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Pesanan Saya
                            @php $pendingCount = $orders->where('status','pending')->count(); @endphp
                            @if($pendingCount > 0)
                                <span class="ml-auto text-xs font-bold px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-700">{{ $pendingCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('customer.cart.index') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium mx-2 rounded-xl transition-all text-slate-600 hover:bg-slate-50 hover:text-cyan-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Keranjang Saya
                        </a>
                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium mx-2 rounded-xl transition-all
                            {{ request()->routeIs('profile.*') ? 'bg-cyan-50 text-cyan-600' : 'text-slate-600 hover:bg-slate-50 hover:text-cyan-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profil Saya
                        </a>
                    </nav>

                    <div class="px-4 pt-3 pb-2 border-t border-slate-50">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Komunikasi</p>
                    </div>
                    <nav class="pb-3">
                        <a href="{{ route('chat.index') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium mx-2 rounded-xl transition-all
                            {{ request()->routeIs('chat.*') ? 'bg-cyan-50 text-cyan-600' : 'text-slate-600 hover:bg-slate-50 hover:text-cyan-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                            Chat & Pesan
                        </a>
                    </nav>
                </div>

                {{-- Banner Buka Toko --}}
                @if(!$userStore)
                <div class="card p-5" style="background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%);">
                    <div class="w-10 h-10 bg-white/20 rounded-2xl flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h4 class="font-bold text-white mb-1" style="font-family:'Outfit',sans-serif;">Jadi Penjual?</h4>
                    <p class="text-white/70 text-xs mb-4 leading-relaxed">Buka tokomu dan mulai berjualan di BelanjaIn. Gratis!</p>
                    <a href="{{ route('customer.store.register') }}"
                        class="block text-center bg-white text-cyan-600 text-sm font-bold py-2 rounded-xl hover:bg-cyan-50 transition-colors" style="font-family:'Outfit',sans-serif;">
                        Buka Toko →
                    </a>
                </div>
                @elseif($userStore->status === 'pending')
                <div class="card p-4 border border-amber-100">
                    <div class="flex gap-3">
                        <div class="w-9 h-9 bg-amber-50 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-4.5 h-4.5 w-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">Toko Dalam Review</p>
                            <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ $userStore->name }} sedang ditinjau oleh tim kami.</p>
                        </div>
                    </div>
                </div>
                @endif

            </aside>

            {{-- ━━━━━━━━━━━━━━ MAIN CONTENT ━━━━━━━━━━━━━━ --}}
            <div class="flex-1 min-w-0 space-y-5">

                {{-- Status Cards --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @php
                        $statusData = [
                            ['label' => 'Menunggu Bayar', 'count' => $orders->where('status','pending')->count(), 'color' => 'amber', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ['label' => 'Dikemas', 'count' => $orders->where('status','processing')->count(), 'color' => 'blue', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                            ['label' => 'Dikirim', 'count' => $orders->where('status','shipped')->count(), 'color' => 'indigo', 'icon' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0'],
                            ['label' => 'Selesai', 'count' => $orders->where('status','completed')->count(), 'color' => 'emerald', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ];
                    @endphp
                    @foreach($statusData as $s)
                        <div class="card card-hover p-4 flex flex-col gap-3 cursor-pointer">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-{{ $s['color'] }}-50">
                                <svg class="w-5 h-5 text-{{ $s['color'] }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $s['icon'] }}"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-2xl text-slate-900" style="font-family:'Outfit',sans-serif;">{{ $s['count'] }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $s['label'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Orders List --}}
                <div class="card overflow-hidden">
                    {{-- Header --}}
                    <div class="px-6 py-4 border-b border-slate-50 flex items-center justify-between">
                        <h2 class="font-bold text-slate-900" style="font-family:'Outfit',sans-serif;">Riwayat Pesanan</h2>
                        <div class="relative">
                            <input type="text" placeholder="Cari pesanan..."
                                class="pl-8 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:border-cyan-300 focus:ring-2 focus:ring-cyan-100 outline-none w-52 transition-all">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>

                    {{-- Status Filter Tabs --}}
                    <div class="px-6 pt-4 flex gap-2 flex-wrap">
                        @php
                            $tabs = ['Semua' => null, 'Menunggu Bayar' => 'pending', 'Dikemas' => 'processing', 'Dikirim' => 'shipped', 'Selesai' => 'completed'];
                            $currentStatus = request('status');
                        @endphp
                        @foreach($tabs as $label => $value)
                            <a href="{{ route('customer.dashboard', $value ? ['status' => $value] : []) }}"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all
                                {{ $currentStatus === $value
                                    ? 'text-white shadow-sm shadow-sky-200'
                                    : 'bg-slate-100 text-slate-500 hover:bg-cyan-50 hover:text-cyan-600' }}"
                                @if($currentStatus === $value)
                                    style="background:var(--brand); font-family:'Outfit',sans-serif;"
                                @else
                                    style="font-family:'Outfit',sans-serif;"
                                @endif>
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    {{-- Orders --}}
                    <div class="p-4 space-y-3 mt-2">
                        @forelse ($orders as $order)
                            <div class="border border-slate-100 rounded-2xl overflow-hidden hover:border-cyan-200 hover:shadow-md transition-all duration-200">

                                {{-- Order Header --}}
                                <div class="flex items-center justify-between px-5 py-3 bg-slate-50/60">
                                    <div class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-lg bg-white border border-slate-200 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800" style="font-family:'Outfit',sans-serif;">{{ $order->store->name ?? 'Toko' }}</p>
                                            <p class="text-xs text-slate-400">{{ $order->invoice_number }} · {{ $order->created_at->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                    @php
                                        $statusMap = [
                                            'pending' => ['text' => 'Menunggu Pembayaran', 'class' => 'bg-amber-50 text-amber-600 border-amber-100'],
                                            'processing' => ['text' => 'Sedang Dikemas', 'class' => 'bg-blue-50 text-blue-600 border-blue-100'],
                                            'shipped' => ['text' => 'Sedang Dikirim', 'class' => 'bg-indigo-50 text-indigo-600 border-indigo-100'],
                                            'completed' => ['text' => 'Selesai', 'class' => 'bg-emerald-50 text-emerald-600 border-emerald-100'],
                                            'cancelled' => ['text' => 'Dibatalkan', 'class' => 'bg-slate-100 text-slate-500 border-slate-200'],
                                        ];
                                        $st = $statusMap[$order->status] ?? $statusMap['cancelled'];
                                    @endphp
                                    <span class="text-xs font-semibold px-3 py-1 rounded-full border {{ $st['class'] }}" style="font-family:'Outfit',sans-serif;">
                                        {{ $st['text'] }}
                                    </span>
                                </div>

                                {{-- Items --}}
                                <div class="px-5 py-4 space-y-3">
                                    @foreach($order->orderItems->take(2) as $item)
                                        <div class="flex items-center gap-4">
                                            <div class="w-14 h-14 rounded-xl overflow-hidden bg-slate-100 shrink-0 border border-slate-100">
                                                @if($item->product->image)
                                                    <img src="{{ asset('storage/' . $item->product->image) }}" class="w-full h-full object-cover" alt="{{ $item->product->name }}">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-slate-800 line-clamp-1">{{ $item->product->name }}</p>
                                                <p class="text-xs text-slate-400 mt-0.5">{{ $item->quantity }} pcs × Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                                            </div>
                                            <p class="text-sm font-semibold text-slate-700 shrink-0">
                                                Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    @endforeach
                                    @if($order->orderItems->count() > 2)
                                        <p class="text-xs text-slate-400 pl-18">+{{ $order->orderItems->count() - 2 }} produk lainnya</p>
                                    @endif
                                </div>

                                {{-- Footer --}}
                                <div class="px-5 py-3.5 bg-slate-50/40 border-t border-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-xs text-slate-500">Total Pesanan</span>
                                        <span class="font-bold text-lg text-slate-900" style="font-family:'Outfit',sans-serif;">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        @if($order->status === 'pending' && !$order->payment_proof)
                                            <a href="{{ route('customer.order.payment', $order) }}" class="btn-primary text-sm py-2 px-5">
                                                Bayar Sekarang
                                            </a>
                                        @elseif($order->status === 'pending' && $order->payment_proof)
                                            <span class="text-xs text-amber-600 bg-amber-50 border border-amber-100 px-3 py-1.5 rounded-xl font-medium">
                                                Menunggu Konfirmasi Penjual
                                            </span>
                                        @endif
                                        @if($order->status === 'completed')
                                            <button class="btn-outline text-sm py-2 px-4">Beli Lagi</button>
                                            <button class="btn-primary text-sm py-2 px-4">Beri Ulasan</button>
                                        @endif
                                        @if(!in_array($order->status, ['pending']))
                                            <a href="#" class="text-sm text-slate-500 hover:text-cyan-600 border border-slate-200 hover:border-cyan-200 px-4 py-2 rounded-xl transition-all font-medium">
                                                Detail
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-16 text-center">
                                <div class="w-20 h-20 rounded-3xl bg-cyan-50 flex items-center justify-center mx-auto mb-5">
                                    <svg class="w-9 h-9 text-sky-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                </div>
                                <h3 class="font-bold text-slate-700 text-lg mb-1" style="font-family:'Outfit',sans-serif;">Belum ada pesanan</h3>
                                <p class="text-slate-400 text-sm mb-6">Temukan produk favoritmu dan mulai berbelanja!</p>
                                <a href="/" class="btn-primary">
                                    Mulai Belanja
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>{{-- end main --}}
        </div>{{-- end flex --}}
    </div>{{-- end container --}}
</div>
</x-app-layout>
