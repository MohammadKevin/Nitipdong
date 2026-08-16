<x-app-layout>
    <div class="bg-[#f5f5f5] min-h-screen pb-32 pt-4">
        <div class="max-w-[1200px] mx-auto px-4">

            <!-- Form Start -->
            <form action="{{ route('customer.order.store') }}" method="POST">
                @csrf

                <!-- Address Section -->
                <div class="bg-white shadow-sm mb-4 relative overflow-hidden">
                    <!-- Repeating Striped Border -->
                    <div class="absolute top-0 left-0 w-full h-1" style="background-image: repeating-linear-gradient(45deg, #06b6d4, #06b6d4 33px, transparent 0, transparent 41px, #1c73db 0, #1c73db 74px, transparent 0, transparent 82px); background-size: 116px 3px;"></div>
                    
                    <div class="px-6 py-6 pt-8">
                        <h2 class="flex items-center gap-2 text-[#06b6d4] text-lg font-medium mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Alamat Pengiriman
                        </h2>
                        
                        <div class="flex items-start md:items-center gap-4 text-sm text-slate-800">
                            <div class="font-bold shrink-0">
                                {{ auth()->user()->name }}<br>
                                (+62) 812-3456-7890
                            </div>
                            <div class="flex-1 ml-4 border-l border-slate-200 pl-4 h-full flex flex-col justify-center">
                                <textarea name="shipping_address" rows="2" required class="w-full border-none focus:ring-1 focus:ring-[#06b6d4] bg-slate-50 rounded-sm text-sm p-2" placeholder="Masukkan alamat lengkap (Nama Jalan, RT/RW, Nomor Rumah, Kelurahan, Kecamatan, Kota, Kode Pos)...">{{ old('shipping_address', auth()->user()->address) }}</textarea>
                                @error('shipping_address') <span class="text-[#06b6d4] text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <a href="#" class="text-blue-600 font-medium ml-4 uppercase text-xs hover:text-blue-800 shrink-0">Ubah</a>
                        </div>
                    </div>
                </div>

                <!-- Products Table Header -->
                <div class="bg-white shadow-sm rounded-sm mb-4 px-6 py-4 flex items-center text-sm text-slate-500 font-medium">
                    <div class="w-[50%] text-slate-800 text-lg">Produk Dipesan</div>
                    <div class="w-[15%] text-center">Harga Satuan</div>
                    <div class="w-[15%] text-center">Jumlah</div>
                    <div class="w-[20%] text-right">Subtotal Produk</div>
                </div>

                <!-- Group by Store -->
                @php
                    $groupedCarts = $carts->groupBy(function($item) {
                        return $item->product->store->name ?? 'Toko';
                    });
                @endphp

                @foreach($groupedCarts as $storeName => $items)
                    <div class="bg-white shadow-sm rounded-sm mb-4">
                        <!-- Store Header -->
                        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                            <span class="bg-[#06b6d4] text-white text-[10px] font-bold px-1.5 py-0.5 rounded-sm">Mall</span>
                            <span class="font-medium text-slate-800">{{ $storeName }}</span>
                            <a href="#" class="text-blue-600 ml-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg></a>
                        </div>
                        
                        <!-- Items -->
                        @php $storeSubtotal = 0; @endphp
                        @foreach($items as $item)
                            @php $storeSubtotal += $item->product->price * $item->quantity; @endphp
                            <div class="px-6 py-4 border-b border-slate-50 flex items-center text-sm">
                                <div class="w-[50%] flex gap-3">
                                    <div class="w-12 h-12 bg-slate-100 shrink-0 border border-slate-200">
                                        @if($item->product->image)
                                            <img src="{{ asset('storage/' . $item->product->image) }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-slate-800 line-clamp-1 leading-snug">{{ $item->product->name }}</h3>
                                        <p class="text-xs text-slate-500 mt-1">Variasi: Default</p>
                                    </div>
                                </div>
                                <div class="w-[15%] text-center text-slate-800">Rp{{ number_format($item->product->price, 0, ',', '.') }}</div>
                                <div class="w-[15%] text-center text-slate-800">{{ $item->quantity }}</div>
                                <div class="w-[20%] text-right font-medium text-slate-800">Rp{{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</div>
                            </div>
                        @endforeach

                        <!-- Shipping & Message Info -->
                        <div class="bg-[#fbfcff] border-t border-slate-100 flex flex-col md:flex-row text-sm">
                            <div class="flex-1 border-r border-dashed border-slate-200 px-6 py-4 flex items-center gap-4">
                                <span class="text-slate-800">Pesan:</span>
                                <input type="text" placeholder="(Opsional) Tinggalkan pesan ke penjual" class="flex-1 border-slate-300 focus:border-[#06b6d4] focus:ring-[#06b6d4] rounded-sm text-sm">
                            </div>
                            <div class="flex-1 px-6 py-4">
                                <div class="flex justify-between items-start text-[#00bfa5] font-medium mb-1">
                                    <span>Opsi Pengiriman:</span>
                                    <a href="#" class="uppercase text-xs text-blue-600 hover:text-blue-800">Ubah</a>
                                </div>
                                <div class="flex justify-between font-medium text-slate-800">
                                    <span>Reguler</span>
                                    <span>Rp0</span>
                                </div>
                                <div class="text-xs text-slate-500 mt-1">Garansi tiba: 19 - 21 Agu</div>
                                <div class="text-xs text-[#00bfa5] mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                    Bebas Ongkir s/d Rp20.000
                                </div>
                            </div>
                        </div>

                        <!-- Store Subtotal -->
                        <div class="border-t border-slate-100 px-6 py-4 flex justify-end items-center gap-4 text-sm bg-white">
                            <span class="text-slate-500">Total Pesanan ({{ $items->sum('quantity') }} Produk):</span>
                            <span class="text-[#06b6d4] text-xl font-medium">Rp{{ number_format($storeSubtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach

                <!-- Payment Details Container -->
                <div class="bg-white shadow-sm rounded-sm mb-4 px-6 pt-6 pb-4">
                    <div class="flex items-center gap-4 text-sm border-b border-slate-100 pb-6 mb-4">
                        <span class="text-slate-800 font-medium w-48">Metode Pembayaran</span>
                        <div class="flex gap-2">
                            <button type="button" class="border border-[#06b6d4] text-[#06b6d4] rounded-sm px-4 py-1.5 font-medium relative overflow-hidden">
                                <div class="absolute bottom-0 right-0 w-3 h-3 bg-[#06b6d4] flex items-center justify-center [clip-path:polygon(100%_0,0_100%,100%_100%)]"><svg class="w-2 h-2 text-white translate-x-0.5 translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg></div>
                                Transfer Bank
                            </button>
                            <button type="button" class="border border-slate-300 text-slate-700 hover:border-[#06b6d4] hover:text-[#06b6d4] rounded-sm px-4 py-1.5 font-medium transition-colors">COD (Bayar di Tempat)</button>
                            <button type="button" class="border border-slate-300 text-slate-700 hover:border-[#06b6d4] hover:text-[#06b6d4] rounded-sm px-4 py-1.5 font-medium transition-colors">Kartu Kredit/Debit</button>
                        </div>
                    </div>

                    <!-- Summary Block -->
                    <div class="bg-[#fafdff] border border-[#e8f1f5] rounded-sm p-4 w-full md:w-96 ml-auto text-sm space-y-3">
                        <div class="flex justify-between items-center text-slate-600">
                            <span>Subtotal untuk Produk</span>
                            <span>Rp{{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-slate-600">
                            <span>Total Ongkos Kirim</span>
                            <span>Rp0</span>
                        </div>
                        <div class="flex justify-between items-center text-slate-600">
                            <span>Biaya Layanan</span>
                            <span>Rp1.000</span>
                        </div>
                        <div class="flex justify-between items-center text-slate-600">
                            <span>Biaya Penanganan</span>
                            <span>Rp0</span>
                        </div>
                        
                        <div class="border-t border-slate-200 pt-4 mt-2 flex justify-between items-center">
                            <span class="text-slate-800 font-medium">Total Pembayaran</span>
                            <span class="text-[#06b6d4] text-3xl font-medium">Rp{{ number_format($totalPrice + 1000, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="border-t border-slate-100 mt-6 pt-6 flex justify-between items-center">
                        <p class="text-xs text-slate-500 max-w-xl">
                            Dengan menekan "Buat Pesanan", Anda menyetujui <a href="#" class="text-blue-600">Syarat & Ketentuan</a> BelanjaIn.
                        </p>
                        <button type="submit" class="bg-[#06b6d4] hover:bg-[#0891b2] text-white rounded-sm px-12 py-3 font-medium transition-colors text-lg min-w-[200px] shadow-sm">
                            Buat Pesanan
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>