<x-app-layout>
    <div class="page-container py-5">
        <nav class="flex text-xs text-slate-400 mb-4 items-center gap-1.5 flex-wrap" aria-label="Breadcrumb">
            <a href="{{ auth()->check() ? url('/?is_from_login=true') : url('/') }}" class="hover:text-cyan-700 transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <a href="{{ route('customer.dashboard') }}" class="hover:text-cyan-700 transition-colors">Akun Saya</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <span class="text-slate-700 font-medium">Wishlist & Produk Favorit</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-heart text-rose-500 text-lg"></i>
                    Wishlist Saya
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Simpan barang impianmu dan beli kapan saja saat promo tiba</p>
            </div>
            <span class="text-xs font-semibold px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200 w-fit">
                Total {{ $wishlists->total() }} Produk
            </span>
        </div>

        @if(session('success'))
            <div class="mb-5 flex items-center gap-2.5 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl text-xs font-semibold">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($wishlists->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-4 sm:gap-5">
                @foreach($wishlists as $wishlist)
                    @php $product = $wishlist->product; @endphp
                    @if($product)
                    <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden shadow-card hover:shadow-lg transition-all duration-200 flex flex-col group relative">
                        {{-- Remove button top right --}}
                        <form action="{{ route('customer.wishlist.destroy', $wishlist) }}" method="POST" class="absolute top-2.5 right-2.5 z-10">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Hapus dari wishlist"
                                    class="w-7 h-7 rounded-full bg-white/90 backdrop-blur-sm border border-slate-200 text-rose-500 hover:bg-rose-500 hover:text-white flex items-center justify-center text-xs shadow-xs transition-all">
                                <i class="fa-solid fa-trash-can text-[11px]"></i>
                            </button>
                        </form>

                        {{-- Product Image --}}
                        <a href="{{ route('product.show', $product) }}" class="relative aspect-square bg-slate-50 overflow-hidden block">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300 text-3xl">
                                    <i class="fa-solid fa-box"></i>
                                </div>
                            @endif

                            @if($product->is_in_flash_sale)
                                <span class="absolute bottom-2 left-2 px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-900 text-cyan-200 flex items-center gap-1 border border-cyan-700/50">
                                    <i class="fa-solid fa-bolt text-cyan-400 text-[9px]"></i> Flash Sale
                                </span>
                            @elseif($product->has_discount)
                                <span class="absolute bottom-2 left-2 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-600 text-white">
                                    -{{ $product->discount_percentage_effective }}%
                                </span>
                            @endif
                        </a>

                        {{-- Product Info --}}
                        <div class="p-3.5 flex-1 flex flex-col justify-between space-y-3">
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase font-semibold tracking-wider block truncate">
                                    {{ $product->category->name ?? 'Kategori' }}
                                </span>
                                <a href="{{ route('product.show', $product) }}" class="text-xs font-semibold text-slate-800 group-hover:text-cyan-700 transition-colors line-clamp-2 mt-0.5">
                                    {{ $product->name }}
                                </a>

                                <div class="mt-2 flex items-baseline gap-1.5 flex-wrap">
                                    <span class="text-sm font-extrabold text-slate-900">
                                        Rp {{ number_format($product->final_price, 0, ',', '.') }}
                                    </span>
                                    @if($product->has_discount)
                                        <span class="text-[10px] text-slate-400 line-through">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 mt-2 text-[11px] text-slate-500">
                                    <div class="flex items-center gap-1 text-amber-500 font-bold">
                                        <i class="fa-solid fa-star text-[10px]"></i>
                                        <span>{{ number_format($product->rating ?? 5.0, 1) }}</span>
                                    </div>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-[10px] truncate text-slate-400">
                                        <i class="fa-solid fa-store text-cyan-600 text-[9px]"></i> {{ $product->store->name ?? 'Toko' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="pt-2 border-t border-slate-100 flex gap-2">
                                <form action="{{ route('customer.cart.store', $product) }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" @if($product->stock <= 0) disabled @endif
                                            class="w-full h-8 rounded-lg bg-cyan-700 hover:bg-cyan-800 text-white text-[11px] font-semibold transition-colors flex items-center justify-center gap-1.5 shadow-xs disabled:opacity-50 disabled:cursor-not-allowed">
                                        <i class="fa-solid fa-cart-plus text-[10px]"></i>
                                        <span>+ Keranjang</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>

            <div class="mt-6">
                {{ $wishlists->links() }}
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center shadow-card max-w-lg mx-auto">
                <div class="w-16 h-16 rounded-full bg-rose-50 border border-rose-100 text-rose-500 flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fa-regular fa-heart"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800">Wishlist Masih Kosong</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                    Belum ada barang impian yang kamu simpan. Temukan ribuan produk menarik dan klik ikon hati untuk menyimpannya!
                </p>
                <a href="{{ url('/products') }}" class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white text-xs font-semibold shadow-sm transition-all">
                    <i class="fa-solid fa-bag-shopping"></i>
                    Jelajahi Produk Sekarang
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
