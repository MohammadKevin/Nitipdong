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
                                <span class="text-slate-600">{{ $product->sold_count ?? rand(50, 500) }}+</span>
                            </div>
                            <span class="text-slate-300">•</span>
                            <div class="flex items-center gap-1 text-amber-500">
                                <i class="fa-solid fa-star text-[10px]"></i>
                                <span class="font-bold text-slate-900">{{ number_format($product->rating ?? 5.0, 1) }}</span>
                                <span class="text-slate-500">({{ $product->reviews->count() }} rating)</span>
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
                            }
                        @else
                            getVariantString() { return ''; }
                        @endif
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

                            <div class="flex gap-3 mt-4">
                                <form action="{{ route('customer.cart.store', $product) }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="quantity" :value="qty">
                                    <input type="hidden" name="variant" :value="getVariantString()">
                                    <button type="submit" class="w-full h-10 rounded-xl border border-cyan-700 text-cyan-800 font-bold text-xs hover:bg-cyan-50/80 transition-all flex items-center justify-center gap-2 shadow-2xs cursor-pointer">
                                        <i class="fa-solid fa-cart-plus text-xs"></i>
                                        <span>+ Keranjang</span>
                                    </button>
                                </form>
                                <form action="{{ route('customer.cart.store', $product) }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="quantity" :value="qty">
                                    <input type="hidden" name="variant" :value="getVariantString()">
                                    <input type="hidden" name="action" value="buy">
                                    <button type="submit" class="w-full h-10 rounded-xl bg-cyan-700 text-white font-bold text-xs hover:bg-cyan-800 transition-all flex items-center justify-center gap-2 shadow-xs cursor-pointer">
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
                    <div class="flex items-center gap-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($product->store->name ?? 'Official Store') }}&background=0891b2&color=fff"
                             class="w-14 h-14 rounded-lg border-2 border-slate-200" alt="Store">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-slate-900">{{ $product->store->name ?? 'Official Store' }}</h3>
                                <i class="fa-solid fa-certificate text-xs text-cyan-600" title="Verified"></i>
                            </div>
                            <div class="flex items-center gap-3 text-xs text-slate-500 mt-1">
                                <div class="flex items-center gap-1">
                                    <i class="fa-solid fa-star text-amber-400 text-[10px]"></i>
                                    <span class="font-semibold">4.8</span>
                                    <span>(37,2 rb)</span>
                                </div>
                                <span class="text-slate-300">•</span>
                                <span class="flex items-center gap-1">
                                    <i class="fa-solid fa-box text-slate-400 text-[10px]"></i>
                                    100 total barang
                                </span>
                            </div>
                        </div>
                        <button class="px-4 py-2 rounded-lg border-2 border-emerald-500 text-emerald-700 font-semibold text-sm hover:bg-emerald-50 transition-colors">
                            Follow
                        </button>
                    </div>
                </div>

                {{-- Detail Produk --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <h3 class="text-base font-bold text-emerald-600 mb-4 pb-3 border-b-2 border-emerald-600">Detail Produk</h3>

                    @if($product->specifications)
                        <div class="space-y-2 text-sm">
                            @foreach($product->specifications as $key => $value)
                                @if(is_array($value))
                                    <div>
                                        <span class="text-slate-500">{{ $key }}:</span>
                                        <div class="ml-4 mt-1 space-y-1">
                                            @foreach($value as $item)
                                                <div class="text-slate-700">* {{ $item }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="flex">
                                        <span class="text-slate-500 w-32">{{ $key }}:</span>
                                        <span class="font-semibold text-emerald-600 flex-1">{{ $value }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="space-y-2 text-sm">
                            <div class="flex">
                                <span class="text-slate-500 w-32">Kondisi:</span>
                                <span class="font-semibold text-emerald-600">Baru</span>
                            </div>
                            <div class="flex">
                                <span class="text-slate-500 w-32">Min. Beli:</span>
                                <span class="font-semibold text-emerald-600">1 Buah</span>
                            </div>
                            <div class="flex">
                                <span class="text-slate-500 w-32">Kategori:</span>
                                <span class="font-semibold text-emerald-600">{{ $product->category->name ?? '-' }}</span>
                            </div>
                            <div class="flex">
                                <span class="text-slate-500 w-32">Etalase:</span>
                                <span class="font-semibold text-emerald-600">Semua Etalase</span>
                            </div>
                        </div>
                    @endif

                    @if($product->description)
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line">{{ $product->description }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
