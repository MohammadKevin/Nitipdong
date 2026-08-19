<x-app-layout>
    <div class="page-container py-5" x-data="{
        showReviewModal: false,
        reviewData: {
            orderId: null,
            orderItemId: null,
            productName: '',
            productImage: '',
            rating: 5,
            comment: '',
            is_anonymous: false,
            actionUrl: ''
        },
        hoverRating: 0,
        openReview(orderId, itemId, name, img, actionUrl) {
            this.reviewData = {
                orderId: orderId,
                orderItemId: itemId,
                productName: name,
                productImage: img,
                rating: 5,
                comment: '',
                is_anonymous: false,
                actionUrl: actionUrl
            };
            this.hoverRating = 5;
            this.showReviewModal = true;
        }
    }">
        <div class="mb-4">
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Akun & Riwayat Belanja</h1>
            <p class="text-xs text-slate-400 mt-0.5">Kelola data profil, alamat, dan status pemesanan Anda di BelanjaIn</p>
        </div>

        @if(session('success'))
            <div class="mb-4 flex items-center gap-2.5 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-lg text-xs font-semibold animate-fade-up">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 flex items-center gap-2.5 px-4 py-3 bg-rose-50 border border-rose-200 text-rose-900 rounded-lg text-xs font-semibold animate-fade-up">
                <i class="fa-solid fa-circle-xmark text-rose-600 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-5 items-start">
            <aside class="w-full lg:w-64 shrink-0 space-y-4">
                <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-card text-center">
                    <div class="relative w-16 h-16 mx-auto mb-3">
                        <img src="{{ auth()->user()->avatar_url }}"
                             class="w-full h-full rounded-2xl border-2 border-cyan-200 object-cover" alt="User">
                    </div>
                    <h3 class="font-bold text-sm text-slate-900 leading-tight">{{ auth()->user()->name }}</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5 truncate">{{ auth()->user()->email }}</p>
                    <div class="mt-3 inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-cyan-50 text-cyan-800 text-[10px] font-semibold border border-cyan-200">
                        <i class="fa-solid fa-user-check text-cyan-600 text-[9px]"></i> Pembeli Terverifikasi
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200/80 p-3.5 shadow-card space-y-1 text-xs">
                    <a href="{{ route('customer.dashboard') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-semibold text-cyan-800 bg-cyan-50 transition-colors">
                        <i class="fa-solid fa-receipt text-cyan-700 w-4"></i>
                        Riwayat Belanja
                    </a>
                    <a href="{{ route('customer.wishlist.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-slate-700 hover:bg-slate-50 hover:text-cyan-700 transition-colors">
                        <i class="fa-regular fa-heart text-rose-500 w-4"></i>
                        Wishlist Saya
                    </a>
                    <a href="{{ route('customer.addresses.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-slate-700 hover:bg-slate-50 hover:text-cyan-700 transition-colors">
                        <i class="fa-solid fa-location-dot text-cyan-600 w-4"></i>
                        Buku Alamat
                    </a>
                    @if(!$userStore)
                        <a href="{{ route('store.register') }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-slate-700 hover:bg-slate-50 hover:text-cyan-700 transition-colors">
                            <i class="fa-solid fa-store text-cyan-700 w-4"></i>
                            Buka Toko Gratis
                        </a>
                    @elseif($userStore->status === 'approved')
                        <a href="{{ route('seller.dashboard') }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-slate-700 hover:bg-slate-50 hover:text-cyan-700 transition-colors">
                            <i class="fa-solid fa-store text-cyan-700 w-4"></i>
                            Seller Center Toko
                        </a>
                    @else
                        <div class="px-3 py-2 rounded-lg bg-amber-50 text-amber-800 text-[11px] font-medium border border-amber-200">
                            Status Toko: <span class="font-bold uppercase">{{ $userStore->status }}</span>
                        </div>
                    @endif
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium text-slate-700 hover:bg-slate-50 hover:text-cyan-700 transition-colors">
                        <i class="fa-solid fa-user-gear text-slate-400 w-4"></i>
                        Pengaturan Profil
                    </a>
                </div>
            </aside>

            <div class="flex-1 min-w-0 space-y-4">
                <div class="bg-white rounded-xl border border-slate-200/80 p-5 sm:p-6 shadow-card">
                    <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4">
                        <h2 class="font-bold text-xs uppercase tracking-wider text-slate-900">Riwayat Pesanan Belanja</h2>
                        <span class="text-xs text-slate-400">Total {{ $orders->count() }} transaksi</span>
                    </div>

                    @if($orders->count() > 0)
                        <div class="space-y-4">
                            @foreach($orders as $order)
                            <div class="border border-slate-200 rounded-lg overflow-hidden shadow-xs">
                                <div class="px-4 py-2.5 bg-slate-50/80 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-receipt text-slate-400 text-xs"></i>
                                        <span class="font-mono font-bold text-slate-800">#{{ $order->invoice_number }}</span>
                                        <span class="text-slate-400 text-[11px]">• {{ $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                                    </div>
                                    @php
                                        $statusBadge = [
                                            'pending'    => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'processing' => 'bg-cyan-50 text-cyan-800 border-cyan-200',
                                            'shipped'    => 'bg-purple-50 text-purple-700 border-purple-200',
                                            'completed'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'cancelled'  => 'bg-rose-50 text-rose-700 border-rose-200',
                                        ];
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        @if($order->tracking_number)
                                            <span class="text-[11px] font-mono text-slate-500 bg-white px-2 py-0.5 rounded border border-slate-200">
                                                Resi: {{ $order->tracking_number }}
                                            </span>
                                        @endif
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border w-fit {{ $statusBadge[$order->status] ?? 'bg-slate-100 text-slate-600' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="p-4 space-y-3">
                                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-700 pb-2.5 border-b border-slate-100">
                                        <i class="fa-solid fa-store text-cyan-700 text-xs"></i>
                                        <span>{{ $order->store->name ?? 'Official Store BelanjaIn' }}</span>
                                    </div>

                                    @foreach($order->orderItems as $item)
                                    <div class="flex items-center gap-3.5 py-1">
                                        <div class="w-12 h-12 rounded-md bg-slate-100 border border-slate-200 overflow-hidden shrink-0">
                                            @if($item->product && $item->product->image_url)
                                                <img src="{{ $item->product->image_url }}" class="w-full h-full object-cover" alt="Product">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-300 text-base">
                                                    <i class="fa-solid fa-box"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs sm:text-sm font-medium text-slate-800 line-clamp-1">
                                                {{ $item->product ? $item->product->name : 'Produk Tidak Tersedia' }}
                                            </p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">
                                                {{ $item->quantity }} unit x Rp {{ number_format($item->price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                        <div class="text-right flex flex-col items-end gap-1">
                                            <span class="font-bold text-xs sm:text-sm text-slate-900">
                                                Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                            </span>

                                            {{-- Review Action or Status --}}
                                            @if($order->status === 'completed')
                                                @php $review = $item->review; @endphp
                                                @if($review)
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                                        <i class="fa-solid fa-star text-[9px]"></i> {{ $review->rating }}/5 (Diulas)
                                                    </span>
                                                @else
                                                    <button type="button"
                                                            @click="openReview({{ $order->id }}, {{ $item->id }}, '{{ addslashes($item->product?->name ?? 'Produk') }}', '{{ $item->product?->image_url }}', '{{ route('customer.reviews.store', $order) }}')"
                                                            class="text-[11px] font-semibold text-cyan-700 hover:text-cyan-800 bg-cyan-50 hover:bg-cyan-100 px-2.5 py-1 rounded-md border border-cyan-200 transition-colors flex items-center gap-1">
                                                        <i class="fa-solid fa-star text-amber-500 text-[10px]"></i> Beri Ulasan
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2.5 text-xs">
                                    <div>
                                        <span class="text-slate-500">Total Pembayaran:</span>
                                        <span class="font-bold text-slate-900 text-sm ml-1">
                                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        @if($order->status === 'pending')
                                            <a href="{{ route('customer.order.payment', $order) }}"
                                               class="btn-primary text-xs h-7.5 px-3.5 flex items-center gap-1.5 bg-cyan-700 hover:bg-cyan-800">
                                                <i class="fa-solid fa-credit-card text-[10px]"></i>
                                                Bayar Sekarang
                                            </a>
                                        @elseif($order->status === 'shipped')
                                            <form action="{{ route('customer.order.confirm_received', $order) }}" method="POST" onsubmit="return confirm('Apakah pesanan sudah Anda terima dalam kondisi baik?')">
                                                @csrf
                                                <button type="submit"
                                                        class="h-7.5 px-3.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs flex items-center gap-1.5 shadow-xs transition-colors">
                                                    <i class="fa-solid fa-circle-check text-[11px]"></i>
                                                    Pesanan Diterima
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-14 text-center text-slate-400">
                            <i class="fa-solid fa-bag-shopping text-3xl mb-2 text-slate-300"></i>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-700">Belum Ada Riwayat Belanja</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Semua riwayat pemesanan Anda akan tercatat rapi di sini.</p>
                            <a href="{{ url('/products') }}" class="mt-4 inline-block btn-primary text-xs h-8.5 px-4.5 bg-cyan-700 hover:bg-cyan-800">
                                Mulai Belanja Sekarang
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Modal Ulasan Produk --}}
        <div x-show="showReviewModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="showReviewModal = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                    <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-star text-amber-500"></i> Berikan Ulasan Produk
                    </h3>
                    <button @click="showReviewModal = false" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form :action="reviewData.actionUrl" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4 text-xs">
                    @csrf
                    <input type="hidden" name="order_item_id" :value="reviewData.orderItemId">
                    <input type="hidden" name="rating" :value="reviewData.rating">

                    {{-- Product summary --}}
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <img :src="reviewData.productImage || 'https://via.placeholder.com/60'"
                             class="w-11 h-11 rounded-lg object-cover border border-slate-200 bg-white" alt="Product">
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 truncate" x-text="reviewData.productName"></p>
                            <p class="text-[10px] text-slate-400">Bagaimana kualitas produk ini secara keseluruhan?</p>
                        </div>
                    </div>

                    {{-- Star Rating Picker --}}
                    <div class="text-center py-2">
                        <label class="block font-semibold text-slate-700 mb-2">Penilaian Bintang</label>
                        <div class="flex items-center justify-center gap-2 text-2xl cursor-pointer">
                            <template x-for="i in 5">
                                <i class="fa-star transition-transform hover:scale-125"
                                   :class="(hoverRating >= i || (!hoverRating && reviewData.rating >= i)) ? 'fa-solid text-amber-400' : 'fa-regular text-slate-300'"
                                   @mouseenter="hoverRating = i"
                                   @mouseleave="hoverRating = reviewData.rating"
                                   @click="reviewData.rating = i; hoverRating = i"></i>
                            </template>
                        </div>
                        <span class="text-[11px] font-bold text-slate-600 block mt-1.5"
                              x-text="['Sangat Buruk', 'Buruk', 'Cukup', 'Puas', 'Sangat Puas!'][reviewData.rating - 1]"></span>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Ceritakan Pengalamanmu (Opsional)</label>
                        <textarea name="comment" x-model="reviewData.comment" rows="3"
                                  placeholder="Ceritakan kualitas bahan, kesesuaian produk, kecepatan pengiriman, atau respon penjual..."
                                  class="w-full rounded-xl border border-slate-300 text-xs p-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500"></textarea>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Foto Produk (Opsional, Maks 5 Foto)</label>
                        <input type="file" name="images[]" multiple accept="image/*"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                    </div>

                    <label class="flex items-center gap-2 cursor-pointer pt-1">
                        <input type="checkbox" name="is_anonymous" value="1" x-model="reviewData.is_anonymous"
                               class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                        <span class="text-slate-600 font-medium">Sembunyikan nama saya (Ulasan Anonim)</span>
                    </label>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="showReviewModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-semibold shadow-xs">
                            Kirim Ulasan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
