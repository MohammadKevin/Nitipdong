<x-seller-layout>
    <x-slot name="title">
        Pesanan Masuk Toko - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-2">
        <div>
            <h1 class="text-xl font-bold text-[#14213D] flex items-center gap-2" style="font-family:'Poppins',sans-serif;">
                <i class="fa-solid fa-clipboard-list text-[#12A57F]"></i>
                Pesanan Masuk Toko
            </h1>
            <p class="text-xs text-[#8A93A6] mt-0.5">Pantau pesanan pembeli, periksa bukti pembayaran, dan perbarui status pengiriman.</p>
        </div>
    </div>

    @php
        $currentStatus = request('status');
    @endphp

    <div class="bg-white rounded-2xl shadow-sm border border-[#EFEBDF] overflow-hidden">
        <div class="p-5 border-b border-[#F0EEE6] flex items-center gap-2 overflow-x-auto bg-slate-50/50">
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
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-colors shrink-0 {{ $currentStatus === $tab['value'] ? 'bg-[#12A57F] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-[#FAF9F5] text-[#8A93A6] font-semibold border-b border-[#F0EEE6]">
                    <tr>
                        <th class="px-6 py-3.5">Invoice & Tanggal</th>
                        <th class="px-6 py-3.5">Pembeli & Alamat</th>
                        <th class="px-6 py-3.5">Item Produk</th>
                        <th class="px-6 py-3.5 text-right">Total Tagihan</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                        <th class="px-6 py-3.5 text-center">Aksi Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F5F3EE]">
                    @forelse ($orders as $order)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-mono font-bold text-slate-800 text-xs block">{{ $order->invoice_number }}</span>
                            <span class="text-[10px] text-slate-400 mt-0.5 block">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-slate-800 text-xs block">{{ $order->user->name ?? 'Pembeli' }}</span>
                            <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-1 max-w-xs" title="{{ $order->shipping_address }}">
                                <i class="fa-solid fa-location-dot text-slate-400 mr-1"></i>{{ $order->shipping_address ?? '-' }}
                            </p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-1 max-w-xs">
                                @foreach($order->orderItems as $item)
                                    <div class="flex items-center justify-between gap-2 text-[11px]">
                                        <span class="truncate text-slate-700 font-medium">{{ $item->product->name ?? 'Produk Dihapus' }}</span>
                                        <span class="text-slate-400 shrink-0">&times;{{ $item->quantity }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="font-bold text-slate-900 text-sm block">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            @if($order->discount_amount > 0)
                                <span class="text-[10px] text-emerald-600 font-medium">Voucher: -Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusStyles = [
                                    'pending'    => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'processing' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'shipped'    => 'bg-purple-50 text-purple-700 border-purple-200',
                                    'completed'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'cancelled'  => 'bg-rose-50 text-rose-700 border-rose-200',
                                ];
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $statusStyles[$order->status] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                            @if($order->payment_proof)
                                <span class="block text-[10px] text-emerald-600 mt-1 font-medium">
                                    <i class="fa-solid fa-receipt"></i> Bukti Terunggah
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('seller.orders.update_status', $order) }}" method="POST" class="inline-flex items-center gap-1.5">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="text-xs py-1.5 px-2.5 bg-white border border-[#E7E3D8] rounded-xl focus:ring-1 focus:ring-[#12A57F] font-medium text-slate-700 shadow-sm">
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
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-400 mb-3 text-2xl">
                                    <i class="fa-solid fa-clipboard-list"></i>
                                </div>
                                <p class="font-medium text-slate-600 text-sm">Tidak ada pesanan pada status ini</p>
                                <p class="text-xs text-slate-400 mt-1">Pesanan dari pembeli akan muncul secara otomatis di sini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-[#F0EEE6]">
            {{ $orders->links('pagination::tailwind') }}
        </div>
    </div>
</x-seller-layout>