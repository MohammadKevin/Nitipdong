<x-app-layout>
    <div class="page-container py-6">
        @if(session('success'))
            <div class="mb-5 flex items-center gap-2.5 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-xs font-semibold animate-fade-up">
                <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-6">
            <aside class="w-full lg:w-64 shrink-0 space-y-4">
                <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=059669&color=fff&size=100"
                             class="w-12 h-12 rounded-full border border-emerald-200 object-cover shrink-0" alt="{{ Auth::user()->name }}">
                        <div class="min-w-0">
                            <h3 class="font-bold text-slate-900 text-sm truncate">{{ Auth::user()->name }}</h3>
                            <p class="text-[11px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Member BelanjaIn
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2 pt-3 border-t border-slate-100 text-center">
                        <div class="p-2 rounded-lg bg-slate-50">
                            <p class="font-bold text-slate-900 text-sm">{{ $orders->count() }}</p>
                            <p class="text-[9px] text-slate-400">Pesanan</p>
                        </div>
                        <div class="p-2 rounded-lg bg-slate-50">
                            <p class="font-bold text-slate-900 text-sm">{{ $orders->where('status','completed')->count() }}</p>
                            <p class="text-[9px] text-slate-400">Selesai</p>
                        </div>
                        <div class="p-2 rounded-lg bg-slate-50">
                            <p class="font-bold text-slate-900 text-sm">{{ $orders->where('status','pending')->count() }}</p>
                            <p class="text-[9px] text-slate-400">Proses</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200/80 p-3 shadow-xs space-y-1 text-xs">
                    <a href="{{ route('customer.dashboard') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-semibold {{ request()->routeIs('customer.dashboard') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="fa-solid fa-bag-shopping w-4 text-emerald-600"></i>
                        Pesanan Saya
                    </a>
                    <a href="{{ route('customer.cart.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                        <i class="fa-solid fa-cart-shopping w-4 text-slate-400"></i>
                        Keranjang Belanja
                    </a>
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                        <i class="fa-solid fa-user-gear w-4 text-slate-400"></i>
                        Pengaturan Profil
                    </a>
                </div>
            </aside>

            <div class="flex-1 min-w-0 space-y-4">
                <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <h2 class="font-bold text-sm text-slate-900">Riwayat Pesanan Belanja</h2>
                        <span class="text-xs text-slate-400">Total {{ $orders->count() }} transaksi</span>
                    </div>

                    @if($orders->count() > 0)
                        <div class="space-y-4">
                            @foreach($orders as $order)
                            <div class="border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
                                <div class="px-4 py-2.5 bg-slate-50/70 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-receipt text-slate-400"></i>
                                        <span class="font-mono font-bold text-slate-800">{{ $order->invoice_number }}</span>
                                        <span class="text-slate-400 text-[11px]">• {{ $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                                    </div>
                                    @php
                                        $statusBadge = [
                                            'pending'    => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'processing' => 'bg-blue-50 text-blue-700 border-blue-200',
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
                                    @foreach($order->orderItems as $item)
                                    <div class="flex items-center gap-3.5">
                                        <div class="w-12 h-12 rounded-lg bg-slate-100 border border-slate-200 overflow-hidden shrink-0">
                                            @if($item->product && $item->product->image)
                                                <img src="{{ asset('storage/' . $item->product->image) }}" class="w-full h-full object-cover" alt="{{ $item->product->name }}">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-300 text-lg">
                                                    <i class="fa-solid fa-box"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h4 class="text-xs font-semibold text-slate-800 line-clamp-1">{{ $item->product->name ?? 'Produk Dihapus' }}</h4>
                                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $item->quantity }} barang &times; Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="px-4 py-3 bg-slate-50/40 border-t border-slate-100 flex items-center justify-between gap-4">
                                    <div>
                                        <span class="text-[11px] text-slate-400 block">Total Pembayaran</span>
                                        <span class="text-xs sm:text-sm font-bold text-slate-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                    </div>
                                    @if($order->status === 'pending' && !$order->payment_proof)
                                        <a href="{{ route('customer.order.payment', $order) }}" class="btn-primary text-xs py-1.5 px-3.5">
                                            Bayar Sekarang
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-12 text-center text-slate-400">
                            <i class="fa-solid fa-bag-shopping text-3xl mb-2 text-slate-300"></i>
                            <p class="text-sm font-semibold text-slate-700">Belum ada transaksi</p>
                            <p class="text-xs text-slate-400 mt-1">Mulailah mencari produk idaman Anda di BelanjaIn.</p>
                            <a href="{{ url('/products') }}" class="mt-3 inline-block btn-primary text-xs">
                                Mulai Belanja
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
