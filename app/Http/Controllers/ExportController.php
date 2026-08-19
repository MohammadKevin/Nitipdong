<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
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

    public function superAdminRevenueReport(Request $request): StreamedResponse
    {
        $fileName = 'laporan_keuntungan_marketplace_' . date('Y-m-d_His') . '.csv';

        $orders = Order::where('status', 'completed')
            ->with(['store', 'user'])
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
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'No Invoice',
                'Tanggal Selesai',
                'Nama Toko',
                'Nama Pembeli',
                'Nilai Transaksi GMV (Rp)',
                'Laba Komisi Platform 5% (Rp)',
                'Bagian Penjual 95% (Rp)'
            ]);

            $totalGMV = 0;
            $totalPlatformFee = 0;

            foreach ($orders as $order) {
                $platformFee = round($order->total_amount * 0.05);
                $sellerShare = $order->total_amount - $platformFee;

                $totalGMV += $order->total_amount;
                $totalPlatformFee += $platformFee;

                fputcsv($handle, [
                    $order->invoice_number,
                    $order->completed_at ? $order->completed_at->format('d/m/Y H:i') : $order->updated_at->format('d/m/Y H:i'),
                    $order->store ? $order->store->name : 'N/A',
                    $order->user ? $order->user->name : 'N/A',
                    $order->total_amount,
                    $platformFee,
                    $sellerShare
                ]);
            }

            // Summary Row
            fputcsv($handle, []);
            fputcsv($handle, [
                'TOTAL KESELURUHAN',
                '',
                '',
                '',
                $totalGMV,
                $totalPlatformFee,
                $totalGMV - $totalPlatformFee
            ]);

            fclose($handle);
        }, 200, $headers);
    }
}
