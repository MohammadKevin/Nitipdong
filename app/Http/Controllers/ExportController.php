<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Seller Sales CSV Export
     */
    public function sellerSalesReport(Request $request): StreamedResponse
    {
        $store = Auth::user()->store;
        if (!$store) {
            abort(403, 'Akses tidak sah.');
        }

        $fileName = 'laporan_penjualan_' . $store->slug . '_' . date('Y-m-d_His') . '.csv';

        $orders = Order::where('store_id', $store->id)
            ->with(['user', 'orderItems.product'])
            ->latest()
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            // Add BOM for UTF-8 Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Header
            fputcsv($handle, [
                'No Invoice',
                'Tanggal Pesanan',
                'Nama Pembeli',
                'Status Pesanan',
                'Daftar Produk & Qty',
                'Diskon Voucher (Rp)',
                'Total Transaksi (Rp)',
                'Komisi Platform 5% (Rp)',
                'Pendapatan Bersih Toko (Rp)',
                'No Resi Pengiriman'
            ]);

            foreach ($orders as $order) {
                $itemsSummary = $order->orderItems->map(function ($it) {
                    return ($it->product ? $it->product->name : 'Produk') . " ({$it->quantity}x @ Rp " . number_format($it->price, 0, ',', '.') . ")";
                })->join('; ');

                $isCompleted = $order->status === 'completed';
                $platformFee = $isCompleted ? round($order->total_amount * 0.05) : 0;
                $netEarnings = $isCompleted ? ($order->total_amount - $platformFee) : 0;

                fputcsv($handle, [
                    $order->invoice_number,
                    $order->created_at->format('d/m/Y H:i'),
                    $order->user ? $order->user->name : 'N/A',
                    strtoupper($order->status),
                    $itemsSummary,
                    $order->discount_amount,
                    $order->total_amount,
                    $platformFee,
                    $netEarnings,
                    $order->tracking_number ?? '-'
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Super Admin Reports Interactive Web Page
     */
    public function superAdminReportsPage(Request $request)
    {
        $parsed = $this->buildReportQuery($request);
        $query = $parsed['query'];
        $startDate = $parsed['startDate'];
        $endDate = $parsed['endDate'];
        $period = $parsed['period'];
        $status = $parsed['status'];
        $storeId = $parsed['storeId'];
        $search = $parsed['search'];

        // Aggregated Metrics
        $totalOrdersCount = (clone $query)->count();
        $totalGrossRevenue = (float) (clone $query)->sum('total_amount');
        $totalPlatformFee = round($totalGrossRevenue * 0.05);
        $totalSellerEarnings = $totalGrossRevenue - $totalPlatformFee;

        // Paginated records for table view
        $orders = $query->with(['store', 'user', 'orderItems.product'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stores = Store::where('status', 'approved')->orderBy('name')->get();

        return view('super_admin.reports.index', compact(
            'orders',
            'stores',
            'period',
            'startDate',
            'endDate',
            'status',
            'storeId',
            'search',
            'totalOrdersCount',
            'totalGrossRevenue',
            'totalPlatformFee',
            'totalSellerEarnings'
        ));
    }

    /**
     * Super Admin Revenue Report Export (Formatted Excel .xls & CSV)
     */
    public function superAdminRevenueReport(Request $request)
    {
        $parsed = $this->buildReportQuery($request);
        $query = $parsed['query'];
        $startDate = $parsed['startDate'];
        $endDate = $parsed['endDate'];
        $period = $parsed['period'];
        $format = $request->query('format', 'excel');

        $orders = $query->with(['store', 'user', 'orderItems.product'])->latest()->get();

        $periodLabel = ($startDate && $endDate)
            ? Carbon::parse($startDate)->format('d-m-Y') . '_sd_' . Carbon::parse($endDate)->format('d-m-Y') 
            : 'semua_waktu';

        $periodText = ($startDate && $endDate)
            ? Carbon::parse($startDate)->translatedFormat('d F Y') . ' s/d ' . Carbon::parse($endDate)->translatedFormat('d F Y')
            : 'Semua Waktu Historis';

        $totalGMV = (float) $orders->sum('total_amount');
        $totalPlatformFee = round($totalGMV * 0.05);
        $totalSellerEarnings = $totalGMV - $totalPlatformFee;

        // 1. FORMAT: EXCEL SPREADSHEET (.xls) with multi-column styles & gridlines
        if ($format === 'excel' || $format === 'xls') {
            $fileName = 'laporan_keuangan_nitipdong_' . $periodLabel . '_' . date('His') . '.xls';

            $headers = [
                'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
                'Pragma'              => 'no-cache',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Expires'             => '0',
            ];

            $content = view('super_admin.reports.excel_template', compact(
                'orders',
                'startDate',
                'endDate',
                'periodText',
                'totalGMV',
                'totalPlatformFee',
                'totalSellerEarnings'
            ))->render();

            return response($content, 200, $headers);
        }

        // 2. FORMAT: RAW CSV (.csv) with UTF-8 BOM and standard delimiter
        $fileName = 'laporan_keuangan_nitipdong_' . $periodLabel . '_' . date('His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($orders, $startDate, $endDate, $periodText) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            // Delimiter hint for Microsoft Excel
            fwrite($handle, "sep=,\r\n");

            // Data Table Headers
            fputcsv($handle, [
                'No',
                'No Invoice',
                'Tanggal Transaksi',
                'Merchant / Toko',
                'Nama Pembeli',
                'Status Pesanan',
                'Item Produk Terjual',
                'Gross Volume GMV (Rp)',
                'Laba Komisi Platform 5% (Rp)',
                'Hak Pembayaran Toko 95% (Rp)'
            ]);

            $totalGMV = 0;
            $totalPlatformFee = 0;
            $totalSellerShare = 0;
            $rowNum = 1;

            foreach ($orders as $order) {
                $platformFee = round($order->total_amount * 0.05);
                $sellerShare = $order->total_amount - $platformFee;

                $totalGMV += $order->total_amount;
                $totalPlatformFee += $platformFee;
                $totalSellerShare += $sellerShare;

                $itemsList = $order->orderItems 
                    ? $order->orderItems->map(fn($it) => ($it->product->name ?? 'Produk') . " ({$it->quantity}x)")->join('; ')
                    : '-';

                fputcsv($handle, [
                    $rowNum++,
                    $order->invoice_number,
                    $order->created_at ? $order->created_at->format('d/m/Y H:i') : '-',
                    $order->store->name ?? 'N/A',
                    $order->user->name ?? 'N/A',
                    strtoupper($order->status ?? 'N/A'),
                    $itemsList,
                    $order->total_amount,
                    $platformFee,
                    $sellerShare
                ]);
            }

            // Summary Footer Row
            fputcsv($handle, []);
            fputcsv($handle, [
                'TOTAL KESELURUHAN',
                '',
                '',
                '',
                '',
                '',
                '',
                $totalGMV,
                $totalPlatformFee,
                $totalSellerShare
            ]);

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Printable / PDF View
     */
    public function superAdminPrintReport(Request $request)
    {
        $parsed = $this->buildReportQuery($request);
        $query = $parsed['query'];
        $startDate = $parsed['startDate'];
        $endDate = $parsed['endDate'];
        $period = $parsed['period'];

        $orders = $query->with(['store', 'user', 'orderItems.product'])->latest()->get();

        $totalGMV = (float) $orders->sum('total_amount');
        $totalPlatformFee = round($totalGMV * 0.05);
        $totalSellerEarnings = $totalGMV - $totalPlatformFee;
        $totalOrders = $orders->count();

        return view('super_admin.reports.print', compact(
            'orders',
            'startDate',
            'endDate',
            'period',
            'totalGMV',
            'totalPlatformFee',
            'totalSellerEarnings',
            'totalOrders'
        ));
    }

    /**
     * Helper to parse and build standard report query
     */
    private function buildReportQuery(Request $request): array
    {
        $period = $request->query('period', 'this_month');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $status = $request->query('status', 'completed');
        $storeId = $request->query('store_id');
        $search = $request->query('search');

        // Resolve dates based on preset period
        if ($period === 'today') {
            $startDate = now()->startOfDay()->format('Y-m-d');
            $endDate = now()->endOfDay()->format('Y-m-d');
        } elseif ($period === '7days') {
            $startDate = now()->subDays(6)->startOfDay()->format('Y-m-d');
            $endDate = now()->endOfDay()->format('Y-m-d');
        } elseif ($period === 'this_month') {
            $startDate = now()->startOfMonth()->format('Y-m-d');
            $endDate = now()->endOfMonth()->format('Y-m-d');
        } elseif ($period === 'last_month') {
            $startDate = now()->subMonth()->startOfMonth()->format('Y-m-d');
            $endDate = now()->subMonth()->endOfMonth()->format('Y-m-d');
        } elseif ($period === 'this_year') {
            $startDate = now()->startOfYear()->format('Y-m-d');
            $endDate = now()->endOfYear()->format('Y-m-d');
        } elseif ($period === 'all') {
            $startDate = null;
            $endDate = null;
        }

        $query = Order::query();

        // Status Filter
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Store Filter
        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        // Date Filter
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        } elseif ($startDate) {
            $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        } elseif ($endDate) {
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        // Search Filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                  ->orWhereHas('store', fn($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        return [
            'query'     => $query,
            'period'    => $period,
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'status'    => $status,
            'storeId'   => $storeId,
            'search'    => $search,
        ];
    }
}
