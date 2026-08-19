<x-app-layout>
    <div class="page-container py-5">
        <div class="mb-4">
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Akun & Riwayat Belanja</h1>
            <p class="text-xs text-slate-400 mt-0.5">Kelola data profil, alamat, dan status pemesanan Anda di BelanjaIn</p>
        </div>

        @if(session('success'))
            <div class="mb-4 flex items-center gap-2.5 px-4 py-3 bg-cyan-50 border border-cyan-200 text-cyan-900 rounded-lg text-xs font-semibold animate-fade-up">
                <i class="fa-solid fa-circle-check text-cyan-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-5 items-start">
            <aside class="w-full lg:w-64 shrink-0 space-y-4">
                <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-card text-center">
                    <div class="relative w-16 h-16 mx-auto mb-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0891b2&color=fff&size=100"
                             class="w-full h-full rounded-full border-2 border-cyan-200 object-cover" alt="User">
                    </div>
                    <h3 class="font-bold text-sm text-slate-900 leading-tight">{{ auth()->user()->name }}</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5 truncate">{{ auth()->user()->email }}</p>
                    <div class="mt-3 inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-cyan-50 text-cyan-800 text-[10px] font-semibold border border-cyan-200">
                        <i class="fa-solid fa-user-check text-cyan-600 text-[9px]"></i> Pembeli Terverifikasi
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200/80 p-3.5 shadow-card space-y-1 text-xs">
                    <a href="{{ route('customer.dashboard') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-semibold text-cyan-800 bg-cyan-50 transition-colors">
                        <i class="fa-solid fa-receipt text-cyan-700 w-4"></i>
                        Riwayat Belanja
                    </a>
                    @if(!$userStore)
                        <a href="{{ route('store.register') }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-slate-700 hover:bg-slate-50 hover:text-cyan-700 transition-colors">
                            <i class="fa-solid fa-store text-cyan-700 w-4"></i>
                            Buka Toko Gratis
                        </a>
                    @elseif($userStore->status === 'approved')
                        <a href="{{ route('seller.dashboard') }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-slate-700 hover:bg-slate-50 hover:text-cyan-700 transition-colors">
                            <i class="fa-solid fa-store text-cyan-700 w-4"></i>
                            Seller Center Toko
                        </a>
                    @else
                        <div class="px-3 py-2 rounded-lg bg-amber-50 text-amber-800 text-[11px] font-medium border border-amber-200">
                            Status Toko: <span class="font-bold uppercase">{{ $userStore->status }}</span>
                        </div>
                    @endif
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-slate-700 hover:bg-slate-50 hover:text-cyan-700 transition-colors">
                        <i class="fa-solid fa-user-gear text-slate-400 w-4"></i>
                        Pengaturan Profil
                    </a>
                </div>
            </aside>

            <div class="flex-1 min-w-0 space-y-4">
                <div class="bg-white rounded-xl border border-slate-200/80 p-5 sm:p-6 shadow-card">
                    <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4">
                        <h2 class="font-bold text-xs uppercase tracking-wider text-slate-900">Riwayat Pesanan Belanja</h2>
                        <span class="text-xs text-slate-400">Total {{ $orders->count() }} transaksi</span>
                    </div>

                    @if($orders->count() > 0)
                        <div class="space-y-4">
                            @foreach($orders as $order)
                            <div class="border border-slate-200 rounded-lg overflow-hidden shadow-xs">
                                <div class="px-4 py-2.5 bg-slate-50/80 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-receipt text-slate-400 text-xs"></i>
                                        <span class="font-mono font-bold text-slate-800">#{{ $order->invoice_number }}</span>
                                        <span class="text-slate-400 text-[11px]">• {{ $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                                    </div>
                                    @php
                                        $statusBadge = [
                                            'pending'    => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'processing' => 'bg-cyan-50 text-cyan-800 border-cyan-200',
                                            'shipped'    => 'bg-purple-50 text-purple-700 border-purple-200',
                                            'completed'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'cancelled'  => 'bg-rose-50 text-rose-700 border-rose-200',
                                        ];
                                    @endphp
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border w-fit {{ $statusBadge[$order->status] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>

                                <div class="p-4 space-y-3">
                                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-700 pb-2.5 border-b border-slate-100">
                                        <i class="fa-solid fa-store text-cyan-700 text-xs"></i>
                                        <span>{{ $order->store->name ?? 'Official Store BelanjaIn' }}</span>
                                    </div>

                                    @foreach($order->orderItems as $item)
                                    <div class="flex items-center gap-3.5">
                                        <div class="w-12 h-12 rounded-md bg-slate-100 border border-slate-200 overflow-hidden shrink-0">
                                            @if($item->product && $item->product->image)
                                                <img src="{{ $item->product->image_url }}" class="w-full h-full object-cover" alt="Product">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-300 text-base">
                                                    <i class="fa-solid fa-box"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs sm:text-sm font-medium text-slate-800 line-clamp-1">
                                                {{ $item->product ? $item->product->name : 'Produk Tidak Tersedia' }}
                                            </p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">
                                                {{ $item->quantity }} unit x Rp {{ number_format($item->price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                        <div class="text-right font-bold text-xs sm:text-sm text-slate-900">
                                            Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2.5 text-xs">
                                    <div>
                                        <span class="text-slate-500">Total Pembayaran:</span>
                                        <span class="font-bold text-slate-900 text-sm ml-1">
                                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    @if($order->status === 'pending')
                                        <a href="{{ route('customer.order.payment', $order) }}"
                                           class="btn-primary text-xs h-7.5 px-3.5 flex items-center gap-1.5 bg-cyan-700 hover:bg-cyan-800">
                                            <i class="fa-solid fa-credit-card text-[10px]"></i>
                                            Bayar Sekarang
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-14 text-center text-slate-400">
                            <i class="fa-solid fa-bag-shopping text-3xl mb-2 text-slate-300"></i>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-700">Belum Ada Riwayat Belanja</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Semua riwayat pemesanan Anda akan tercatat rapi di sini.</p>
                            <a href="{{ url('/products') }}" class="mt-4 inline-block btn-primary text-xs h-8.5 px-4.5 bg-cyan-700 hover:bg-cyan-800">
                                Mulai Belanja Sekarang
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
