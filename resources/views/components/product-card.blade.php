@props(['product'])

<div class="bg-white rounded-2xl border border-slate-200/70 overflow-hidden shadow-card hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group relative">

    {{-- Discount Badge - Top Left (Exact Match to User Reference) --}}
    @if($product->has_discount)
        <div class="absolute top-2 left-2 z-10 pointer-events-none">
            <span class="bg-[#ff3b5c] text-white text-[11px] font-black px-2 py-0.5 rounded-full shadow-xs tracking-tight inline-flex items-center">
                {{ $product->discount_percentage_effective }}%
            </span>
        </div>
    @endif

    {{-- Wishlist Heart Button - Top Right --}}
    @auth
        @if(auth()->user()->role === 'customer')
            @php $isWish = $product->isWishlistedBy(auth()->user()); @endphp
            <form action="{{ route('customer.wishlist.toggle', $product) }}" method="POST" class="absolute top-2 right-2 z-10">
                @csrf
                <button type="submit" title="{{ $isWish ? 'Hapus Wishlist' : 'Tambah Wishlist' }}"
                        class="w-7 h-7 rounded-full bg-white/90 backdrop-blur-xs border border-slate-200/80 flex items-center justify-center text-xs transition-transform active:scale-90 shadow-2xs {{ $isWish ? 'text-rose-600' : 'text-slate-400 hover:text-rose-600' }}">
                    <i class="{{ $isWish ? 'fa-solid text-rose-600' : 'fa-regular' }} fa-heart"></i>
                </button>
            </form>
        @endif
    @endauth

    {{-- Product Image --}}
    <a href="{{ route('product.show', $product) }}" class="relative aspect-square bg-slate-50 overflow-hidden block">
        @if($product->image_url)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                 loading="lazy"
                 onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center text-slate-300 text-3xl\'><i class=\'fa-solid fa-bag-shopping\'></i></div>';">
        @else
            <div class="w-full h-full flex items-center justify-center text-slate-300 text-3xl">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
        @endif
    </a>

    {{-- Product Details / Info --}}
    <div class="p-3 flex-1 flex flex-col justify-between gap-1.5">
        <div>
            {{-- Title with 2-line ellipsis clamp --}}
            <a href="{{ route('product.show', $product) }}"
               class="text-xs sm:text-[13px] font-medium text-slate-900 group-hover:text-cyan-700 transition-colors line-clamp-2 leading-snug min-h-[2.3rem]"
               title="{{ $product->name }}">
                {{ $product->name }}
            </a>

            {{-- Voucher / Price Badge (Exact Match: Pink border with Ticket Icon & Price) --}}
            <div class="mt-1.5 flex items-center">
                <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-[#fff0f3] border border-[#ffccd5] text-[#ff3366] font-bold text-xs sm:text-sm tracking-tight shadow-2xs">
                    <svg class="w-3.5 h-3.5 fill-current shrink-0" viewBox="0 0 24 24">
                        <path d="M4 4c-1.11 0-2 .89-2 2v4c1.1 0 2 .9 2 2s-.9 2-2 2v4c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2v-4c-1.1 0-2-.9-2-2s.9-2 2-2V6c0-1.11-.89-2-2-2H4zm5 3h6v2H9V7zm0 4h6v2H9v-2zm0 4h6v2H9v-2z"/>
                    </svg>
                    <span>Rp{{ number_format($product->final_price, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Bonus / Cashback Subtext (Orange) --}}
            <div class="mt-1.5">
                <span class="text-[11px] sm:text-xs font-bold text-[#ff6600] tracking-tight block truncate">
                    Hemat s.d {{ $product->has_discount ? $product->discount_percentage_effective : 8 }}% Pakai Bonus
                </span>
            </div>
        </div>

        <div>
            {{-- Rating & Sales Row --}}
            <div class="mt-1 flex items-center gap-1 text-[11px] sm:text-xs text-slate-600 font-medium">
                @if($product->effective_rating > 0)
                    <i class="fa-solid fa-star text-amber-400 text-xs"></i>
                    <span class="font-bold text-slate-800">{{ number_format($product->effective_rating, 1) }}</span>
                    <span class="text-slate-300">•</span>
                @endif
                <span class="text-slate-500">{{ $product->formatted_sold_count }} terjual</span>
            </div>

            {{-- Location & Options Row --}}
            <div class="mt-1.5 pt-1.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                <span class="truncate pr-1" title="{{ $product->store_city }}">{{ $product->store_city }}</span>
                <span class="text-slate-300 group-hover:text-slate-500 transition-colors text-xs shrink-0 cursor-pointer">
                    <i class="fa-solid fa-ellipsis"></i>
                </span>
            </div>
        </div>
    </div>
</div>
