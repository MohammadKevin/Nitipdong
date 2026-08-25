<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice #{{ $order->invoice_number }} — NitipDong</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
            .print-container { box-shadow: none !important; border: none !important; max-width: 100% !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-900 min-h-screen py-8">

    <div class="max-w-3xl mx-auto mb-4 px-4 flex items-center justify-between no-print">
        <a href="javascript:void(0);" onclick="if (window.history.length > 1 && document.referrer) { window.history.back(); } else { window.close(); setTimeout(function() { window.location.href = '{{ url('/') }}'; }, 150); }" class="text-xs font-semibold text-slate-600 hover:text-cyan-700 flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="btn-primary text-xs h-8.5 px-4 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-semibold flex items-center gap-1.5 shadow-xs cursor-pointer">
                <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-card border border-slate-200 p-8 sm:p-10 print-container">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-200 gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-cyan-700 text-white flex items-center justify-center font-bold text-sm">N</div>
                    <span class="font-extrabold text-xl text-slate-900 tracking-tight">Nitip<span class="text-cyan-700">Dong</span></span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Platform Belanja Online Terpercaya di Indonesia</p>
            </div>
            <div class="text-left sm:text-right">
                <h2 class="text-lg font-extrabold text-slate-900 tracking-tight uppercase">Invoice Pembelian</h2>
                <p class="text-xs font-mono font-bold text-cyan-800 mt-0.5">#{{ $order->invoice_number }}</p>
                <p class="text-[11px] text-slate-400">{{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 py-6 border-b border-slate-100 text-xs">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Diterbitkan Atas Nama:</span>
                <p class="font-bold text-slate-900 text-sm">{{ $order->store->name ?? 'Official Store NitipDong' }}</p>
                <p class="text-slate-500 mt-0.5">{{ $order->store->address ?? 'Indonesia' }}</p>
                <p class="text-slate-600 mt-2">
                    <strong>Metode Pembayaran:</strong> {{ strtoupper($order->payment_method ?? 'QRIS') }}
                </p>
                @if($order->shipping_courier)
                    <p class="text-slate-600">
                        <strong>Ekspedisi:</strong> {{ $order->shipping_courier }} - {{ $order->shipping_service }} ({{ number_format($order->total_weight ?? 0.5, 1) }} kg)
                    </p>
                @endif
                @if($order->tracking_number)
                    <p class="text-slate-600">
                        <strong>No. Resi Pengiriman:</strong> {{ $order->tracking_number }}
                    </p>
                @endif
            </div>

            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Tujuan Pengiriman:</span>
                <p class="font-bold text-slate-900 text-sm">{{ $order->user->name }}</p>
                <p class="text-slate-600 mt-0.5 whitespace-pre-line leading-relaxed">{{ $order->shipping_address }}</p>
            </div>
        </div>

        <div class="py-6">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500 text-[11px] uppercase font-bold">
                        <th class="pb-3">Info Produk</th>
                        <th class="pb-3 text-center">Jumlah</th>
                        <th class="pb-3 text-right">Harga Satuan</th>
                        <th class="pb-3 text-right">Total Harga</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($order->orderItems as $item)
                    <tr>
                        <td class="py-3.5 pr-3">
                            <span class="font-bold text-slate-900 block">{{ $item->product ? $item->product->name : 'Produk' }}</span>
                            @if($item->variant)
                                <span class="inline-block px-1.5 py-0.5 bg-slate-100 text-slate-600 text-[10px] rounded font-medium mt-0.5">
                                    Varian: {{ $item->variant }}
                                </span>
                            @endif
                            <span class="text-[10px] text-slate-400 block mt-0.5">SKU: {{ $item->product->sku ?? '-' }}</span>
                        </td>
                        <td class="py-3.5 px-3 text-center font-bold text-slate-800">{{ $item->quantity }}</td>
                        <td class="py-3.5 px-3 text-right text-slate-700">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="py-3.5 pl-3 text-right font-extrabold text-slate-900">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row justify-between items-start gap-6 text-xs">
            <div class="max-w-xs">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Status Transaksi:</span>
                @php
                    $statusLabels = [
                        'pending'    => ['label' => 'Menunggu Pembayaran', 'class' => 'text-amber-700 bg-amber-50 border-amber-200'],
                        'processing' => ['label' => 'Diproses Penjual',     'class' => 'text-cyan-800 bg-cyan-50 border-cyan-200'],
                        'shipped'    => ['label' => 'Sedang Dikirim',      'class' => 'text-purple-700 bg-purple-50 border-purple-200'],
                        'completed'  => ['label' => 'Lunas & Selesai',     'class' => 'text-emerald-700 bg-emerald-50 border-emerald-200'],
                        'cancelled'  => ['label' => 'Dibatalkan',          'class' => 'text-rose-700 bg-rose-50 border-rose-200'],
                    ];
                    $st = $statusLabels[$order->status] ?? ['label' => ucfirst($order->status), 'class' => 'text-slate-700 bg-slate-50 border-slate-200'];
                @endphp
                <span class="inline-block px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $st['class'] }}">
                    {{ $st['label'] }}
                </span>
                <p class="text-[10px] text-slate-400 mt-2 leading-relaxed">
                    Invoice ini merupakan bukti pembayaran yang sah dan diterbitkan secara elektronik oleh sistem NitipDong.
                </p>
            </div>

            <div class="w-full sm:w-64 space-y-2">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal Produk:</span>
                    <span class="font-semibold text-slate-900">
                        Rp {{ number_format($order->orderItems->sum(fn($i) => $i->price * $i->quantity), 0, ',', '.') }}
                    </span>
                </div>
                @if($order->discount_amount > 0)
                <div class="flex justify-between text-cyan-700">
                    <span>Diskon Voucher ({{ $order->voucher_code }}):</span>
                    <span class="font-semibold">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between text-slate-600">
                    <span>Biaya Ongkir ({{ $order->shipping_courier ?? 'Kurir' }}):</span>
                    <span class="font-semibold {{ $order->shipping_cost > 0 ? 'text-slate-900' : 'text-emerald-600' }}">
                        {{ $order->shipping_cost > 0 ? 'Rp ' . number_format($order->shipping_cost, 0, ',', '.') : 'Gratis (Rp 0)' }}
                    </span>
                </div>
                <div class="pt-2 border-t border-slate-200 flex justify-between items-baseline">
                    <span class="font-bold text-slate-900 text-sm">Total Bayar:</span>
                    <span class="font-extrabold text-cyan-800 text-base">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
            <span>Terima kasih telah berbelanja di NitipDong</span>
            <span>www.nitipdong.com</span>
        </div>
    </div>

</body>
</html>
