<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Pesanan Masuk Toko') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-slate-500 font-medium border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4">No Invoice & Tgl</th>
                                <th class="px-6 py-4">Pembeli</th>
                                <th class="px-6 py-4">Item Produk</th>
                                <th class="px-6 py-4">Total</th>
                                <th class="px-6 py-4">Status & Resi</th>
                                <th class="px-6 py-4 text-center">Update Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-slate-800">
                                        {{ $order->invoice_number }}<br>
                                        <span class="text-xs text-slate-400 font-normal">{{ $order->created_at->format('d M Y, H:i') }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $order->user->name }}<br>
                                        <span class="text-xs text-slate-400">{{ $order->shipping_address }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <ul class="text-xs space-y-1">
                                            @foreach($order->orderItems as $item)
                                                <li>• {{ $item->product->name }} ({{ $item->quantity }}x)</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-900">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($order->status === 'pending')
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">Pending</span>
                                        @elseif($order->status === 'processing')
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Diproses</span>
                                        @elseif($order->status === 'shipped')
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">Dikirim</span>
                                            @if($order->tracking_number)
                                                <div class="text-xs text-slate-500 mt-1">Resi: {{ $order->tracking_number }}</div>
                                            @endif
                                        @elseif($order->status === 'completed')
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">Selesai</span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800">Batal</span>
                                        @endif

                                        @if($order->payment_proof)
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank" class="inline-flex items-center gap-1 text-xs text-emerald-600 hover:text-emerald-700 font-medium">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                    Lihat Bukti Bayar
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <form action="{{ route('seller.orders.update_status', $order) }}" method="POST" class="flex flex-col gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="text-xs rounded-lg border-slate-200 py-1">
                                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Proses Pesanan</option>
                                                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Kirim Barang</option>
                                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Batalkan</option>
                                            </select>
                                            <input type="text" name="tracking_number" value="{{ $order->tracking_number }}" placeholder="No. Resi (opsional)" class="text-xs rounded-lg border-slate-200 py-1">
                                            <button type="submit" class="px-3 py-1 bg-slate-800 hover:bg-slate-900 text-white rounded text-xs">Simpan</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                        Belum ada pesanan masuk untuk toko Anda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($orders->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>