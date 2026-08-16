<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Konfirmasi Pesanan & Checkout') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('customer.order.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                @csrf

                <!-- Form Alamat Pengiriman -->
                <div class="lg:col-span-2 bg-white rounded-3xl p-8 border border-slate-100 shadow-sm space-y-6">
                    <h3 class="font-bold text-slate-800 text-lg">Informasi Pengiriman</h3>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Nama Penerima</label>
                        <input type="text" value="{{ auth()->user()->name }}" disabled class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Alamat Lengkap Pengiriman</label>
                        <textarea name="shipping_address" rows="4" required class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm" placeholder="Nama Jalan, RT/RW, Nomor Rumah, Kelurahan, Kecamatan, Kota, Kode Pos...">{{ old('shipping_address', auth()->user()->address) }}</textarea>
                        @error('shipping_address') <span class="text-rose-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Ringkasan Pembayaran -->
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm h-fit space-y-6">
                    <h3 class="font-bold text-slate-800 text-lg">Ringkasan Pembayaran</h3>

                    <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto space-y-3">
                        @foreach ($carts as $cart)
                            <div class="flex justify-between items-center text-sm pt-2">
                                <div>
                                    <p class="font-medium text-slate-800">{{ $cart->product->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $cart->quantity }}x @ Rp {{ number_format($cart->product->price, 0, ',', '.') }}</p>
                                </div>
                                <span class="font-semibold text-slate-700">Rp {{ number_format($cart->product->price * $cart->quantity, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                        <span class="font-bold text-slate-800">Total Pembayaran</span>
                        <span class="font-extrabold text-emerald-600 text-lg">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-sm shadow-sm transition-colors">
                        Bayar & Buat Pesanan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>