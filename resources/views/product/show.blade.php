<x-app-layout>
    <div class="page-container py-5">
        <nav class="flex text-xs text-slate-400 mb-4 items-center gap-1.5 flex-wrap" aria-label="Breadcrumb">
            <a href="{{ url('/') }}" class="hover:text-cyan-700 transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <a href="{{ url('/products') }}" class="hover:text-cyan-700 transition-colors">Katalog</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <a href="{{ url('/products?category='.($product->category->slug ?? '')) }}" class="hover:text-cyan-700 transition-colors">
                {{ $product->category->name ?? 'Kategori' }}
            </a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <span class="text-slate-700 font-medium truncate max-w-xs">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
            {{-- Left: Product Image Gallery --}}
            <div class="lg:col-span-5">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sticky top-20">
                    <div class="relative w-full aspect-square rounded-lg overflow-hidden bg-slate-50 border border-slate-200 mb-3">
                        @php
                            $allImages = array_filter(array_merge(
                                $product->image ? [$product->image] : [],
                                $product->images ?? []
                            ));
                        @endphp

                        @if(count($allImages))
                            @php
                                $firstImageUrl = str_starts_with($allImages[0], 'img/')
                                    ? asset($allImages[0])
                                    : asset('storage/' . $allImages[0]);
                            @endphp
                            <img src="{{ $firstImageUrl }}"
                                 class="w-full h-full object-cover"
                                 alt="{{ $product->name }}"
                                 id="main-pdp-img">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300 text-4xl">
                                <i class="fa-solid fa-box"></i>
                            </div>
                        @endif

                        {{-- Discount Badge --}}
                        @if($product->has_discount)
                            <div class="absolute top-0 left-0 bg-rose-600 text-white px-2 py-1.5 text-sm font-black leading-none rounded-br-lg">
                                {{ $product->discount_percentage_effective }}%
                            </div>
                        @endif

                        {{-- Official Badge --}}
                        <div class="absolute top-2 right-2 px-2 py-0.5 rounded-full text-[9px] font-semibold bg-emerald-500 text-white border border-emerald-300 shadow-sm">
                            Resmi
                        </div>
                    </div>

                    {{-- Thumbnail Gallery --}}
                    @if(count($allImages) > 1)
                    <div class="flex gap-2 overflow-x-auto pb-1">
                        @foreach($allImages as $i => $imgPath)
                        @php
                            $thumbUrl = str_starts_with($imgPath, 'img/')
                                ? asset($imgPath)
                                : asset('storage/' . $imgPath);
                        @endphp
                        <button type="button"
                            onclick="document.getElementById('main-pdp-img').src='{{ $thumbUrl }}'; document.querySelectorAll('.pdp-thumb').forEach(el => el.classList.remove('ring-2','ring-cyan-600')); this.classList.add('ring-2','ring-cyan-600');"
                            class="pdp-thumb shrink-0 w-14 h-14 rounded-md overflow-hidden border border-slate-200 bg-white {{ $i === 0 ? 'ring-2 ring-cyan-600' : '' }}">
                            <img src="{{ $thumbUrl }}" class="w-full h-full object-cover" alt="Foto {{ $i+1 }}">
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- Right: Product Info --}}
            <div class="lg:col-span-7 space-y-5">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
                    <div>
                        <h1 class="text-lg sm:text-xl font-bold text-slate-900 leading-tight">
                            {{ $product->name }}
                        </h1>

                        <div class="flex items-center gap-3 text-xs text-slate-500 mt-2">
                            <div class="flex items-center gap-1">
                                <span class="text-slate-900 font-bold">Terjual</span>
                                <span class="text-slate-700 font-semibold">{{ $product->formatted_sold_count }}</span>
                            </div>
                            <span class="text-slate-300">•</span>
                            <div class="flex items-center gap-1 text-amber-500">
                                <i class="fa-solid fa-star text-[10px]"></i>
                                <span class="font-bold text-slate-900">{{ number_format($product->rating, 1) }}</span>
                                @if($product->reviews->count() > 0)
                                    <span class="text-slate-500">({{ $product->reviews->count() }} ulasan)</span>
                                @else
                                    <span class="text-slate-400 font-medium">(Belum ada ulasan)</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Price --}}
                    <div class="pt-3 border-t border-slate-100">
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-bold text-rose-600">
                                Rp{{ number_format($product->final_price, 0, ',', '.') }}
                            </span>
                            @if($product->has_discount)
                                <span class="text-sm text-slate-400 line-through">
                                    Rp{{ number_format($product->price, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>

                        @if($product->has_discount)
                        <div class="mt-2">
                            <span class="inline-block text-[10px] font-bold text-orange-600 bg-orange-50 px-2 py-1 rounded">
                                Hemat s.d {{ $product->discount_percentage_effective }}% Pakai Bonus
                            </span>
                        </div>
                        @endif
                    </div>

                    {{-- Interactive Variants & Purchase Panel --}}
                    <div x-data="{
                        qty: 1,
                        stock: {{ $product->stock }},
                        price: {{ $product->final_price }},
                        isAddingToCart: false,
                        @if($product->variants && count($product->variants) > 0)
                            @foreach($product->variants as $variantIndex => $variant)
                                selected{{ $variantIndex }}: '{{ $variant['options'][0] ?? '' }}',
                            @endforeach
                            getVariantString() {
                                let parts = [];
                                @foreach($product->variants as $variantIndex => $variant)
                                    if (this.selected{{ $variantIndex }}) {
                                        parts.push('{{ $variant['name'] }}: ' + this.selected{{ $variantIndex }});
                                    }
                                @endforeach
                                return parts.join(', ');
                            },
                        @else
                            getVariantString() { return ''; },
                        @endif
                        async addToCartAnimated(e) {
                            e.preventDefault();
                            if (this.isAddingToCart) return;
                            this.isAddingToCart = true;

                            // Parabolic Fly-to-Cart Animation
                            const productImg = document.getElementById('main-pdp-img');
                            const cartBtn = document.getElementById('nav-cart-btn');

                            if (productImg && cartBtn) {
                                const startRect = productImg.getBoundingClientRect();
                                const endRect = cartBtn.getBoundingClientRect();

                                const flyer = document.createElement('img');
                                flyer.src = productImg.src || '{{ $product->image_url }}';
                                flyer.style.position = 'fixed';
                                flyer.style.zIndex = '999999';
                                flyer.style.width = '70px';
                                flyer.style.height = '70px';
                                flyer.style.borderRadius = '1rem';
                                flyer.style.objectFit = 'cover';
                                flyer.style.boxShadow = '0 20px 25px -5px rgba(8, 145, 178, 0.4)';
                                flyer.style.border = '2px solid #0891b2';
                                flyer.style.pointerEvents = 'none';
                                flyer.style.top = `${startRect.top}px`;
                                flyer.style.left = `${startRect.left}px`;
                                document.body.appendChild(flyer);

                                const deltaX = (endRect.left + endRect.width / 2) - (startRect.left + startRect.width / 2);
                                const deltaY = (endRect.top + endRect.height / 2) - (startRect.top + startRect.height / 2);

                                const anim = flyer.animate([
                                    {
                                        top: `${startRect.top}px`,
                                        left: `${startRect.left}px`,
                                        width: '70px',
                                        height: '70px',
                                        opacity: 1,
                                        transform: 'scale(1) rotate(0deg)'
                                    },
                                    {
                                        top: `${Math.min(startRect.top, endRect.top) - 50}px`,
                                        left: `${startRect.left + deltaX * 0.45}px`,
                                        width: '45px',
                                        height: '45px',
                                        opacity: 0.9,
                                        transform: 'scale(0.8) rotate(-15deg)'
                                    },
                                    {
                                        top: `${endRect.top}px`,
                                        left: `${endRect.left}px`,
                                        width: '18px',
                                        height: '18px',
                                        opacity: 0.2,
                                        transform: 'scale(0.3) rotate(25deg)'
                                    }
                                ], {
                                    duration: 750,
                                    easing: 'cubic-bezier(0.2, 0.8, 0.25, 1)',
                                    fill: 'forwards'
                                });

                                anim.onfinish = () => flyer.remove();
                            }

                            // Submit to Cart via AJAX
                            try {
                                const formData = new FormData();
                                formData.append('_token', '{{ csrf_token() }}');
                                formData.append('quantity', this.qty);
                                formData.append('variant', this.getVariantString());

                                const response = await fetch('{{ route('customer.cart.store', $product) }}', {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    }
                                });

                                if (response.status === 401) {
                                    const data = await response.json();
                                    window.dispatchEvent(new CustomEvent('notify', {
                                        detail: { title: 'Perhatian', message: data.message || 'Silakan login terlebih dahulu.', type: 'error' }
                                    }));
                                    setTimeout(() => window.location.href = '{{ route('login') }}', 1200);
                                    return;
                                }

                                const data = await response.json();
                                if (response.ok && data.status === 'success') {
                                    window.dispatchEvent(new CustomEvent('cart-updated', {
                                        detail: { count: data.cart_count }
                                    }));
                                    window.dispatchEvent(new CustomEvent('notify', {
                                        detail: { title: 'Berhasil Masuk Keranjang', message: `${this.qty}x {{ addslashes($product->name) }} berhasil ditambahkan!`, type: 'success' }
                                    }));
                                } else {
                                    window.dispatchEvent(new CustomEvent('notify', {
                                        detail: { title: 'Gagal', message: data.message || 'Gagal menambahkan produk.', type: 'error' }
                                    }));
                                }
                            } catch (err) {
                                console.error(err);
                            } finally {
                                this.isAddingToCart = false;
                            }
                        }
                    }" class="space-y-4">

                        @if($product->variants && count($product->variants) > 0)
                        <div class="space-y-3.5 pb-2">
                            @foreach($product->variants as $variantIndex => $variant)
                            <div>
                                <div class="flex items-center justify-between text-xs font-semibold text-slate-700 mb-2">
                                    <span>Pilih {{ $variant['name'] }}:</span>
                                    <span x-text="selected{{ $variantIndex }}" class="text-cyan-700 font-bold"></span>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($variant['options'] as $option)
                                    <button type="button"
                                            @click="selected{{ $variantIndex }} = '{{ $option }}'"
                                            :class="selected{{ $variantIndex }} === '{{ $option }}' ? 'border-cyan-600 bg-cyan-50/80 text-cyan-800 ring-1 ring-cyan-600 font-bold' : 'border-slate-200 bg-white hover:border-slate-300 text-slate-700 font-medium'"
                                            class="px-3.5 py-1.5 rounded-lg border text-xs transition-all cursor-pointer shadow-2xs">
                                        <span>{{ $option }}</span>
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- Quantity & Actions --}}
                        <div class="pt-3 border-t border-slate-100">
                            <label class="block text-xs font-semibold text-slate-700 mb-2 uppercase tracking-wider">Jumlah Pembelian</label>
                            <div class="flex items-center gap-3">
                                <div class="inline-flex items-center rounded-lg border border-slate-300 bg-white shadow-2xs overflow-hidden">
                                    <button type="button" @click="if(qty > 1) qty--" class="w-8 h-8 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors">
                                        <i class="fa-solid fa-minus text-[10px]"></i>
                                    </button>
                                    <input type="number" x-model.number="qty" min="1" :max="stock" class="w-12 text-center text-xs font-bold border-none focus:ring-0 p-0 h-8">
                                    <button type="button" @click="if(qty < stock) qty++" class="w-8 h-8 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors">
                                        <i class="fa-solid fa-plus text-[10px]"></i>
                                    </button>
                                </div>
                                <span class="text-xs text-slate-500">
                                    Tersisa <strong class="text-slate-900">{{ $product->stock }}</strong> unit
                                </span>
                            </div>

                            <div class="flex items-center gap-2.5 mt-4">
                                {{-- Wishlist Toggle Button --}}
                                @auth
                                    @if(auth()->user()->role === 'customer')
                                        @php $isWish = $product->isWishlistedBy(auth()->user()); @endphp
                                        <div x-data="{ isWish: {{ $isWish ? 'true' : 'false' }}, isToggling: false, bounce: false }" class="shrink-0">
                                            <button type="button"
                                                    @click.prevent.stop="
                                                        if(isToggling) return;
                                                        isToggling = true;
                                                        bounce = true;
                                                        fetch('{{ route('customer.wishlist.toggle', $product) }}', {
                                                            method: 'POST',
                                                            headers: {
                                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                                'Accept': 'application/json',
                                                                'X-Requested-With': 'XMLHttpRequest'
                                                            }
                                                        })
                                                        .then(res => res.json())
                                                        .then(data => {
                                                            isWish = data.is_wishlisted;
                                                            window.dispatchEvent(new CustomEvent('wishlist-updated', { detail: data }));
                                                            window.dispatchEvent(new CustomEvent('notify', {
                                                                detail: {
                                                                    title: data.is_wishlisted ? 'Ditambahkan ke Wishlist' : 'Dihapus dari Wishlist',
                                                                    message: data.message,
                                                                    type: data.is_wishlisted ? 'success' : 'info'
                                                                }
                                                            }));
                                                        })
                                                        .catch(err => console.error(err))
                                                        .finally(() => {
                                                            isToggling = false;
                                                            setTimeout(() => bounce = false, 600);
                                                        });
                                                    "
                                                    :title="isWish ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist'"
                                                    class="w-11 h-11 rounded-xl border flex items-center justify-center text-sm transition-all shadow-2xs cursor-pointer"
                                                    :class="[
                                                        isWish ? 'border-rose-300 bg-rose-50 text-rose-600 shadow-rose-100' : 'border-slate-300 text-slate-500 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50/40',
                                                        bounce ? 'scale-125 ring-2 ring-rose-200' : ''
                                                    ]">
                                                <i class="fa-heart text-base" :class="isWish ? 'fa-solid text-rose-600' : 'fa-regular'"></i>
                                            </button>
                                        </div>
                                    @endif
                                @endauth

                                <button type="button"
                                        @click.prevent.stop="addToCartAnimated($event)"
                                        onclick="event.preventDefault(); event.stopPropagation();"
                                        :disabled="isAddingToCart"
                                        class="flex-1 h-11 rounded-xl border border-cyan-700 text-cyan-800 font-bold text-xs hover:bg-cyan-50/80 active:scale-95 transition-all flex items-center justify-center gap-2 shadow-2xs cursor-pointer disabled:opacity-50">
                                    <i class="fa-solid" :class="isAddingToCart ? 'fa-spinner animate-spin text-xs' : 'fa-cart-plus text-xs'"></i>
                                    <span x-text="isAddingToCart ? 'Menambahkan...' : '+ Keranjang'">+ Keranjang</span>
                                </button>
                                <form action="{{ route('customer.cart.store', $product) }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="quantity" :value="qty">
                                    <input type="hidden" name="variant" :value="getVariantString()">
                                    <input type="hidden" name="action" value="buy">
                                    <button type="submit" class="w-full h-11 rounded-xl bg-cyan-700 text-white font-bold text-xs hover:bg-cyan-800 active:scale-95 transition-all flex items-center justify-center gap-2 shadow-xs cursor-pointer">
                                        <i class="fa-solid fa-bolt text-xs"></i>
                                        <span>Beli Sekarang</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Store Profile --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <div class="flex items-center justify-between gap-4 flex-wrap">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <a href="{{ $product->store ? route('store.show', $product->store->slug) : '#' }}" class="relative block shrink-0">
                                <img src="{{ $product->store ? $product->store->logo_url : 'https://ui-avatars.com/api/?name=Store&background=0891b2&color=fff' }}"
                                     class="w-12 h-12 rounded-xl border border-slate-200 object-cover shadow-2xs" alt="Store">
                            </a>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <a href="{{ $product->store ? route('store.show', $product->store->slug) : '#' }}" class="text-xs font-extrabold text-slate-900 hover:text-cyan-700 truncate">
                                        {{ $product->store->name ?? 'Official Store' }}
                                    </a>
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.2 bg-cyan-50 text-cyan-800 text-[9px] font-bold rounded border border-cyan-200">
                                        <i class="fa-solid fa-certificate text-[8px] text-cyan-600"></i> Official
                                    </span>
                                </div>
                                <div class="flex items-center gap-2.5 text-[11px] text-slate-500 mt-1 flex-wrap">
                                    <span class="flex items-center gap-1 font-medium text-slate-700">
                                        <i class="fa-solid fa-location-dot text-cyan-600 text-[10px]"></i>
                                        <span>Dikirim dari: <strong>{{ $product->store->city ?: ($product->store->effective_city ?: 'Jakarta Pusat') }}</strong></span>
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded bg-emerald-50 text-emerald-800 text-[10px] font-bold border border-emerald-200">
                                        <i class="fa-solid fa-truck-fast text-[9px] text-emerald-600"></i> Gratis Ongkir 1 Kota
                                    </span>
                                </div>
                            </div>
                        </div>
                        @if($product->store)
                            <div class="flex items-center gap-2">
                                @auth
                                    <button type="button"
                                            @click="$dispatch('open-chat', { receiver_id: {{ $product->store->user_id ?? 1 }}, receiver_name: '{{ addslashes($product->store->name) }}' })"
                                            class="btn-secondary text-xs h-8.5 px-3 rounded-xl border-cyan-200 text-cyan-800 bg-cyan-50/80 hover:bg-cyan-100 hover:border-cyan-400 font-bold flex items-center gap-1.5 shadow-2xs cursor-pointer">
                                        <i class="fa-regular fa-comment-dots text-xs text-cyan-700"></i>
                                        <span>Chat Penjual</span>
                                    </button>
                                @else
                                    <a href="{{ route('login') }}" class="btn-secondary text-xs h-8.5 px-3 rounded-xl border-cyan-200 text-cyan-800 bg-cyan-50/80 hover:bg-cyan-100 hover:border-cyan-400 font-bold flex items-center gap-1.5 shadow-2xs">
                                        <i class="fa-regular fa-comment-dots text-xs text-cyan-700"></i>
                                        <span>Chat Penjual</span>
                                    </a>
                                @endauth
                                <a href="{{ route('store.show', $product->store->slug) }}" class="btn-secondary text-xs h-8.5 px-3.5 rounded-xl border-slate-300 hover:border-cyan-600 hover:text-cyan-700 font-semibold flex items-center gap-1.5 shadow-2xs">
                                    <i class="fa-solid fa-store text-xs"></i> Kunjungi Toko
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Tabbed Section: Detail, Ulasan & Diskusi --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-card overflow-hidden"
                     x-data="{
                        activeTab: 'detail',
                        replyToId: null,
                        showPhotoModal: false,
                        activePhoto: ''
                     }">
                    {{-- Tab Navigation Bar --}}
                    <div class="flex border-b border-slate-200 px-5 gap-6 text-xs bg-slate-50/50">
                        <button type="button" @click="activeTab = 'detail'"
                                :class="activeTab === 'detail' ? 'border-cyan-700 text-cyan-800 font-extrabold border-b-2' : 'text-slate-500 hover:text-slate-800 font-semibold'"
                                class="py-3.5 transition-all flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-circle-info text-xs"></i>
                            <span>Detail & Spesifikasi</span>
                        </button>
                        <button type="button" @click="activeTab = 'reviews'"
                                :class="activeTab === 'reviews' ? 'border-cyan-700 text-cyan-800 font-extrabold border-b-2' : 'text-slate-500 hover:text-slate-800 font-semibold'"
                                class="py-3.5 transition-all flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-star text-amber-500 text-xs"></i>
                            <span>Ulasan Pembeli ({{ $product->reviews->count() }})</span>
                        </button>
                        <button type="button" @click="activeTab = 'discussions'"
                                :class="activeTab === 'discussions' ? 'border-cyan-700 text-cyan-800 font-extrabold border-b-2' : 'text-slate-500 hover:text-slate-800 font-semibold'"
                                class="py-3.5 transition-all flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-comments text-cyan-600 text-xs"></i>
                            <span>Diskusi & Tanya Jawab ({{ $product->discussions->count() }})</span>
                        </button>
                    </div>

                    <div class="p-5 sm:p-6">
                        {{-- 1. TAB DETAIL PRODUK --}}
                        <div x-show="activeTab === 'detail'" class="space-y-4">
                            @if($product->specifications)
                                <div class="space-y-2.5 text-xs">
                                    @foreach($product->specifications as $key => $value)
                                        @if(is_array($value))
                                            <div>
                                                <span class="text-slate-400 font-semibold uppercase tracking-wider">{{ $key }}:</span>
                                                <div class="ml-4 mt-1 space-y-1">
                                                    @foreach($value as $item)
                                                        <div class="text-slate-700 flex items-center gap-1.5">
                                                            <i class="fa-solid fa-check text-cyan-600 text-[10px]"></i>
                                                            <span>{{ $item }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-center">
                                                <span class="text-slate-400 w-36 font-semibold">{{ $key }}:</span>
                                                <span class="font-bold text-slate-800 flex-1">{{ $value }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs bg-slate-50 p-4 rounded-xl border border-slate-100">
                                    <div>
                                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Kondisi</span>
                                        <span class="font-bold text-slate-800">Baru (Original)</span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Min. Beli</span>
                                        <span class="font-bold text-slate-800">1 Unit</span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Kategori</span>
                                        <span class="font-bold text-cyan-800">{{ $product->category->name ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Garansi Toko</span>
                                        <span class="font-bold text-emerald-700">7 Hari Retur</span>
                                    </div>
                                </div>
                            @endif

                            @if($product->description)
                            <div class="mt-4 pt-4 border-t border-slate-100">
                                <h4 class="text-xs font-bold text-slate-900 mb-2 uppercase tracking-wider">Deskripsi Lengkap</h4>
                                <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line">{{ $product->description }}</p>
                            </div>
                            @endif
                        </div>

                        {{-- 2. TAB ULASAN PEMBELI & FOTO --}}
                        <div x-show="activeTab === 'reviews'" x-cloak class="space-y-5">
                            {{-- Rating Overview Header --}}
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 bg-gradient-to-r from-amber-50/50 to-orange-50/30 rounded-2xl border border-amber-200/60">
                                <div class="flex items-center gap-4">
                                    <div class="text-center bg-white px-5 py-3 rounded-xl border border-amber-200 shadow-xs">
                                        <span class="text-3xl font-black text-slate-900 leading-none">{{ number_format($product->rating ?? 5.0, 1) }}</span>
                                        <div class="flex items-center gap-0.5 text-amber-400 text-xs mt-1 justify-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fa-solid fa-star {{ $i <= round($product->rating ?? 5) ? '' : 'text-slate-200' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="text-[10px] text-slate-400 block mt-0.5">dari 5 bintang</span>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-sm text-slate-900">Ulasan Pembeli Terverifikasi</h4>
                                        <p class="text-xs text-slate-500 mt-0.5">Semua ulasan berasal dari transaksi yang telah selesai.</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-white text-cyan-800 font-bold text-xs rounded-xl border border-slate-200 self-start sm:self-auto shadow-2xs">
                                    {{ $product->reviews->count() }} Total Ulasan
                                </span>
                            </div>

                            {{-- Review Items List --}}
                            @if($product->reviews->count() > 0)
                                <div class="space-y-4 divide-y divide-slate-100">
                                    @foreach($product->reviews as $rev)
                                        <div class="pt-4 first:pt-0 space-y-2 text-xs">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-7 h-7 rounded-full bg-cyan-100 text-cyan-800 font-bold flex items-center justify-center text-[10px]">
                                                        {{ substr($rev->is_anonymous ? 'A' : ($rev->user->name ?? 'U'), 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <strong class="text-slate-900 font-semibold">{{ $rev->is_anonymous ? 'Pengguna Anonim' : ($rev->user->name ?? 'Pembeli') }}</strong>
                                                        <span class="text-[10px] text-emerald-600 font-medium ml-1.5"><i class="fa-solid fa-circle-check text-[9px]"></i> Pembeli Terverifikasi</span>
                                                    </div>
                                                </div>
                                                <span class="text-[10px] text-slate-400">{{ $rev->created_at->diffForHumans() }}</span>
                                            </div>

                                            <div class="flex items-center gap-1 text-amber-400 text-[11px]">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fa-solid fa-star {{ $i <= $rev->rating ? '' : 'text-slate-200' }}"></i>
                                                @endfor
                                            </div>

                                            @if($rev->comment)
                                                <p class="text-slate-700 leading-relaxed">{{ $rev->comment }}</p>
                                            @endif

                                            {{-- Attached Photos Gallery --}}
                                            @if($rev->images && is_array($rev->images) && count($rev->images) > 0)
                                                <div class="flex items-center gap-2 pt-1">
                                                    @foreach($rev->images as $img)
                                                        @php
                                                            $src = str_starts_with($img, 'http') ? $img : asset('storage/' . $img);
                                                        @endphp
                                                        <img src="{{ $src }}" alt="Foto Ulasan"
                                                             @click="activePhoto = '{{ $src }}'; showPhotoModal = true"
                                                             class="w-16 h-16 rounded-xl object-cover border border-slate-200 cursor-pointer hover:opacity-90 hover:scale-105 transition-all shadow-2xs">
                                                    @endforeach
                                                </div>
                                            @endif

                                            {{-- Seller Reply if any --}}
                                            @if($rev->seller_reply)
                                                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/80 ml-4 mt-2 space-y-1">
                                                    <div class="flex items-center gap-1.5 text-cyan-800 font-bold text-[11px]">
                                                        <i class="fa-solid fa-store text-cyan-600"></i>
                                                        <span>Respon Penjual:</span>
                                                    </div>
                                                    <p class="text-slate-600 text-xs">{{ $rev->seller_reply }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="py-10 text-center text-slate-400 text-xs">
                                    <i class="fa-regular fa-star text-3xl text-slate-300 mb-2 block"></i>
                                    <p>Belum ada ulasan untuk produk ini. Jadilah pembeli pertama yang memberikan ulasan!</p>
                                </div>
                            @endif
                        </div>

                        {{-- 3. TAB FORUM DISKUSI PRODUK (Q&A) --}}
                        <div x-show="activeTab === 'discussions'" x-cloak class="space-y-5">
                            {{-- Form Tanya Pertanyaan Baru --}}
                            @auth
                                <form action="{{ route('products.discussions.store', $product) }}" method="POST" class="p-4 bg-cyan-50/40 rounded-2xl border border-cyan-200/80 space-y-3">
                                    @csrf
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-circle-question text-cyan-700 text-sm"></i>
                                        <h4 class="font-bold text-xs text-cyan-950">Ada pertanyaan mengenai produk ini? Tanyakan langsung ke Penjual</h4>
                                    </div>
                                    <textarea name="body" rows="2" required placeholder="Contoh: Apakah barang ini ready warna hitam? Berapa estimasi pengirimannya?"
                                              class="w-full rounded-xl border border-slate-300 text-xs p-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500 bg-white"></textarea>
                                    <div class="flex justify-end">
                                        <button type="submit" class="btn-primary text-xs h-8 px-4 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-semibold flex items-center gap-1.5 shadow-2xs cursor-pointer">
                                            <i class="fa-solid fa-paper-plane text-[10px]"></i>
                                            <span>Kirim Pertanyaan</span>
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-center text-xs text-slate-600">
                                    <p>Ingin bertanya seputar produk ini? <a href="{{ route('login') }}" class="text-cyan-700 font-bold hover:underline">Masuk ke Akun Anda</a> terlebih dahulu.</p>
                                </div>
                            @endauth

                            {{-- Discussion Threads List --}}
                            @if($product->discussions->count() > 0)
                                <div class="space-y-4 divide-y divide-slate-100">
                                    @foreach($product->discussions as $disc)
                                        <div id="discussion-{{ $disc->id }}" class="pt-4 first:pt-0 space-y-2 text-xs">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-[10px]">
                                                        {{ substr($disc->user->name ?? 'P', 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <strong class="text-slate-900 font-semibold">{{ $disc->user->name ?? 'Calon Pembeli' }}</strong>
                                                        @if($disc->is_seller)
                                                            <span class="text-[9px] font-bold text-cyan-800 bg-cyan-100 px-1.5 py-0.2 rounded ml-1">Penjual</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="text-[10px] text-slate-400">{{ $disc->created_at->diffForHumans() }}</span>
                                            </div>

                                            <p class="text-slate-800 leading-relaxed pl-9 font-medium">{{ $disc->body }}</p>

                                            {{-- Replies List --}}
                                            @if($disc->replies->count() > 0)
                                                <div class="ml-9 mt-2 space-y-2 border-l-2 border-cyan-200 pl-3">
                                                    @foreach($disc->replies as $rep)
                                                        <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/70 space-y-1">
                                                            <div class="flex items-center justify-between">
                                                                <div class="flex items-center gap-1.5">
                                                                    <strong class="text-slate-900 font-bold text-[11px]">{{ $rep->user->name ?? 'Penjual' }}</strong>
                                                                    @if($rep->is_seller)
                                                                        <span class="text-[9px] font-bold text-cyan-800 bg-cyan-100 px-1.5 py-0.2 rounded">Toko Resmi</span>
                                                                    @endif
                                                                </div>
                                                                <span class="text-[9px] text-slate-400">{{ $rep->created_at->diffForHumans() }}</span>
                                                            </div>
                                                            <p class="text-slate-700 text-xs">{{ $rep->body }}</p>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            {{-- Reply Button / Form Trigger --}}
                                            @auth
                                                <div class="pl-9 pt-1">
                                                    <button type="button" @click="replyToId = replyToId === {{ $disc->id }} ? null : {{ $disc->id }}"
                                                            class="text-[11px] font-semibold text-cyan-700 hover:text-cyan-800 flex items-center gap-1 cursor-pointer">
                                                        <i class="fa-solid fa-reply text-[9px]"></i>
                                                        <span x-text="replyToId === {{ $disc->id }} ? 'Batal Balas' : 'Balas Pertanyaan'"></span>
                                                    </button>

                                                    <form x-show="replyToId === {{ $disc->id }}" x-cloak
                                                          action="{{ route('products.discussions.reply', [$product, $disc]) }}" method="POST"
                                                          class="mt-2 space-y-2 bg-white p-3 rounded-xl border border-slate-200 shadow-2xs">
                                                        @csrf
                                                        <input type="text" name="body" required placeholder="Tulis balasan Anda..."
                                                               class="w-full h-8 rounded-lg border border-slate-300 text-xs px-2.5 focus:border-cyan-600">
                                                        <div class="flex justify-end">
                                                            <button type="submit" class="btn-primary text-xs h-7 px-3 rounded-lg bg-cyan-700 hover:bg-cyan-800 text-white font-semibold">
                                                                Kirim Balasan
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endauth
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="py-10 text-center text-slate-400 text-xs">
                                    <i class="fa-regular fa-comments text-3xl text-slate-300 mb-2 block"></i>
                                    <p>Belum ada diskusi untuk produk ini. Ada hal yang ingin Anda tanyakan?</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Image Lightbox Modal --}}
                    <div x-show="showPhotoModal" x-cloak
                         class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4"
                         @click="showPhotoModal = false">
                        <div class="relative max-w-xl max-h-[85vh]">
                            <img :src="activePhoto" alt="Zoom Foto" class="max-w-full max-h-[85vh] rounded-2xl object-contain shadow-2xl">
                            <button type="button" @click="showPhotoModal = false" class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-white text-slate-900 font-bold flex items-center justify-center shadow-lg cursor-pointer">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
