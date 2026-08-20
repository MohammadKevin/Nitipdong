<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Label Pengiriman #{{ $order->invoice_number }} — SakserShop</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+128&family=Plus+Jakarta+Sans:wght@500;700;800&family=Space+Mono:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css'])
    <style>
        .barcode {
            font-family: 'Libre Barcode 128', cursive;
            font-size: 48px;
            line-height: 1;
        }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; padding: 0 !important; margin: 0 !important; }
            .label-card { border: 2px solid #000 !important; box-shadow: none !important; width: 100% !important; max-width: 105mm !important; margin: 0 auto !important; }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-900 min-h-screen py-8">

    {{-- Top Action Bar --}}
    <div class="max-w-md mx-auto mb-4 px-2 flex items-center justify-between no-print">
        <a href="javascript:history.back()" class="text-xs font-semibold text-slate-600 hover:text-cyan-700 flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
        <button onclick="window.print()" class="btn-primary text-xs h-8 px-4 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-semibold flex items-center gap-1.5 shadow-xs cursor-pointer">
            <i class="fa-solid fa-print"></i> Cetak Label (A6 / Thermal)
        </button>
    </div>

    {{-- Thermal / A6 Shipping Label Card --}}
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-card border-2 border-slate-900 p-5 label-card text-xs">
        {{-- Header Strip --}}
        <div class="flex items-center justify-between pb-3 border-b-2 border-slate-900">
            <div class="flex items-center gap-1.5">
                <div class="w-6 h-6 rounded bg-slate-900 text-white flex items-center justify-center font-extrabold text-xs">S</div>
                <span class="font-extrabold text-base tracking-tight">Sakser<span class="text-cyan-700">Shop</span></span>
            </div>
            <div class="text-right">
                <span class="font-extrabold text-xs uppercase px-2 py-0.5 bg-slate-900 text-white rounded">
                    {{ $order->shipping_courier ?? 'EXP' }} - {{ $order->shipping_service ?? 'REG' }}
                </span>
            </div>
        </div>

        {{-- Barcode & Resi Box --}}
        <div class="py-3 text-center border-b-2 border-slate-900">
            @php
                $barcodeText = $order->tracking_number ?: $order->invoice_number;
            @endphp
            <div class="barcode overflow-hidden select-none">{{ $barcodeText }}</div>
            <p class="font-mono font-bold text-sm tracking-wider mt-0.5">{{ $barcodeText }}</p>
            <div class="flex items-center justify-center gap-3 text-[10px] text-slate-500 font-mono mt-0.5">
                <span>Invoice: #{{ $order->invoice_number }}</span>
                <span>•</span>
                <span>Berat: {{ number_format($order->total_weight ?? 0.5, 1) }} kg</span>
            </div>
        </div>

        {{-- Destination / Receiver Box (Big & Clear) --}}
        <div class="py-3 border-b-2 border-slate-900">
            <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider block">PENERIMA:</span>
            <h3 class="font-bold text-sm text-slate-900 mt-0.5">{{ $order->user->name }}</h3>
            <p class="text-xs font-bold text-slate-800 font-mono mt-0.5">{{ $order->user->phone ?? '08xxxxxxxxxx' }}</p>
            <p class="text-xs text-slate-800 mt-1.5 whitespace-pre-line leading-relaxed font-medium bg-slate-50 p-2 rounded border border-slate-200">
                {{ $order->shipping_address }}
            </p>
        </div>

        {{-- Sender / Store Box --}}
        <div class="py-3 border-b-2 border-slate-900 grid grid-cols-2 gap-2 text-[11px]">
            <div>
                <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider block">PENGIRIM:</span>
                <strong class="text-slate-900 block mt-0.5">{{ $order->store->name ?? 'SakserShop Store' }}</strong>
                <p class="text-slate-600 mt-0.5 line-clamp-2">{{ $order->store->address ?? 'Kota Penjual' }}</p>
            </div>
            <div class="border-l border-slate-200 pl-2">
                <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider block">METODE BAYAR:</span>
                <strong class="text-emerald-700 block mt-0.5 uppercase">{{ strtoupper($order->payment_method ?? 'TRANSFER') }} (LUNAS)</strong>
                <span class="text-[10px] text-slate-500 block mt-1">Total: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Item Contents & Note --}}
        <div class="py-3 border-b-2 border-slate-900 text-[11px]">
            <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider block mb-1">ISI PAKET:</span>
            <div class="space-y-1 max-h-32 overflow-hidden">
                @foreach($order->orderItems as $it)
                    <div class="flex justify-between gap-2 text-slate-700">
                        <span class="truncate font-medium">
                            • {{ $it->product ? $it->product->name : 'Item' }}
                            @if($it->variant)
                                <span class="text-[10px] text-slate-500 font-normal">({{ $it->variant }})</span>
                            @endif
                        </span>
                        <span class="font-bold shrink-0">&times;{{ $it->quantity }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Footer Warnings --}}
        <div class="pt-3 flex items-center justify-between text-[10px] text-slate-500 font-bold">
            <span class="flex items-center gap-1 text-rose-700">
                <i class="fa-solid fa-wine-glass"></i> FRAGILE / JANGAN DIBANTING
            </span>
            <span>SakserShop Express</span>
        </div>
    </div>

</body>
</html>
