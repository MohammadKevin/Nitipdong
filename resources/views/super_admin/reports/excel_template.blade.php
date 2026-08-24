<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    {!! '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Laporan Keuangan</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->' !!}
    <style>
        body, table {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
        }
        .header-title {
            font-size: 15pt;
            font-weight: bold;
            color: #0F172A;
            height: 35px;
            vertical-align: middle;
        }
        .meta-label {
            font-size: 9pt;
            color: #64748B;
            font-weight: bold;
        }
        .meta-val {
            font-size: 9pt;
            color: #0F172A;
            font-weight: bold;
        }
        .th-header {
            background-color: #0F172A;
            color: #FFFFFF;
            font-weight: bold;
            font-size: 10pt;
            text-align: center;
            border: 1px solid #1E293B;
            height: 30px;
            vertical-align: middle;
        }
        .td-data {
            border: 1px solid #CBD5E1;
            padding: 6px;
            vertical-align: middle;
            font-size: 9.5pt;
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
            height: 32px;
            vertical-align: middle;
        }
        .highlight-fee {
            background-color: #ECFDF5;
            color: #065F46;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <table>
        <!-- Meta info -->
        <tr>
            <td colspan="10" class="header-title">LAPORAN KEUANGAN &amp; KOMISI PLATFORM NITIPDONG</td>
        </tr>
        <tr>
            <td colspan="2" class="meta-label">Tanggal Dibuat:</td>
            <td colspan="8" class="meta-val">{{ date('d F Y, H:i:s') }} WIB</td>
        </tr>
        <tr>
            <td colspan="2" class="meta-label">Periode Laporan:</td>
            <td colspan="8" class="meta-val">{{ $periodText ?? 'Semua Waktu Historis' }}</td>
        </tr>
        <tr>
            <td colspan="2" class="meta-label">Total Transaksi:</td>
            <td colspan="8" class="meta-val">{{ number_format($orders->count(), 0, ',', '.') }} Pesanan</td>
        </tr>
        <tr>
            <td colspan="10" style="height: 15px;"></td>
        </tr>

        <!-- Table Columns Header -->
        <tr>
            <th class="th-header" style="width: 45px;">No</th>
            <th class="th-header" style="width: 140px;">No Invoice</th>
            <th class="th-header" style="width: 130px;">Tanggal Transaksi</th>
            <th class="th-header" style="width: 180px;">Merchant / Toko</th>
            <th class="th-header" style="width: 150px;">Nama Pembeli</th>
            <th class="th-header" style="width: 250px;">Item Produk &amp; Qty</th>
            <th class="th-header" style="width: 110px;">Status</th>
            <th class="th-header" style="width: 150px;">Gross Volume GMV (Rp)</th>
            <th class="th-header" style="width: 150px;">Komisi Platform 5% (Rp)</th>
            <th class="th-header" style="width: 150px;">Hak Penjual 95% (Rp)</th>
        </tr>

        <!-- Data Rows -->
        @foreach($orders as $idx => $order)
        @php
            $fee = round($order->total_amount * 0.05);
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

        <!-- Summary Row -->
        <tr>
            <td colspan="10" style="height: 10px;"></td>
        </tr>
        <tr>
            <td colspan="7" class="td-total" style="text-align: right; font-size: 10pt; text-transform: uppercase; letter-spacing: 0.5px;">
                TOTAL KESELURUHAN LAPORAN:
            </td>
            <td class="td-total td-right" style="font-size: 10.5pt;">{{ $totalGMV }}</td>
            <td class="td-total td-right highlight-fee" style="font-size: 10.5pt;">{{ $totalPlatformFee }}</td>
            <td class="td-total td-right" style="font-size: 10.5pt;">{{ $totalSellerEarnings }}</td>
        </tr>
    </table>
</body>
</html>
