<x-app-layout>
    <div class="page-container py-5 max-w-4xl">
        {{-- Header --}}
        <div class="flex items-center gap-3 mb-5">
            <a href="{{ route('customer.cart.index') }}" class="text-slate-500 hover:text-cyan-700">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h1 class="text-xl font-bold text-slate-900">Pilih Voucher BelanjaIn</h1>
        </div>

        @if(session('success'))
            <div class="mb-4 flex items-center gap-2.5 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-lg text-sm font-semibold">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 flex items-center gap-2.5 px-4 py-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-lg text-sm font-semibold">
                <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Minimum Purchase Info --}}
        <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 mb-4 text-xs text-slate-600">
            <span class="font-semibold">Min. Blj Rp0</span>
        </div>

        {{-- Voucher List --}}
        <div class="space-y-3">
            @forelse($vouchers as $voucher)
            <div class="bg-white border border-slate-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                <div class="flex">
                    {{-- Left: Voucher Ticket Design (Red with serrated edge) --}}
                    <div class="w-32 flex-shrink-0 bg-gradient-to-br from-rose-500 to-rose-600 text-white p-4 relative flex flex-col items-center justify-center">
                        {{-- Serrated edge on right --}}
                        <div class="absolute top-0 right-0 bottom-0 w-3 bg-white" style="
                            background-image:
                                radial-gradient(circle at 0 0%, transparent 6px, white 6px),
                                radial-gradient(circle at 0 100%, transparent 6px, white 6px);
                            background-size: 12px 12px;
                            background-position: 0 0, 0 6px;
                            background-repeat: repeat-y;
                        "></div>

                        {{-- Badge/Icon --}}
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mb-2">
                            @if(str_contains(strtolower($voucher->name), 'hall') || str_contains(strtolower($voucher->name), 'star'))
                                <i class="fa-solid fa-star text-2xl text-yellow-300"></i>
                            @elseif(str_contains(strtolower($voucher->name), 'vip'))
                                <i class="fa-solid fa-crown text-2xl text-yellow-300"></i>
                            @elseif(str_contains(strtolower($voucher->name), 'toko') || str_contains(strtolower($voucher->name), 'official'))
                                <i class="fa-solid fa-store text-2xl"></i>
                            @elseif(str_contains(strtolower($voucher->name), 'ongkir'))
                                <i class="fa-solid fa-truck-fast text-2xl"></i>
                            @else
                                <i class="fa-solid fa-ticket text-2xl"></i>
                            @endif
                        </div>

                        <span class="text-[9px] font-bold uppercase text-center leading-tight">
                            @if(str_contains(strtolower($voucher->name), 'semua'))
                                SEMUA<br>KATEGORI
                            @elseif(str_contains(strtolower($voucher->name), 'toko'))
                                TOKO<br>PILIHAN
                            @elseif($voucher->store_id)
                                {{ Str::limit($voucher->store->name ?? 'TOKO', 12) }}
                            @else
                                PROMO<br>SPESIAL
                            @endif
                        </span>
                    </div>

                    {{-- Right: Voucher Details --}}
                    <div class="flex-1 p-4 flex items-center justify-between">
                        <div class="flex-1">
                            {{-- Voucher Name --}}
                            <h3 class="text-sm font-bold text-slate-900 mb-1">
                                {{ $voucher->name }}
                            </h3>

                            {{-- Min Purchase --}}
                            <p class="text-xs text-slate-500 mb-1">
                                Min. Blj Rp{{ number_format($voucher->min_spend, 0, ',', '.') }}
                            </p>

                            {{-- Badge & Expiry --}}
                            <div class="flex items-center gap-2 flex-wrap">
                                @if(str_contains(strtolower($voucher->description), 'spayl ater'))
                                    <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-[10px] font-semibold rounded">SPayLater Cicilan s.d. 24 Bln</span>
                                @elseif(str_contains(strtolower($voucher->description), 'vip'))
                                    <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-[10px] font-semibold rounded">ShopeeVIP</span>
                                @endif

                                @if($voucher->expires_at)
                                    <span class="text-[10px] text-slate-400">
                                        @if($voucher->expires_at->isPast())
                                            <span class="text-rose-600">Sudah kadaluarsa</span>
                                        @elseif($voucher->expires_at->diffInDays() < 7)
                                            <span class="text-orange-600">Segera habis • s.d. {{ $voucher->expires_at->format('d.m.Y') }}</span>
                                        @else
                                            Hingga {{ $voucher->expires_at->format('d.m.Y') }}
                                        @endif
                                        <span class="text-cyan-600 font-semibold cursor-pointer hover:underline ml-1">S&K</span>
                                    </span>
                                @endif
                            </div>

                            {{-- Special Label --}}
                            @if($voucher->amount >= 50)
                                <span class="inline-block mt-1 px-2 py-0.5 bg-rose-600 text-white text-[9px] font-bold rounded">Terbatas</span>
                            @endif
                        </div>

                        {{-- Button Pakai --}}
                        <div class="ml-4">
                            @if($selectedVoucherCode === $voucher->code)
                                <div class="px-4 py-2 bg-emerald-100 text-emerald-700 rounded-lg text-sm font-semibold border-2 border-emerald-500">
                                    <i class="fa-solid fa-check mr-1"></i> Terpilih
                                </div>
                            @else
                                <form action="{{ route('customer.vouchers.select', $voucher) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-sm font-semibold transition-colors">
                                        Pakai
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <i class="fa-solid fa-ticket text-4xl text-slate-300 mb-3"></i>
                <h3 class="text-sm font-bold text-slate-700">Belum Ada Voucher Tersedia</h3>
                <p class="text-xs text-slate-500 mt-1">Tambahkan produk ke keranjang untuk melihat voucher yang tersedia</p>
            </div>
            @endforelse
        </div>

        {{-- Bottom Info --}}
        @if($vouchers->count() > 0)
        <div class="mt-6 p-4 bg-slate-50 rounded-lg border border-slate-200">
            <p class="text-xs text-slate-600">
                <strong>{{ $vouchers->count() }} Voucher Dipilih.</strong><br>
                @if($selectedVoucherCode)
                    <span class="text-emerald-600 font-semibold">Voucher {{ $selectedVoucherCode }} sudah dipilih!</span>
                @else
                    Pilih voucher untuk mendapatkan diskon saat checkout.
                @endif
            </p>
        </div>
        @endif

        {{-- Button OK (Bottom Fixed) --}}
        <div class="mt-6">
            <a href="{{ route('customer.order.checkout') }}" class="block w-full bg-rose-600 hover:bg-rose-700 text-white text-center py-3 rounded-lg font-bold text-base transition-colors">
                OK
            </a>
        </div>
    </div>
</x-app-layout>
