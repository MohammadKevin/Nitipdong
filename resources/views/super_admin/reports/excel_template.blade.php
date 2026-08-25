<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Laporan Keuangan Platform NitipDong</title>
    <style>
        body {
            font-family: Calibri, 'Segoe UI', Arial, sans-serif;
            font-size: 11pt;
            color: #0F172A;
            background-color: #FFFFFF;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            font-family: Calibri, 'Segoe UI', Arial, sans-serif;
        }
        .header-title {
            font-size: 16pt;
            font-weight: bold;
            color: #0F172A;
            height: 40px;
            vertical-align: middle;
        }
        .meta-label {
            font-size: 10pt;
            color: #475569;
            font-weight: bold;
        }
        .meta-val {
            font-size: 10pt;
            color: #0F172A;
            font-weight: bold;
        }
        .th-header {
            background-color: #0F172A;
            color: #FFFFFF;
            font-weight: bold;
            font-size: 10.5pt;
            text-align: center;
            border: 1px solid #0F172A;
            height: 32px;
            vertical-align: middle;
            padding: 8px;
        }
        .td-data {
            border: 1px solid #CBD5E1;
            padding: 6px 8px;
            vertical-align: middle;
            font-size: 10pt;
        }
        .td-center {
            text-align: center;
        }
        .td-right {
            text-align: right;
            mso-number-format: "\#\,\#\#0";
        }
        .td-date {
            text-align: center;
            mso-number-format: "dd\/mm\/yyyy\ hh\:mm";
        }
        .td-total {
            background-color: #F1F5F9;
            font-weight: bold;
            border-top: 2px solid #0F172A;
            border-bottom: 2px solid #0F172A;
            border-left: 1px solid #CBD5E1;
            border-right: 1px solid #CBD5E1;
            height: 35px;
            vertical-align: middle;
            padding: 8px;
        }
        .highlight-fee {
            background-color: #ECFDF5;
            color: #065F46;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <table border="1" cellpadding="6" cellspacing="0">
        <!-- Meta info -->
        <tr>
            <td colspan="10" class="header-title" style="border: none;">LAPORAN KEUANGAN &amp; KOMISI PLATFORM NITIPDONG</td>
        </tr>
        <tr>
            <td colspan="2" class="meta-label" style="border: none;">Tanggal Dibuat:</td>
            <td colspan="8" class="meta-val" style="border: none;">{{ date('d F Y, H:i:s') }} WIB</td>
        </tr>
        <tr>
            <td colspan="2" class="meta-label" style="border: none;">Periode Laporan:</td>
            <td colspan="8" class="meta-val" style="border: none;">{{ $periodText ?? 'Semua Waktu Historis' }}</td>
        </tr>
        <tr>
            <td colspan="2" class="meta-label" style="border: none;">Total Transaksi:</td>
            <td colspan="8" class="meta-val" style="border: none;">{{ number_format($orders->count(), 0, ',', '.') }} Pesanan</td>
        </tr>
        <tr>
            <td colspan="10" style="height: 15px; border: none;"></td>
        </tr>

        <!-- Table Columns Header -->
        <thead>
            <tr>
                <th class="th-header" style="width: 45px;">No</th>
                <th class="th-header" style="width: 140px;">No Invoice</th>
                <th class="th-header" style="width: 140px;">Tanggal Transaksi</th>
                <th class="th-header" style="width: 180px;">Merchant / Toko</th>
                <th class="th-header" style="width: 160px;">Nama Pembeli</th>
                <th class="th-header" style="width: 260px;">Item Produk &amp; Qty</th>
                <th class="th-header" style="width: 120px;">Status</th>
                <th class="th-header" style="width: 160px;">Gross Volume GMV (Rp)</th>
                <th class="th-header" style="width: 160px;">Komisi Platform 15% (Rp)</th>
                <th class="th-header" style="width: 160px;">Hak Penjual 85% (Rp)</th>
            </tr>
        </thead>

        <!-- Data Rows -->
        <tbody>
            @foreach($orders as $idx => $order)
            @php
                $fee = round($order->total_amount * 0.15);
                $sellerNet = $order->total_amount - $fee;
                $itemsList = $order->orderItems 
                    ? $order->orderItems->map(fn($it) => ($it->product->name ?? 'Produk') . " ({$it->quantity}x)")->join(', ')
                    : '-';
            @endphp
            <tr style="{{ $idx % 2 == 1 ? 'background-color: #F8FAFC;' : 'background-color: #FFFFFF;' }}">
                <td class="td-data td-center">{{ $idx + 1 }}</td>
                <td class="td-data td-center" style="font-weight: bold; mso-number-format: '\@';">{{ $order->invoice_number }}</td>
                <td class="td-data td-date">{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '-' }}</td>
                <td class="td-data">{{ $order->store->name ?? '-' }}</td>
                <td class="td-data">{{ $order->user->name ?? '-' }}</td>
                <td class="td-data">{{ $itemsList ?: '-' }}</td>
                <td class="td-data td-center" style="font-weight: bold;">{{ strtoupper($order->status ?? '-') }}</td>
                <td class="td-data td-right">{{ $order->total_amount }}</td>
                <td class="td-data td-right highlight-fee">{{ $fee }}</td>
                <td class="td-data td-right">{{ $sellerNet }}</td>
            </tr>
            @endforeach
        </tbody>

        <!-- Summary Row -->
        <tfoot>
            <tr>
                <td colspan="10" style="height: 10px; border: none;"></td>
            </tr>
            <tr>
                <td colspan="7" class="td-total" style="text-align: right; font-size: 10pt; text-transform: uppercase; letter-spacing: 0.5px;">
                    TOTAL KESELURUHAN LAPORAN:
                </td>
                <td class="td-total td-right" style="font-size: 10.5pt;">{{ $totalGMV }}</td>
                <td class="td-total td-right highlight-fee" style="font-size: 10.5pt;">{{ $totalPlatformFee }}</td>
                <td class="td-total td-right" style="font-size: 10.5pt;">{{ $totalSellerEarnings }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
