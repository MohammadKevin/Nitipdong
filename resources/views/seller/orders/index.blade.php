<x-seller-layout>
    <x-slot name="title">
        Pesanan Masuk Toko - {{ config('app.name', 'NitipDong') }}
    </x-slot>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-4" x-data="{
        showResiModal: false,
        selectedOrder: null,
        trackingNumber: '',
        orderActionUrl: '',
        openResi(order, url) {
            this.selectedOrder = order;
            this.trackingNumber = order.tracking_number || '';
            this.orderActionUrl = url;
            this.showResiModal = true;
        }
    }">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-receipt text-cyan-700"></i>
                Pesanan Masuk Toko
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Pantau pesanan pembeli, cetak label pengiriman, periksa bukti pembayaran, dan perbarui status resi.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('seller.reports.sales.export') }}" class="btn-secondary text-xs h-9 px-3.5 rounded-xl border border-slate-200 hover:bg-slate-50 font-semibold flex items-center gap-1.5 text-slate-700 transition-colors">
                <i class="fa-solid fa-file-excel text-emerald-600"></i>
                <span>Unduh Laporan Penjualan</span>
            </a>
        </div>

        <div x-show="showResiModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="showResiModal = false"
                 class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-2xl border border-slate-200 text-xs">
                <h3 class="font-bold text-sm text-slate-900 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-truck-fast text-cyan-600"></i> Input No. Resi Pengiriman
                </h3>
                <form :action="orderActionUrl" method="POST" class="space-y-3">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="shipped">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nomor Resi (Kurir)</label>
                        <input type="text" name="tracking_number" x-model="trackingNumber" required
                               placeholder="Contoh: JNE8291028192"
                               class="w-full py-2 px-3 rounded-xl border border-slate-300 font-mono text-xs focus:border-cyan-600">
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="showResiModal = false" class="px-3 py-1.5 rounded-lg border border-slate-300 text-slate-600">Batal</button>
                        <button type="submit" class="px-4 py-1.5 rounded-lg bg-cyan-700 text-white font-semibold hover:bg-cyan-800">Simpan & Kirim</button>
                    </div>
                </form>
            </div>
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
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all shrink-0 {{ $currentStatus === $tab['value'] ? 'bg-cyan-700 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200' }}">
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
                        <th class="px-5 py-3.5 font-semibold text-center">Dokumen & Resi</th>
                        <th class="px-5 py-3.5 font-semibold text-center">Update Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($orders as $order)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-5 py-3.5">
                            <span class="font-mono font-bold text-slate-900 text-xs block">#{{ $order->invoice_number }}</span>
                            <span class="text-[10px] text-slate-400 mt-0.5 block">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                            @if($order->complaint)
                                <a href="{{ route('seller.complaints.index') }}" class="inline-flex items-center gap-1 text-[9px] font-bold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200 mt-1">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Ada Komplain
                                </a>
                            @endif
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
                                <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank" class="block text-[10px] text-cyan-700 mt-0.5 font-semibold hover:underline">
                                    <i class="fa-solid fa-receipt"></i> Bukti Bayar
                                </a>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex flex-col items-center gap-1">
                                <a href="{{ route('orders.shipping_label', $order) }}" target="_blank"
                                   class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-700 hover:text-cyan-700 bg-slate-100 hover:bg-slate-200 px-2 py-0.5 rounded transition-colors" title="Cetak Label Pengiriman">
                                    <i class="fa-solid fa-print text-[9px]"></i> Label Resi
                                </a>
                                @if(in_array($order->status, ['processing', 'shipped', 'completed']))
                                    <a href="{{ route('orders.tracking', $order) }}" target="_blank"
                                       class="inline-flex items-center gap-1 text-[10px] font-bold text-cyan-700 hover:text-cyan-800 bg-cyan-50 hover:bg-cyan-100 px-2 py-0.5 rounded border border-cyan-200 transition-colors" title="Lacak Posisi Paket di Peta Live">
                                        <i class="fa-solid fa-map-location-dot text-[9px]"></i> Lacak Peta
                                    </a>
                                @endif
                                <a href="{{ route('orders.invoice', $order) }}" target="_blank"
                                   class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-500 hover:text-cyan-700 hover:underline">
                                    <i class="fa-solid fa-file-invoice text-[9px]"></i> Invoice
                                </a>
                                @if($order->tracking_number)
                                    <span class="font-mono text-[10px] text-slate-600 bg-white px-1.5 py-0.2 rounded border border-slate-200">
                                        {{ $order->tracking_number }}
                                    </span>
                                @endif
                                @if($order->courier)
                                    <span class="inline-flex items-center gap-1 text-[9px] font-bold text-cyan-800 bg-cyan-50 px-1.5 py-0.5 rounded border border-cyan-200" title="Diantar oleh {{ $order->courier->name }}">
                                        <i class="fa-solid fa-motorcycle text-cyan-600"></i> {{ Str::limit($order->courier->name, 10) }}
                                    </span>
                                @endif
                                @if($order->delivery_proof_image)
                                    <a href="{{ asset('storage/' . $order->delivery_proof_image) }}" target="_blank"
                                       class="inline-flex items-center gap-1 text-[9.5px] font-bold text-emerald-700 hover:text-emerald-800 bg-emerald-50 hover:bg-emerald-100 px-1.5 py-0.5 rounded border border-emerald-300 transition-colors" title="Lihat Foto Bukti Serah Terima Paket">
                                        <i class="fa-solid fa-camera text-emerald-600"></i> Bukti Antar
                                    </a>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <form action="{{ route('seller.orders.update_status', $order) }}" method="POST" class="inline-flex items-center gap-1">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="input text-xs py-1 px-2 rounded-md font-semibold text-slate-800 h-8 w-28 min-w-[110px] bg-white border border-slate-300 shadow-xs cursor-pointer focus:border-cyan-600 focus:ring-cyan-200">
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
                        <td colspan="7" class="px-5 py-12 text-center text-slate-400">
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
