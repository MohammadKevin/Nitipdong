<x-admin-layout>
    <x-slot name="title">
        Monitoring Pesanan - {{ config('app.name', 'NitipDong') }}
    </x-slot>
    <x-slot name="pageTitle">
        Monitoring Pesanan Platform
    </x-slot>

    <!-- HEADER BAR -->
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 pb-1">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                Monitoring Pesanan &amp; Logistik
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Pantau seluruh alur transaksi, status resi pengiriman, dan intervensi operasional pesanan.</p>
        </div>
    </div>

    <!-- 5 SUMMARY CARDS -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5 sm:gap-4">
        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-xs flex items-center gap-3 hover:border-slate-300 transition-colors">
            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-200/70 shrink-0">
                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider truncate">Total Order</p>
                <h4 class="text-lg font-bold text-slate-900 mt-0.5 truncate">{{ number_format($totalOrders, 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-xs flex items-center gap-3 hover:border-slate-300 transition-colors">
            <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-200/70 shrink-0">
                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider truncate">Diproses</p>
                <h4 class="text-lg font-bold text-slate-900 mt-0.5 truncate">{{ number_format($processingOrders, 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-xs flex items-center gap-3 hover:border-slate-300 transition-colors">
            <div class="w-9 h-9 rounded-lg bg-cyan-50 text-cyan-600 flex items-center justify-center border border-cyan-200/70 shrink-0">
                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <rect width="16" height="13" x="1" y="3" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider truncate">Dikirim</p>
                <h4 class="text-lg font-bold text-slate-900 mt-0.5 truncate">{{ number_format($shippedOrders, 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-xs flex items-center gap-3 hover:border-slate-300 transition-colors">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200/70 shrink-0">
                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider truncate">Selesai</p>
                <h4 class="text-lg font-bold text-slate-900 mt-0.5 truncate">{{ number_format($completedOrders, 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-xs flex items-center gap-3 hover:border-slate-300 transition-colors col-span-2 sm:col-span-1">
            <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-200/70 shrink-0">
                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider truncate">Batal / Kendala</p>
                <h4 class="text-lg font-bold text-slate-900 mt-0.5 truncate">{{ number_format($cancelledOrders, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    <!-- MAIN CARD: FILTER & TABLE -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-xs overflow-hidden" x-data="{ selectedOrder: null, showCancelModal: false }">
        
        <!-- Filter Tabs & Search -->
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-slate-50/50">
            <!-- Filter Status Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 lg:pb-0">
                <a href="{{ route('admin.orders.index', ['status' => 'all', 'search' => $search]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $status === 'all' ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    Semua
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'paid', 'search' => $search]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $status === 'paid' ? 'bg-blue-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    Terbayar
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'processing', 'search' => $search]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $status === 'processing' ? 'bg-amber-500 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    Diproses
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'shipped', 'search' => $search]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $status === 'shipped' ? 'bg-cyan-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    Dikirim
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'completed', 'search' => $search]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $status === 'completed' ? 'bg-emerald-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    Selesai
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'cancelled', 'search' => $search]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $status === 'cancelled' ? 'bg-rose-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    Dibatalkan
                </a>
            </div>

            <!-- Search Form -->
            <form action="{{ route('admin.orders.index') }}" method="GET" class="relative">
                <input type="hidden" name="status" value="{{ $status }}">
                <input type="text" name="search" value="{{ $search }}" class="w-full lg:w-72 h-9 pl-9 pr-3 text-xs rounded-lg border border-slate-200 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder:text-slate-400" placeholder="Cari invoice, resi, pembeli, toko...">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
            </form>
        </div>

        <!-- Table Orders -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[11px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5">No. Invoice &amp; Tanggal</th>
                        <th class="px-5 py-3.5">Pembeli &amp; Toko</th>
                        <th class="px-5 py-3.5">Produk Dipesan</th>
                        <th class="px-5 py-3.5">Total &amp; Pembayaran</th>
                        <th class="px-5 py-3.5">Status &amp; Logistik</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <!-- Invoice & Date -->
                        <td class="px-5 py-4 align-top">
                            <span class="font-bold text-slate-900 block text-xs">#{{ $order->invoice_number }}</span>
                            <span class="text-[11px] text-slate-500 mt-0.5 block">{{ $order->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                            @if($order->complaint)
                            <a href="{{ route('admin.complaints.index', ['search' => $order->invoice_number]) }}" class="inline-flex items-center gap-1 text-[10px] font-bold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200 mt-1.5">
                                ⚠ Ada Komplain
                            </a>
                            @endif
                        </td>

                        <!-- Pembeli & Toko -->
                        <td class="px-5 py-4 align-top">
                            <div class="space-y-1.5">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pembeli:</span>
                                    <span class="font-semibold text-slate-800">{{ $order->user->name ?? '-' }}</span>
                                    <span class="text-[11px] text-slate-400 block">{{ $order->user->phone ?? $order->user->email ?? '' }}</span>
                                </div>
                                <div class="pt-1 border-t border-slate-100">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Toko / Merchant:</span>
                                    <span class="font-medium text-blue-700">{{ $order->store->name ?? '-' }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Produk Dipesan -->
                        <td class="px-5 py-4 align-top max-w-xs">
                            <div class="space-y-1.5">
                                @foreach($order->orderItems->take(2) as $item)
                                <div class="flex items-center gap-2">
                                    <img src="{{ $item->product->image_url ?? asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}" class="w-7 h-7 rounded object-cover border border-slate-200 shrink-0" alt="Product">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-medium text-slate-800 truncate">{{ $item->product->name ?? 'Produk Dihapus' }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $item->quantity }}x &bull; Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                @endforeach
                                @if($order->orderItems->count() > 2)
                                <p class="text-[10px] text-slate-500 italic">+{{ $order->orderItems->count() - 2 }} produk lainnya</p>
                                @endif
                            </div>
                        </td>

                        <!-- Total & Pembayaran -->
                        <td class="px-5 py-4 align-top">
                            <span class="font-bold text-slate-900 block text-xs">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            <span class="text-[11px] text-slate-500 mt-0.5 block uppercase">{{ $order->payment_method ?? 'Midtrans Gateway' }}</span>
                            <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded mt-1 inline-block">Lunas (Escrow)</span>
                        </td>

                        <!-- Status & Logistik -->
                        <td class="px-5 py-4 align-top">
                            @if($order->status === 'pending')
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Menunggu Pembayaran</span>
                            @elseif($order->status === 'paid' || $order->status === 'processing')
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">Diproses Toko</span>
                            @elseif($order->status === 'shipped')
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-cyan-50 text-cyan-700 border border-cyan-200">Dalam Pengiriman</span>
                            @elseif($order->status === 'completed' || $order->status === 'delivered')
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Selesai</span>
                            @elseif($order->status === 'cancelled')
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">Dibatalkan</span>
                            @endif

                            @if($order->tracking_number)
                            <div class="mt-1.5">
                                <span class="text-[10px] text-slate-400 block uppercase">No. Resi:</span>
                                <span class="font-mono text-xs font-semibold text-slate-700">{{ $order->tracking_number }}</span>
                            </div>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="px-5 py-4 align-top text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('orders.invoice', $order) }}" target="_blank" class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-100 transition-colors" title="Lihat Invoice">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/>
                                    </svg>
                                </a>

                                @if($order->status !== 'cancelled' && $order->status !== 'completed')
                                <button type="button" 
                                        @click="selectedOrder = {{ json_encode($order) }}; showCancelModal = true"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                        title="Intervensi Batalkan Pesanan">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                        <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-14 text-center text-slate-400">
                            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/>
                                </svg>
                            </div>
                            <span class="text-sm font-semibold text-slate-700 block">Tidak Ada Pesanan Ditemukan</span>
                            <p class="text-xs text-slate-400 mt-1">Coba sesuaikan filter status atau kata kunci pencarian Anda.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="p-4 border-t border-slate-100 bg-white">
            {{ $orders->links() }}
        </div>
        @endif

        <!-- MODAL INTERVENSI PEMBATALAN PESANAN -->
        <div x-show="showCancelModal" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
            <div @click.away="showCancelModal = false" 
                 class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Batalkan Pesanan (Admin)</h3>
                    <button @click="showCancelModal = false" class="text-slate-400 hover:text-slate-700 text-sm font-bold">✕</button>
                </div>

                <p class="text-xs text-slate-500">
                    Anda akan membatalkan pesanan <span class="font-bold text-slate-800" x-text="'#' + selectedOrder?.invoice_number"></span>. Stok produk akan dikembalikan otomatis dan notifikasi pembatalan akan dikirimkan ke pembeli serta penjual.
                </p>

                <form :action="`/admin/orders/${selectedOrder?.id}/cancel`" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Alasan Pembatalan Operasional (Wajib)</label>
                        <textarea name="reason" rows="3" required class="w-full text-xs rounded-xl border border-slate-200 p-3 focus:border-rose-500 focus:ring-1 focus:ring-rose-500 placeholder:text-slate-400" placeholder="Misal: Penjual tidak memproses pesanan melewati 3 hari..."></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" @click="showCancelModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                            Kembali
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition-colors">
                            Konfirmasi Pembatalan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-admin-layout>
