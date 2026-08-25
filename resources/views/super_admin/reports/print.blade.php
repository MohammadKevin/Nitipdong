<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan & Transaksi - NitipDong Platform</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #0F172A;
            background-color: #ffffff;
        }
        .font-mono-num {
            font-family: 'JetBrains Mono', monospace;
            font-variant-numeric: tabular-nums lining-nums;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            @page {
                size: A4 landscape;
                margin: 12mm;
            }
        }
    </style>
</head>
<body class="p-6 sm:p-10 max-w-6xl mx-auto text-slate-900 bg-white">

    <div class="no-print mb-6 p-4 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-between">
        <div class="flex items-center gap-2">
            <a href="{{ route('super_admin.reports.index', request()->query()) }}" class="px-3 py-1.5 rounded-md border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50">
                &larr; Kembali ke Dashboard
            </a>
            <span class="text-xs text-slate-500 font-medium">Pratinjau Dokumen Cetak / Simpan PDF</span>
        </div>
        <button onclick="window.print()" class="px-4 py-1.5 rounded-md bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs flex items-center gap-1.5 shadow-xs cursor-pointer">
            <span>Cetak / Unduh PDF Sekarang</span>
        </button>
    </div>

    <div class="border-b-2 border-slate-900 pb-5 mb-6">
        <div class="flex justify-between items-start">
            <div class="flex items-center gap-3">
                <img src="{{ asset('icon-app-web-terbaru/nitipdong-icon-mark.svg') }}" alt="Logo" class="w-10 h-10 object-contain">
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">NITIPDONG PLATFORM</h1>
                    <p class="text-xs text-slate-500 font-mono-num">Enterprise Marketplace & Multi-Vendor E-Commerce</p>
                </div>
            </div>
            <div class="text-right text-xs">
                <span class="inline-block px-2.5 py-0.5 rounded bg-slate-100 font-bold uppercase tracking-wider text-[10px] text-slate-700 border border-slate-300 font-mono-num mb-1">
                    DOKUMEN RESMI PLATFORM
                </span>
                <p class="text-slate-500 text-[11px] font-mono-num">Tanggal Cetak: {{ date('d M Y, H:i') }} WIB</p>
                <p class="text-slate-500 text-[11px] font-mono-num">Dicetak oleh: {{ auth()->user()->name }} (Super Admin)</p>
            </div>
        </div>

        <div class="mt-4 pt-3 border-t border-slate-200 flex justify-between items-center text-xs">
            <div>
                <span class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Laporan: </span>
                <span class="text-slate-700 font-medium">Rekapitulasi Penjualan &amp; Pembagian Komisi Platform (15%)</span>
            </div>
            <div class="font-mono-num">
                <span class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Periode: </span>
                <span class="text-blue-700 font-bold">
                    @if($startDate && $endDate)
                        {{ Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
                    @else
                        Semua Waktu Historis
                    @endif
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-4 gap-3 mb-6 font-mono-num">
        <div class="p-3.5 bg-slate-50 rounded-lg border border-slate-200">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Gross Volume (GMV)</span>
            <span class="text-base font-extrabold text-slate-900 mt-1 block">Rp {{ number_format($totalGMV, 0, ',', '.') }}</span>
        </div>
        <div class="p-3.5 bg-emerald-50/60 rounded-lg border border-emerald-200">
            <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Laba Komisi Platform 15%</span>
            <span class="text-base font-extrabold text-emerald-800 mt-1 block">Rp {{ number_format($totalPlatformFee, 0, ',', '.') }}</span>
        </div>
        <div class="p-3.5 bg-purple-50/60 rounded-lg border border-purple-200">
            <span class="text-[10px] font-bold text-purple-800 uppercase tracking-wider block">Hak Pembayaran Toko 85%</span>
            <span class="text-base font-extrabold text-purple-900 mt-1 block">Rp {{ number_format($totalSellerEarnings, 0, ',', '.') }}</span>
        </div>
        <div class="p-3.5 bg-slate-50 rounded-lg border border-slate-200">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Total Transaksi</span>
            <span class="text-base font-extrabold text-slate-900 mt-1 block">{{ number_format($totalOrders, 0, ',', '.') }} Pesanan</span>
        </div>
    </div>

    <table class="w-full text-left text-xs border border-slate-300 mb-6">
        <thead class="bg-slate-100 text-slate-800 font-bold uppercase text-[10px] tracking-wider border-b border-slate-300 font-mono-num">
            <tr>
                <th class="p-2.5 border-r border-slate-300 w-10 text-center">No</th>
                <th class="p-2.5 border-r border-slate-300">Invoice & Tanggal</th>
                <th class="p-2.5 border-r border-slate-300">Nama Toko</th>
                <th class="p-2.5 border-r border-slate-300">Nama Pembeli</th>
                <th class="p-2.5 border-r border-slate-300">Ringkasan Produk</th>
                <th class="p-2.5 border-r border-slate-300 text-right">Nilai GMV (Rp)</th>
                <th class="p-2.5 border-r border-slate-300 text-right">Komisi 15% (Rp)</th>
                <th class="p-2.5 text-right">Hak Toko 85% (Rp)</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 font-mono-num text-[11px]">
            @forelse($orders as $idx => $order)
            @php
                $fee = round($order->total_amount * 0.15);
                $sellerNet = $order->total_amount - $fee;
            @endphp
            <tr class="{{ $idx % 2 === 1 ? 'bg-slate-50/50' : 'bg-white' }}">
                <td class="p-2.5 border-r border-slate-200 text-center text-slate-500">{{ $idx + 1 }}</td>
                <td class="p-2.5 border-r border-slate-200 font-bold">
                    {{ $order->invoice_number }}<br>
                    <span class="text-[10px] font-normal text-slate-500">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </td>
                <td class="p-2.5 border-r border-slate-200 font-sans font-medium text-slate-800">
                    {{ $order->store->name ?? '-' }}
                </td>
                <td class="p-2.5 border-r border-slate-200 font-sans text-slate-700">
                    {{ $order->user->name ?? '-' }}
                </td>
                <td class="p-2.5 border-r border-slate-200 font-sans text-[10px] text-slate-600">
                    {{ $order->orderItems->map(fn($it) => ($it->product->name ?? 'Item') . " ({$it->quantity}x)")->join(', ') }}
                </td>
                <td class="p-2.5 border-r border-slate-200 text-right font-bold">
                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                </td>
                <td class="p-2.5 border-r border-slate-200 text-right font-bold text-emerald-800">
                    Rp {{ number_format($fee, 0, ',', '.') }}
                </td>
                <td class="p-2.5 text-right text-slate-800">
                    Rp {{ number_format($sellerNet, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="p-8 text-center text-slate-400 font-sans">Tidak ada data transaksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot class="bg-slate-100 font-bold border-t-2 border-slate-300 font-mono-num text-xs">
            <tr>
                <td colspan="5" class="p-3 text-right uppercase tracking-wider text-slate-700 font-bold font-sans">
                    TOTAL KESELURUHAN LAPORAN:
                </td>
                <td class="p-3 text-right text-slate-900 border-r border-slate-300">
                    Rp {{ number_format($totalGMV, 0, ',', '.') }}
                </td>
                <td class="p-3 text-right text-emerald-800 border-r border-slate-300">
                    Rp {{ number_format($totalPlatformFee, 0, ',', '.') }}
                </td>
                <td class="p-3 text-right text-slate-900">
                    Rp {{ number_format($totalSellerEarnings, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="mt-10 pt-6 border-t border-slate-200 flex justify-between items-end text-xs">
        <div>
            <p class="text-[10px] text-slate-400 font-mono-num uppercase tracking-wider">Keaslian Dokumen</p>
            <p class="text-xs text-slate-600 mt-1">Laporan ini dibuat otomatis oleh sistem mesin analitik NitipDong Engine v2.4.</p>
            <p class="text-[10px] text-slate-400 font-mono-num mt-0.5">Kode Hash Verifikasi: {{ strtoupper(substr(md5(now() . $totalGMV), 0, 16)) }}</p>
        </div>

        <div class="text-center w-64">
            <p class="text-xs font-semibold text-slate-800">Super Administrator Platform,</p>
            <div class="h-16 flex items-center justify-center">
                <span class="px-3 py-1 rounded border border-slate-300 bg-slate-50 text-[10px] font-bold text-blue-800 uppercase tracking-wider font-mono-num">
                    TERVERIFIKASI SISTEM
                </span>
            </div>
            <p class="text-xs font-bold text-slate-900 border-t border-slate-400 pt-1 font-mono-num">{{ auth()->user()->name }}</p>
            <p class="text-[10px] text-slate-500">Otoritas Pusat NitipDong</p>
        </div>
    </div>

</body>
</html>
