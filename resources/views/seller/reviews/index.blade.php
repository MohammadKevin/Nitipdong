<x-app-layout>
    <div class="page-container py-5">
        <nav class="flex text-xs text-slate-400 mb-4 items-center gap-1.5 flex-wrap" aria-label="Breadcrumb">
            <a href="/" class="hover:text-cyan-700 transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <a href="{{ route('seller.dashboard') }}" class="hover:text-cyan-700 transition-colors">Seller Center</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <span class="text-slate-700 font-medium">Ulasan & Rating Pembeli</span>
        </nav>

        <div class="flex flex-col lg:flex-row gap-6 items-start">
            {{-- Seller Sidebar --}}
            @include('seller.sidebar')

            <div class="flex-1 min-w-0 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                            <i class="fa-solid fa-star text-amber-400"></i>
                            Ulasan & Penilaian Pembeli
                        </h1>
                        <p class="text-xs text-slate-400 mt-0.5">Kelola dan tanggapi ulasan dari pembeli untuk meningkatkan kepuasan pelanggan</p>
                    </div>
                    <span class="text-xs font-semibold px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200 w-fit">
                        Total {{ $reviews->total() }} Ulasan
                    </span>
                </div>

                @if(session('success'))
                    <div class="flex items-center gap-2.5 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl text-xs font-semibold">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if($reviews->count() > 0)
                    <div class="space-y-4">
                        @foreach($reviews as $rev)
                            <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-card space-y-4"
                                 x-data="{ showReplyForm: false }">
                                {{-- Top: Product Info --}}
                                <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-100 flex-wrap">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 overflow-hidden shrink-0">
                                            @if($rev->product && $rev->product->image_url)
                                                <img src="{{ $rev->product->image_url }}" class="w-full h-full object-cover" alt="Product">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                    <i class="fa-solid fa-box text-xs"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('product.show', $rev->product) }}" target="_blank"
                                               class="text-xs font-bold text-slate-900 hover:text-cyan-700 transition-colors truncate block">
                                                {{ $rev->product->name ?? 'Produk' }}
                                            </a>
                                            <span class="text-[10px] text-slate-400">Order #{{ $rev->order->invoice_number ?? '-' }}</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center text-amber-400 text-xs">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fa-solid fa-star {{ $i <= $rev->rating ? 'text-amber-400' : 'text-slate-200' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="text-xs font-bold text-slate-800">({{ $rev->rating }}.0)</span>
                                    </div>
                                </div>

                                {{-- Middle: Customer Review --}}
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2 text-xs">
                                        <span class="font-bold text-slate-800">
                                            @if($rev->is_anonymous)
                                                {{ Str::mask($rev->user->name ?? 'User', '*', 1, -1) }} (Anonim)
                                            @else
                                                {{ $rev->user->name ?? 'Pembeli' }}
                                            @endif
                                        </span>
                                        <span class="text-slate-300">•</span>
                                        <span class="text-slate-400 text-[11px]">{{ $rev->created_at->translatedFormat('d M Y, H:i') }}</span>
                                    </div>

                                    @if($rev->comment)
                                        <p class="text-xs text-slate-700 leading-relaxed bg-slate-50 p-3 rounded-lg border border-slate-100">
                                            {{ $rev->comment }}
                                        </p>
                                    @endif

                                    @if(!empty($rev->images) && is_array($rev->images))
                                        <div class="flex gap-2 pt-1">
                                            @foreach($rev->images as $img)
                                                <a href="{{ asset('storage/' . $img) }}" target="_blank" class="w-14 h-14 rounded-lg overflow-hidden border border-slate-200 bg-slate-50 hover:opacity-90">
                                                    <img src="{{ asset('storage/' . $img) }}" class="w-full h-full object-cover" alt="Foto">
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                {{-- Bottom: Seller Reply Section --}}
                                @if($rev->seller_reply)
                                    <div class="p-3.5 rounded-xl bg-cyan-50/50 border border-cyan-200/70 text-xs space-y-1.5">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-cyan-900 flex items-center gap-1 text-[11px]">
                                                <i class="fa-solid fa-reply text-cyan-600"></i> Balasan Toko Anda:
                                            </span>
                                            <button @click="showReplyForm = !showReplyForm" class="text-[10px] text-cyan-700 hover:underline font-semibold">
                                                Ubah Balasan
                                            </button>
                                        </div>
                                        <p class="text-slate-700 text-xs leading-relaxed">
                                            {{ $rev->seller_reply }}
                                        </p>
                                    </div>
                                @endif

                                {{-- Reply Form (Toggled or if no reply exists) --}}
                                <div x-show="showReplyForm || {{ $rev->seller_reply ? 'false' : 'true' }}" x-cloak
                                     class="pt-2 border-t border-slate-100">
                                    <form action="{{ route('seller.reviews.reply', $rev) }}" method="POST" class="space-y-2 text-xs">
                                        @csrf
                                        <label class="block font-semibold text-slate-700">
                                            {{ $rev->seller_reply ? 'Perbarui Balasan Anda:' : 'Tanggapi Ulasan Pembeli Ini:' }}
                                        </label>
                                        <textarea name="seller_reply" rows="2" required
                                                  placeholder="Tulis ucapan terima kasih atau solusi untuk pembeli..."
                                                  class="w-full rounded-xl border border-slate-300 text-xs p-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">{{ $rev->seller_reply }}</textarea>
                                        <div class="flex justify-end gap-2">
                                            @if($rev->seller_reply)
                                                <button type="button" @click="showReplyForm = false" class="px-3 py-1.5 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 font-medium text-xs">
                                                    Tutup
                                                </button>
                                            @endif
                                            <button type="submit" class="px-4 py-1.5 rounded-lg bg-cyan-700 hover:bg-cyan-800 text-white font-semibold text-xs shadow-xs">
                                                {{ $rev->seller_reply ? 'Simpan Perubahan' : 'Kirim Balasan' }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5">
                        {{ $reviews->links() }}
                    </div>
                @else
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center shadow-card">
                        <div class="w-16 h-16 rounded-full bg-amber-50 border border-amber-100 text-amber-500 flex items-center justify-center mx-auto mb-4 text-2xl">
                            <i class="fa-regular fa-star"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-800">Belum Ada Ulasan Pembeli</h3>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                            Ulasan dari pelanggan yang telah menyelesaikan pesanan pada tokomu akan tampil di sini.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
