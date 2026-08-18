<x-seller-layout>
    <x-slot name="title">
        Pesanan Masuk Toko - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-2">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-receipt text-cyan-700"></i>
                Pesanan Masuk Toko
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Pantau pesanan pembeli, periksa bukti pembayaran, dan perbarui status pengiriman.</p>
        </div>
    </div>

    @php
        $currentStatus = request('status');
    @endphp

    <div class="bg-white rounded-xl shadow-card border border-slate-200/80 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center gap-1.5 overflow-x-auto bg-slate-50/50">
            @php
            $tabs = [
                ['label' => 'Semua Pesanan',   'value' => null],
                ['label' => 'Menunggu Bayar',  'value' => 'pending'],
                ['label' => 'Perlu Diproses',  'value' => 'processing'],
                ['label' => 'Sedang Dikirim',  'value' => 'shipped'],
                ['label' => 'Selesai',         'value' => 'completed'],
                ['label' => 'Dibatalkan',      'value' => 'cancelled'],
            ];
            @endphp
            @foreach($tabs as $tab)
                <a href="{{ route('seller.orders.index', $tab['value'] ? ['status' => $tab['value']] : []) }}"
                   class="px-3 py-1.5 rounded-md text-xs font-semibold transition-colors shrink-0 {{ $currentStatus === $tab['value'] ? 'bg-cyan-700 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200' }}">
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 font-semibold">Invoice & Tanggal</th>
                        <th class="px-5 py-3.5 font-semibold">Pembeli & Alamat</th>
                        <th class="px-5 py-3.5 font-semibold">Item Produk</th>
                        <th class="px-5 py-3.5 font-semibold text-right">Total Tagihan</th>
                        <th class="px-5 py-3.5 font-semibold text-center">Status</th>
                        <th class="px-5 py-3.5 font-semibold text-center">Update Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($orders as $order)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-5 py-3.5">
                            <span class="font-mono font-bold text-slate-900 text-xs block">#{{ $order->invoice_number }}</span>
                            <span class="text-[10px] text-slate-400 mt-0.5 block">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="font-bold text-slate-900 text-xs block">{{ $order->user->name ?? 'Pembeli' }}</span>
                            <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-1 max-w-xs" title="{{ $order->shipping_address }}">
                                <i class="fa-solid fa-location-dot text-slate-400 mr-1"></i>{{ $order->shipping_address ?? '-' }}
                            </p>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="space-y-0.5 max-w-xs">
                                @foreach($order->orderItems as $item)
                                    <div class="flex items-center justify-between gap-2 text-[11px]">
                                        <span class="truncate text-slate-700 font-medium">{{ $item->product->name ?? 'Produk Dihapus' }}</span>
                                        <span class="text-slate-400 shrink-0">&times;{{ $item->quantity }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <span class="font-bold text-slate-900 text-xs block">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            @if($order->discount_amount > 0)
                                <span class="text-[10px] text-cyan-700 font-medium">Voucher: -Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @php
                                $statusStyles = [
                                    'pending'    => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'processing' => 'bg-cyan-50 text-cyan-800 border-cyan-200',
                                    'shipped'    => 'bg-purple-50 text-purple-700 border-purple-200',
                                    'completed'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'cancelled'  => 'bg-rose-50 text-rose-700 border-rose-200',
                                ];
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $statusStyles[$order->status] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                            @if($order->payment_proof)
                                <span class="block text-[10px] text-cyan-700 mt-0.5 font-medium">
                                    <i class="fa-solid fa-receipt"></i> Bukti Ada
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <form action="{{ route('seller.orders.update_status', $order) }}" method="POST" class="inline-flex items-center gap-1">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="input text-xs py-1 px-2 rounded-md font-semibold text-slate-800 h-7.5">
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Proses</option>
                                    <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Kirim</option>
                                    <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Batal</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                            Tidak ada pesanan pada status ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 bg-white">
            {{ $orders->links('pagination::tailwind') }}
        </div>
    </div>
</x-seller-layout>