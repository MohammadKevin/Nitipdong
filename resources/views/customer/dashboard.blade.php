<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Dashboard Pembeli') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('info'))
                <div class="p-4 bg-blue-50 border border-blue-200 text-blue-700 rounded-2xl flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('info') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Welcome Header -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 md:p-8 overflow-hidden relative">
                <div class="absolute top-0 right-0 -mt-8 -mr-8 w-48 h-48 bg-emerald-50 rounded-full blur-3xl opacity-50"></div>
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800 mb-2">Halo, {{ Auth::user()->name }}! 👋</h3>
                        <p class="text-slate-500">Selamat datang kembali di BelanjaIn. Siap untuk belanja hari ini?</p>
                    </div>
                    <div>
                        <a href="/" class="inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-6 py-3 rounded-xl shadow-sm shadow-emerald-600/20 transition-all duration-300">
                            Mulai Belanja
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pesanan Saya -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-bold text-slate-800">Daftar Pesanan Saya</h3>
                </div>
                
                <div class="divide-y divide-slate-100 p-6 space-y-4">
                    @forelse ($orders as $order)
                        <div class="border border-slate-100 rounded-2xl p-4 sm:p-6 hover:border-emerald-200 transition-colors bg-white">
                            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-4 pb-4 border-b border-slate-50">
                                <div>
                                    <p class="text-xs text-slate-400 mb-1">
                                        {{ $order->created_at->format('d M Y, H:i') }} • <span class="font-medium text-slate-600">{{ $order->invoice_number }}</span>
                                    </p>
                                    <p class="font-bold text-slate-800 flex items-center gap-2">
                                        Toko: {{ $order->store->name ?? '-' }}
                                    </p>
                                </div>
                                <div>
                                    @if($order->status === 'pending')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">Menunggu Pembayaran</span>
                                    @elseif($order->status === 'processing')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Sedang Diproses</span>
                                    @elseif($order->status === 'shipped')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">Sedang Dikirim</span>
                                    @elseif($order->status === 'completed')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">Selesai</span>
                                    @else
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800">Dibatalkan</span>
                                    @endif
                                </div>
                            </div>

                            <div class="space-y-3 mb-4">
                                @foreach($order->orderItems as $item)
                                    <div class="flex justify-between items-center text-sm">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-slate-100 rounded-lg overflow-hidden shrink-0">
                                                @if($item->product->image)
                                                    <img src="{{ asset('storage/' . $item->product->image) }}" class="w-full h-full object-cover">
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-semibold text-slate-700">{{ $item->product->name }}</p>
                                                <p class="text-xs text-slate-500">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 pt-4 border-t border-slate-50">
                                <div>
                                    <p class="text-xs text-slate-500">Total Belanja</p>
                                    <p class="font-bold text-slate-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    @if($order->status === 'pending' && !$order->payment_proof)
                                        <a href="{{ route('customer.order.payment', $order) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors">
                                            Bayar Sekarang
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <p class="text-slate-500 mb-4">Belum ada pesanan.</p>
                            <a href="/" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl font-medium hover:bg-slate-200 transition-colors">Mulai Belanja</a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Ajakan Menjadi Seller -->
            @if(!$userStore)
                <div x-data="{ openModal: false }" class="bg-gradient-to-r from-emerald-600 to-indigo-600 rounded-3xl p-8 md:p-10 shadow-lg text-white relative overflow-hidden mt-8">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/2"></div>
                    <div class="relative z-10 md:w-2/3">
                        <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-semibold tracking-wider mb-4 border border-white/20">PELUANG BISNIS</span>
                        <h3 class="text-2xl md:text-3xl font-bold mb-4 leading-tight">Mulai Jual Produkmu Sendiri!</h3>
                        <p class="text-emerald-50 mb-6 text-sm md:text-base leading-relaxed">
                            Punya produk menarik? Buka tokomu di BelanjaIn sekarang dan jangkau ribuan pembeli. Gratis biaya pendaftaran dan nikmati fitur lengkap khusus seller.
                        </p>
                        <a href="{{ route('customer.store.register') }}" class="inline-block bg-white text-emerald-600 hover:bg-slate-50 font-bold px-6 py-3 rounded-xl shadow-lg transition-colors">
                            Buka Toko Gratis
                        </a>
                    </div>
                </div>
            @elseif($userStore->status === 'pending')
                <div class="bg-amber-50 border border-amber-200 rounded-3xl p-6 mt-8 flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-amber-800">Pengajuan Toko Sedang Diproses</h4>
                        <p class="text-sm text-amber-700">Mohon menunggu, admin kami sedang meninjau pengajuan toko Anda ({{ $userStore->name }}).</p>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
