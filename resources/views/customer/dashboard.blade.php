<x-app-layout>
    <div class="page-container py-6" x-data="{
        activeTab: 'all',
        searchQuery: '',
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
        copiedInvoice: null,
        showComplaintModal: false,
        showCancelModal: false,
        cancelData: {
            invoiceNumber: '',
            reason: 'Ingin mengubah metode pembayaran / salah pesan',
            actionUrl: ''
        },
        complaintData: {
            orderId: null,
            invoiceNumber: '',
            storeName: '',
            reason: 'Barang Rusak / Cacat',
            description: '',
            actionUrl: ''
        },
        openCancelModal(invoice, actionUrl) {
            this.cancelData = {
                invoiceNumber: invoice,
                reason: 'Ingin mengubah metode pembayaran / salah pesan',
                actionUrl: actionUrl
            };
            this.showCancelModal = true;
        },
        copyToClipboard(text) {
            navigator.clipboard.writeText(text);
            this.copiedInvoice = text;
            setTimeout(() => {
                if (this.copiedInvoice === text) this.copiedInvoice = null;
            }, 2000);
        },
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
        },
        openComplaint(order, actionUrl) {
            this.complaintData = {
                orderId: order.id,
                invoiceNumber: order.invoice_number,
                storeName: order.store ? order.store.name : 'Toko',
                reason: 'Barang Rusak / Cacat',
                description: '',
                actionUrl: actionUrl
            };
            this.showComplaintModal = true;
        }
    }">
        {{-- Breadcrumb --}}
        <nav class="flex text-xs text-slate-400 mb-4 items-center gap-1.5 flex-wrap" aria-label="Breadcrumb">
            <a href="{{ auth()->check() ? url('/?is_from_login=true') : url('/') }}" class="hover:text-cyan-700 transition-colors flex items-center gap-1">
                <i class="fa-solid fa-house text-[10px]"></i> Beranda
            </a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <span class="text-slate-500 font-medium">Akun Saya</span>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <span class="text-slate-800 font-semibold">Dashboard & Riwayat Belanja</span>
        </nav>

        {{-- Page Title Banner --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs relative overflow-hidden">
            <div class="absolute -right-8 -top-8 w-32 h-32 bg-cyan-50 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyan-600"></span>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Akun & Riwayat Belanja</h1>
                </div>
                <p class="text-xs text-slate-500 mt-1">Pantau status pesanan, kelola alamat pengiriman, dan kelola akun belanja Anda di SakserShop</p>
            </div>
            <div class="flex items-center gap-2 relative z-10 shrink-0">
                <a href="{{ url('/products') }}" class="btn-primary text-xs h-9 px-4 rounded-xl bg-cyan-700 hover:bg-cyan-800 flex items-center gap-2 shadow-xs transition-all">
                    <i class="fa-solid fa-bag-shopping text-xs"></i>
                    <span>Jelajahi Produk</span>
                </a>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-5 flex items-center gap-3 px-4 py-3.5 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl text-xs font-semibold shadow-xs animate-fade-up">
                <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-check text-emerald-600 text-xs"></i>
                </div>
                <span class="flex-1">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-5 flex items-center gap-3 px-4 py-3.5 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl text-xs font-semibold shadow-xs animate-fade-up">
                <div class="w-6 h-6 rounded-full bg-rose-100 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-xmark text-rose-600 text-xs"></i>
                </div>
                <span class="flex-1">{{ session('error') }}</span>
            </div>
        @endif

        @php
            $pendingCount = $orders->where('status', 'pending')->count();
            $processingCount = $orders->where('status', 'processing')->count();
            $shippedCount = $orders->where('status', 'shipped')->count();
            $completedCount = $orders->where('status', 'completed')->count();
            $cancelledCount = $orders->where('status', 'cancelled')->count();
            $complaintCount = $orders->filter(fn($o) => $o->complaint !== null)->count();
            $totalOrdersCount = $orders->count();

            $wishlistCount = auth()->user()->wishlists()->count();
            $addressCount = auth()->user()->addresses()->count();
        @endphp

        <div class="flex flex-col lg:flex-row gap-6 items-start">
            {{-- Left Sidebar Cards --}}
            <aside class="w-full lg:w-72 shrink-0 space-y-4">
                {{-- Card 1: User Profile Card --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-card overflow-hidden transition-all hover:shadow-card-hover">
                    {{-- Decorative Top Header --}}
                    <div class="h-20 bg-gradient-to-r from-cyan-600 via-cyan-700 to-slate-900 relative">
                        <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:12px_12px]"></div>
                    </div>

                    <div class="px-5 pb-5 pt-0 text-center relative">
                        {{-- Avatar --}}
                        <div class="relative w-20 h-20 mx-auto -mt-10 mb-3">
                            <img src="{{ auth()->user()->avatar_url }}"
                                 class="w-full h-full rounded-2xl border-4 border-white shadow-md object-cover bg-cyan-700" alt="{{ auth()->user()->name }}">
                            <span class="absolute bottom-0 right-0 w-5 h-5 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center text-[10px] text-white shadow-xs" title="Akun Aktif">
                                <i class="fa-solid fa-check"></i>
                            </span>
                        </div>

                        <h3 class="font-bold text-sm text-slate-900 leading-snug">{{ auth()->user()->name }}</h3>
                        <p class="text-xs text-slate-400 mt-0.5 truncate">{{ auth()->user()->email }}</p>

                        <div class="mt-2.5 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan-50 text-cyan-800 text-[11px] font-semibold border border-cyan-200">
                            <i class="fa-solid fa-shield-check text-cyan-600 text-[10px]"></i>
                            <span>Pembeli Terverifikasi</span>
                        </div>

                        {{-- Mini Stats Grid inside Profile Card --}}
                        <div class="grid grid-cols-3 gap-2 mt-4 pt-4 border-t border-slate-100 text-center">
                            <a href="{{ route('customer.dashboard') }}" class="p-2 rounded-xl bg-slate-50 hover:bg-cyan-50 transition-colors group">
                                <span class="block font-bold text-slate-900 text-sm group-hover:text-cyan-700">{{ $totalOrdersCount }}</span>
                                <span class="block text-[10px] text-slate-400 mt-0.5">Pesanan</span>
                            </a>
                            <a href="{{ route('customer.wishlist.index') }}" class="p-2 rounded-xl bg-slate-50 hover:bg-rose-50 transition-colors group">
                                <span class="block font-bold text-slate-900 text-sm group-hover:text-rose-600">{{ $wishlistCount }}</span>
                                <span class="block text-[10px] text-slate-400 mt-0.5">Wishlist</span>
                            </a>
                            <a href="{{ route('customer.addresses.index') }}" class="p-2 rounded-xl bg-slate-50 hover:bg-cyan-50 transition-colors group">
                                <span class="block font-bold text-slate-900 text-sm group-hover:text-cyan-700">{{ $addressCount }}</span>
                                <span class="block text-[10px] text-slate-400 mt-0.5">Alamat</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Navigation Menu Card --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-card space-y-4">
                    {{-- Section 1: Aktivitas Belanja --}}
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 mb-2">Aktivitas Belanja</p>
                        <div class="space-y-1 text-xs">
                            <a href="{{ route('customer.dashboard') }}"
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-semibold text-cyan-800 bg-cyan-50/80 border border-cyan-200/70 transition-all">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-6 h-6 rounded-lg bg-cyan-600 text-white flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-receipt"></i>
                                    </div>
                                    <span>Riwayat Belanja</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full bg-cyan-700 text-white text-[10px] font-bold">
                                    {{ $totalOrdersCount }}
                                </span>
                            </a>

                            <a href="{{ route('customer.wishlist.index') }}"
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium text-slate-700 hover:bg-slate-50 hover:text-cyan-700 transition-colors">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-6 h-6 rounded-lg bg-rose-50 text-rose-500 border border-rose-100 flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-heart"></i>
                                    </div>
                                    <span>Wishlist Saya</span>
                                </div>
                                @if($wishlistCount > 0)
                                    <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 text-[10px] font-bold">
                                        {{ $wishlistCount }}
                                    </span>
                                @endif
                            </a>

                            <a href="{{ route('customer.vouchers.index') }}"
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium text-slate-700 hover:bg-slate-50 hover:text-cyan-700 transition-colors">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-6 h-6 rounded-lg bg-orange-50 text-orange-500 border border-orange-100 flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-ticket"></i>
                                    </div>
                                    <span>Voucher Saya</span>
                                </div>
                                @if(session('applied_voucher'))
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold">
                                        <i class="fa-solid fa-check text-[9px]"></i> Dipilih
                                    </span>
                                @else
                                    <span class="text-[11px] text-slate-400">50+ Voucher</span>
                                @endif
                            </a>
                        </div>
                    </div>

                    {{-- Section 2: Pengaturan Akun --}}
                    <div class="pt-3 border-t border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 mb-2">Pengaturan Akun</p>
                        <div class="space-y-1 text-xs">
                            <a href="{{ route('customer.vouchers.index') }}"
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium text-slate-700 hover:bg-slate-50 hover:text-cyan-700 transition-colors">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-6 h-6 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-ticket"></i>
                                    </div>
                                    <span>Voucher Saya</span>
                                </div>
                                <i class="fa-solid fa-chevron-right text-[10px] text-slate-300"></i>
                            </a>

                            <a href="{{ route('customer.addresses.index') }}"
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium text-slate-700 hover:bg-slate-50 hover:text-cyan-700 transition-colors">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </div>
                                    <span>Buku Alamat</span>
                                </div>
                                <span class="text-[11px] text-slate-400">{{ $addressCount }} Alamat</span>
                            </a>

                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium text-slate-700 hover:bg-slate-50 hover:text-cyan-700 transition-colors">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-user-gear"></i>
                                    </div>
                                    <span>Pengaturan Profil</span>
                                </div>
                                <i class="fa-solid fa-chevron-right text-[10px] text-slate-300"></i>
                            </a>
                        </div>
                    </div>

                    {{-- Section 3: Merchant & Toko --}}
                    <div class="pt-3 border-t border-slate-100">
                        @if(!$userStore)
                            <a href="{{ route('store.register') }}"
                               class="block p-3 rounded-xl bg-gradient-to-br from-cyan-900 to-slate-900 text-white hover:from-cyan-800 hover:to-slate-800 transition-all shadow-xs group">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-cyan-500/20 border border-cyan-400/30 flex items-center justify-center text-cyan-300 text-sm">
                                        <i class="fa-solid fa-store"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold text-xs text-white group-hover:text-cyan-200 transition-colors">Buka Toko Gratis</p>
                                        <p class="text-[10px] text-cyan-200/80 truncate">Mulai jualan produkmu sekarang</p>
                                    </div>
                                    <i class="fa-solid fa-arrow-right text-xs text-cyan-300 group-hover:translate-x-0.5 transition-transform"></i>
                                </div>
                            </a>
                        @elseif($userStore->status === 'approved')
                            <a href="{{ route('seller.dashboard') }}"
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100/80 transition-colors text-xs">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-6 h-6 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-store"></i>
                                    </div>
                                    <span>Seller Center Toko</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full bg-emerald-600 text-white text-[10px] font-bold">Aktif</span>
                            </a>
                        @else
                            <div class="p-3 rounded-xl bg-amber-50 text-amber-900 text-xs font-medium border border-amber-200 flex items-center gap-2.5">
                                <i class="fa-solid fa-clock text-amber-600 text-sm"></i>
                                <div>
                                    <p class="font-bold text-[11px]">Pengajuan Toko</p>
                                    <p class="text-[10px] text-amber-700 uppercase font-semibold">Status: {{ $userStore->status }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </aside>

            {{-- Right Main Content --}}
            <div class="flex-1 min-w-0 w-full space-y-5">
                {{-- Quick Stats Row --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
                    {{-- Total Orders --}}
                    <button type="button" @click="activeTab = 'all'"
                            :class="activeTab === 'all' ? 'border-cyan-500 ring-2 ring-cyan-500/20 bg-cyan-50/40' : 'border-slate-200/80 bg-white hover:border-slate-300'"
                            class="p-4 rounded-2xl border text-left shadow-card transition-all flex flex-col justify-between cursor-pointer">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-medium text-slate-500">Total Pesanan</span>
                            <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-clipboard-list"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="text-xl font-extrabold text-slate-900">{{ $totalOrdersCount }}</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Semua status</span>
                        </div>
                    </button>

                    {{-- Pending Payment --}}
                    <button type="button" @click="activeTab = 'pending'"
                            :class="activeTab === 'pending' ? 'border-amber-500 ring-2 ring-amber-500/20 bg-amber-50/40' : 'border-slate-200/80 bg-white hover:border-slate-300'"
                            class="p-4 rounded-2xl border text-left shadow-card transition-all flex flex-col justify-between cursor-pointer">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-medium text-slate-500">Belum Bayar</span>
                            <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center text-xs relative">
                                <i class="fa-solid fa-credit-card"></i>
                                @if($pendingCount > 0)
                                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
                                @endif
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="text-xl font-extrabold {{ $pendingCount > 0 ? 'text-amber-600' : 'text-slate-900' }}">{{ $pendingCount }}</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Perlu diselesaikan</span>
                        </div>
                    </button>

                    {{-- In Shipping / Processing --}}
                    <button type="button" @click="activeTab = 'shipped'"
                            :class="activeTab === 'shipped' ? 'border-purple-500 ring-2 ring-purple-500/20 bg-purple-50/40' : 'border-slate-200/80 bg-white hover:border-slate-300'"
                            class="p-4 rounded-2xl border text-left shadow-card transition-all flex flex-col justify-between cursor-pointer">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-medium text-slate-500">Dikirim</span>
                            <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-700 border border-purple-200 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-truck-fast"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="text-xl font-extrabold {{ $shippedCount > 0 ? 'text-purple-700' : 'text-slate-900' }}">{{ $shippedCount }}</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Dalam perjalanan</span>
                        </div>
                    </button>

                    {{-- Completed --}}
                    <button type="button" @click="activeTab = 'completed'"
                            :class="activeTab === 'completed' ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/40' : 'border-slate-200/80 bg-white hover:border-slate-300'"
                            class="p-4 rounded-2xl border text-left shadow-card transition-all flex flex-col justify-between cursor-pointer">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-medium text-slate-500">Selesai</span>
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="text-xl font-extrabold text-slate-900">{{ $completedCount }}</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Pesanan sukses</span>
                        </div>
                    </button>
                </div>

                {{-- Main Card: Riwayat Pesanan Belanja --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-card overflow-hidden">
                    {{-- Header with Search & Filter Tabs --}}
                    <div class="p-5 border-b border-slate-100 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-cyan-50 text-cyan-700 border border-cyan-200 flex items-center justify-center text-sm shrink-0">
                                    <i class="fa-solid fa-receipt"></i>
                                </div>
                                <div>
                                    <h2 class="font-bold text-sm text-slate-900">Daftar Transaksi Pesanan</h2>
                                    <p class="text-[11px] text-slate-400">Total {{ $totalOrdersCount }} transaksi tercatat di akun Anda</p>
                                </div>
                            </div>

                            {{-- Search bar within orders --}}
                            @if($totalOrdersCount > 0)
                            <div class="relative w-full sm:w-64">
                                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                                <input type="text"
                                       x-model="searchQuery"
                                       placeholder="Cari no. invoice / produk..."
                                       class="w-full pl-9 pr-3.5 py-1.5 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500 bg-slate-50/50">
                                <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 hover:text-slate-600">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                            @endif
                        </div>

                        {{-- Status Filter Tabs --}}
                        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs no-scrollbar">
                            <button type="button" @click="activeTab = 'all'"
                                    :class="activeTab === 'all' ? 'bg-cyan-700 text-white font-semibold shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200/80 font-medium'"
                                    class="px-3.5 py-1.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer">
                                <span>Semua</span>
                                <span class="px-1.5 py-0.2 rounded-full text-[10px]"
                                      :class="activeTab === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'">
                                    {{ $totalOrdersCount }}
                                </span>
                            </button>

                            <button type="button" @click="activeTab = 'pending'"
                                    :class="activeTab === 'pending' ? 'bg-cyan-700 text-white font-semibold shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200/80 font-medium'"
                                    class="px-3.5 py-1.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer">
                                <span>Belum Bayar</span>
                                @if($pendingCount > 0)
                                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-500 text-white font-bold">
                                        {{ $pendingCount }}
                                    </span>
                                @endif
                            </button>

                            <button type="button" @click="activeTab = 'processing'"
                                    :class="activeTab === 'processing' ? 'bg-cyan-700 text-white font-semibold shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200/80 font-medium'"
                                    class="px-3.5 py-1.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer">
                                <span>Diproses</span>
                                @if($processingCount > 0)
                                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-cyan-100 text-cyan-800 font-bold">
                                        {{ $processingCount }}
                                    </span>
                                @endif
                            </button>

                            <button type="button" @click="activeTab = 'shipped'"
                                    :class="activeTab === 'shipped' ? 'bg-cyan-700 text-white font-semibold shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200/80 font-medium'"
                                    class="px-3.5 py-1.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer">
                                <span>Dikirim</span>
                                @if($shippedCount > 0)
                                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-purple-100 text-purple-800 font-bold">
                                        {{ $shippedCount }}
                                    </span>
                                @endif
                            </button>

                            <button type="button" @click="activeTab = 'completed'"
                                    :class="activeTab === 'completed' ? 'bg-cyan-700 text-white font-semibold shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200/80 font-medium'"
                                    class="px-3.5 py-1.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer">
                                <span>Selesai</span>
                                @if($completedCount > 0)
                                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-emerald-100 text-emerald-800 font-bold">
                                        {{ $completedCount }}
                                    </span>
                                @endif
                            </button>

                            <button type="button" @click="activeTab = 'cancelled'"
                                    :class="activeTab === 'cancelled' ? 'bg-cyan-700 text-white font-semibold shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200/80 font-medium'"
                                    class="px-3.5 py-1.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer">
                                <span>Dibatalkan</span>
                                @if($cancelledCount > 0)
                                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-rose-100 text-rose-800 font-bold">
                                        {{ $cancelledCount }}
                                    </span>
                                @endif
                            </button>

                            <button type="button" @click="activeTab = 'complaint'"
                                    :class="activeTab === 'complaint' ? 'bg-cyan-700 text-white font-semibold shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200/80 font-medium'"
                                    class="px-3.5 py-1.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer">
                                <span>Komplain / Retur</span>
                                @if($complaintCount > 0)
                                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-500 text-white font-bold">
                                        {{ $complaintCount }}
                                    </span>
                                @endif
                            </button>
                        </div>
                    </div>

                    {{-- Orders Content Container --}}
                    <div class="p-5">
                        @if($orders->count() > 0)
                            <div class="space-y-4">
                                @foreach($orders as $order)
                                @php
                                    $itemSearchData = $order->orderItems->map(fn($it) => $it->product?->name ?? '')->join(' ');
                                    $searchBlob = strtolower($order->invoice_number . ' ' . ($order->store->name ?? '') . ' ' . $itemSearchData);
                                @endphp
                                <div x-show="(activeTab === 'all' || (activeTab === 'complaint' ? {{ $order->complaint ? 'true' : 'false' }} : activeTab === '{{ $order->status }}')) && (!searchQuery || '{{ addslashes($searchBlob) }}'.includes(searchQuery.toLowerCase()))"
                                     class="border border-slate-200/90 rounded-2xl overflow-hidden shadow-xs hover:shadow-card hover:border-cyan-200 transition-all bg-white">
                                    {{-- Order Card Header --}}
                                    <div class="px-4 sm:px-5 py-3.5 bg-slate-50/80 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 text-xs">
                                        <div class="flex items-center gap-2.5 flex-wrap">
                                            {{-- Store badge --}}
                                            @if($order->store && $order->store->slug)
                                                <a href="{{ route('store.show', $order->store) }}" target="_blank"
                                                   class="flex items-center gap-1.5 font-bold text-slate-800 hover:text-cyan-700 hover:underline transition-colors" title="Kunjungi Toko">
                                                    <i class="fa-solid fa-store text-cyan-700 text-xs"></i>
                                                    <span>{{ $order->store->name }}</span>
                                                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-slate-400"></i>
                                                </a>
                                            @else
                                                <div class="flex items-center gap-1.5 font-bold text-slate-800">
                                                    <i class="fa-solid fa-store text-cyan-700 text-xs"></i>
                                                    <span>{{ $order->store->name ?? 'Official Store SakserShop' }}</span>
                                                </div>
                                            @endif
                                            <span class="text-slate-300">|</span>
                                            {{-- Invoice badge with copy --}}
                                            <div class="flex items-center gap-1 bg-white px-2 py-0.5 rounded-md border border-slate-200">
                                                <span class="font-mono font-semibold text-slate-700 text-[11px]">#{{ $order->invoice_number }}</span>
                                                <button type="button" @click="copyToClipboard('{{ $order->invoice_number }}')"
                                                        class="text-slate-400 hover:text-cyan-700 text-[10px] ml-1 cursor-pointer" title="Salin No. Invoice">
                                                    <i class="fa-regular fa-copy" :class="copiedInvoice === '{{ $order->invoice_number }}' ? 'fa-solid fa-check text-emerald-600' : 'fa-regular fa-copy'"></i>
                                                </button>
                                                <a href="{{ route('orders.invoice', $order) }}" target="_blank" class="text-slate-400 hover:text-cyan-700 text-[10px] ml-1" title="Lihat & Cetak Invoice Resmi">
                                                    <i class="fa-solid fa-file-invoice"></i>
                                                </a>
                                            </div>
                                            <span class="text-slate-400 text-[11px]">• {{ $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                                        </div>

                                        @php
                                            $statusConfig = [
                                                'pending'    => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'icon' => 'fa-hourglass-half', 'label' => 'Menunggu Pembayaran'],
                                                'processing' => ['bg' => 'bg-cyan-50 text-cyan-800 border-cyan-200', 'icon' => 'fa-box-archive', 'label' => 'Diproses Penjual'],
                                                'shipped'    => ['bg' => 'bg-purple-50 text-purple-700 border-purple-200', 'icon' => 'fa-truck-fast', 'label' => 'Sedang Dikirim'],
                                                'completed'  => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'icon' => 'fa-circle-check', 'label' => 'Selesai'],
                                                'cancelled'  => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'icon' => 'fa-circle-xmark', 'label' => 'Dibatalkan'],
                                            ];
                                            $currStatus = $statusConfig[$order->status] ?? ['bg' => 'bg-slate-100 text-slate-700 border-slate-200', 'icon' => 'fa-circle-info', 'label' => ucfirst($order->status)];
                                        @endphp
                                        <div class="flex items-center gap-2 shrink-0">
                                            @if($order->tracking_number)
                                                <a href="{{ route('orders.tracking', $order) }}"
                                                   class="text-[11px] font-mono text-cyan-800 hover:text-cyan-900 bg-white hover:bg-cyan-50 px-2.5 py-0.5 rounded-lg border border-slate-200 hover:border-cyan-300 flex items-center gap-1 transition-colors" title="Lacak Posisi Paket di Peta">
                                                    <i class="fa-solid fa-map-location-dot text-cyan-600 text-[10px]"></i>
                                                    <span>{{ $order->tracking_number }}</span>
                                                </a>
                                            @endif
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold border {{ $currStatus['bg'] }}">
                                                <i class="fa-solid {{ $currStatus['icon'] }} text-[10px]"></i>
                                                {{ $currStatus['label'] }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Order Items List --}}
                                    <div class="p-4 sm:p-5 divide-y divide-slate-100 space-y-3.5">
                                        @foreach($order->orderItems as $item)
                                        <div class="flex items-start sm:items-center gap-3.5 {{ !$loop->first ? 'pt-3.5' : '' }}">
                                            {{-- Product Image --}}
                                            <div class="w-14 h-14 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0">
                                                @if($item->product && $item->product->image_url)
                                                    <img src="{{ $item->product->image_url }}" class="w-full h-full object-cover" alt="{{ $item->product->name }}">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-slate-300 text-base">
                                                        <i class="fa-solid fa-box"></i>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Product Info --}}
                                            <div class="flex-1 min-w-0">
                                                @if($item->product)
                                                    <a href="{{ route('product.show', $item->product) }}" class="text-xs sm:text-sm font-semibold text-slate-900 hover:text-cyan-700 transition-colors line-clamp-1">
                                                        {{ $item->product->name }}
                                                    </a>
                                                @else
                                                    <p class="text-xs sm:text-sm font-medium text-slate-500">Produk Tidak Tersedia</p>
                                                @endif

                                                <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-500">
                                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium">{{ $item->quantity }}x</span>
                                                    <span>Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                                </div>
                                            </div>

                                            {{-- Subtotal & Review CTA --}}
                                            <div class="text-right flex flex-col items-end gap-1.5 shrink-0">
                                                <span class="font-extrabold text-xs sm:text-sm text-slate-900">
                                                    Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                                </span>

                                                {{-- Review Action or Status --}}
                                                @if($order->status === 'completed')
                                                    @php $review = $item->review; @endphp
                                                    @if($review)
                                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200">
                                                            <i class="fa-solid fa-star text-[9px]"></i> {{ $review->rating }}/5 (Diulas)
                                                        </span>
                                                    @else
                                                        <button type="button"
                                                                @click="openReview({{ $order->id }}, {{ $item->id }}, '{{ addslashes($item->product?->name ?? 'Produk') }}', '{{ $item->product?->image_url }}', '{{ route('customer.reviews.store', $order) }}')"
                                                                class="text-[11px] font-semibold text-cyan-700 hover:text-cyan-800 bg-cyan-50 hover:bg-cyan-100 px-2.5 py-1 rounded-lg border border-cyan-200 transition-colors flex items-center gap-1.5 shadow-2xs cursor-pointer">
                                                            <i class="fa-solid fa-star text-amber-500 text-[10px]"></i> Beri Ulasan
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    {{-- Order Card Footer --}}
                                    <div class="px-4 sm:px-5 py-3.5 bg-slate-50/70 border-t border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
                                        <div class="flex items-center gap-3 flex-wrap">
                                            <div class="flex items-baseline gap-1.5">
                                                <span class="text-slate-500 text-xs">Total Pembayaran:</span>
                                                <span class="font-extrabold text-slate-900 text-base text-cyan-800">
                                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                                </span>
                                            </div>

                                            @if($order->complaint)
                                                @if($order->complaint->status === 'pending')
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200">
                                                        <i class="fa-solid fa-hourglass-half text-[9px]"></i> Komplain Diproses Penjual
                                                    </span>
                                                @elseif($order->complaint->status === 'approved')
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200">
                                                        <i class="fa-solid fa-circle-check text-[9px]"></i> Komplain Disetujui (Refund)
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-lg border border-rose-200">
                                                        <i class="fa-solid fa-circle-xmark text-[9px]"></i> Komplain Ditolak
                                                    </span>
                                                @endif
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-2 flex-wrap">
                                            {{-- Live Maps Tracking Button --}}
                                            @if(in_array($order->status, ['processing', 'shipped', 'completed']))
                                                <a href="{{ route('orders.tracking', $order) }}"
                                                   class="h-8 px-3.5 rounded-xl border border-cyan-200 hover:border-cyan-300 bg-cyan-50/80 hover:bg-cyan-100 text-cyan-800 font-bold text-xs flex items-center gap-1.5 transition-all shadow-2xs" title="Lacak Posisi Paket di Peta Live">
                                                    <i class="fa-solid fa-map-location-dot text-cyan-600 text-xs"></i>
                                                    <span>Lacak Paket</span>
                                                </a>
                                            @endif

                                            {{-- Invoice Button --}}
                                            <a href="{{ route('orders.invoice', $order) }}" target="_blank"
                                               class="h-8 px-3 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 text-slate-600 font-semibold text-xs flex items-center gap-1.5 transition-colors" title="Lihat Invoice Resmi">
                                                <i class="fa-solid fa-file-invoice text-[10px] text-slate-400"></i>
                                                <span>Invoice</span>
                                            </a>

                                            {{-- Complaint Trigger Button --}}
                                            @if(!$order->complaint && in_array($order->status, ['processing', 'shipped', 'completed']))
                                                <button type="button"
                                                        @click="openComplaint({{ $order->toJson() }}, '{{ route('customer.complaints.store', $order) }}')"
                                                        class="h-8 px-3 rounded-xl border border-rose-200 hover:border-rose-300 bg-white hover:bg-rose-50 text-rose-600 font-semibold text-xs flex items-center gap-1.5 transition-colors cursor-pointer" title="Ajukan Komplain / Pengembalian">
                                                    <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                                    <span>Komplain</span>
                                                </button>
                                            @endif

                                            @if($order->status === 'pending')
                                                <button type="button"
                                                        @click="openCancelModal('{{ $order->invoice_number }}', '{{ route('customer.order.cancel', $order) }}')"
                                                        class="h-8 px-3 rounded-xl border border-rose-200 hover:border-rose-300 bg-white hover:bg-rose-50 text-rose-600 font-semibold text-xs flex items-center gap-1.5 transition-colors cursor-pointer" title="Batalkan Pesanan">
                                                    <i class="fa-solid fa-xmark text-xs"></i>
                                                    <span>Batalkan</span>
                                                </button>
                                                <a href="{{ route('customer.order.payment', $order) }}"
                                                   class="btn-primary text-xs h-8 px-4 rounded-xl flex items-center gap-1.5 bg-cyan-700 hover:bg-cyan-800 text-white font-semibold shadow-xs">
                                                    <i class="fa-solid fa-credit-card text-[11px]"></i>
                                                    <span>Bayar Sekarang</span>
                                                </a>
                                            @elseif($order->status === 'shipped')
                                                <form action="{{ route('customer.order.confirm_received', $order) }}" method="POST" onsubmit="return confirm('Apakah pesanan sudah Anda terima dalam kondisi baik?')">
                                                    @csrf
                                                    <button type="submit"
                                                            class="h-8 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs flex items-center gap-1.5 shadow-xs transition-colors cursor-pointer">
                                                        <i class="fa-solid fa-circle-check text-xs"></i>
                                                        <span>Pesanan Diterima</span>
                                                    </button>
                                                </form>
                                            @endif

                                            @if($order->orderItems->first() && $order->orderItems->first()->product)
                                                <a href="{{ route('product.show', $order->orderItems->first()->product) }}"
                                                   class="h-8 px-3.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs flex items-center gap-1.5 transition-colors">
                                                    <i class="fa-solid fa-arrow-rotate-left text-[10px] text-slate-400"></i>
                                                    <span>Beli Lagi</span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            {{-- Enhanced Empty State Card --}}
                            <div class="py-16 px-4 text-center">
                                <div class="relative w-24 h-24 mx-auto mb-4">
                                    <div class="absolute inset-0 bg-cyan-100/60 rounded-full blur-xl animate-pulse"></div>
                                    <div class="relative w-full h-full rounded-3xl bg-gradient-to-br from-cyan-50 to-slate-100 border border-cyan-200/80 flex items-center justify-center text-cyan-600 text-4xl shadow-sm">
                                        <i class="fa-solid fa-bag-shopping"></i>
                                    </div>
                                </div>
                                <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Belum Ada Riwayat Belanja</h3>
                                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto leading-relaxed">
                                    Semua transaksi belanja, status pengiriman, dan ulasan pesanan Anda akan tercatat dengan rapi di sini.
                                </p>
                                <div class="mt-6 flex items-center justify-center gap-3 flex-wrap">
                                    <a href="{{ url('/products') }}"
                                       class="btn-primary text-xs h-10 px-5 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-semibold flex items-center gap-2 shadow-sm transition-all">
                                        <i class="fa-solid fa-cart-shopping text-xs"></i>
                                        <span>Mulai Belanja Sekarang</span>
                                    </a>
                                    <a href="{{ route('customer.wishlist.index') }}"
                                       class="h-10 px-4 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs flex items-center gap-2 transition-colors">
                                        <i class="fa-regular fa-heart text-rose-500 text-xs"></i>
                                        <span>Lihat Wishlist</span>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
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
                    <button @click="showReviewModal = false" class="text-slate-400 hover:text-slate-600 w-7 h-7 rounded-lg flex items-center justify-center hover:bg-slate-100 transition-colors">
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
                    <div class="text-center py-2 bg-slate-50/60 rounded-xl border border-slate-100 p-3">
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

        {{-- Modal Ajukan Komplain Pesanan --}}
        <div x-show="showComplaintModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="showComplaintModal = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                    <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600"></i> Ajukan Komplain & Retur
                    </h3>
                    <button @click="showComplaintModal = false" class="text-slate-400 hover:text-slate-600 w-7 h-7 rounded-lg flex items-center justify-center hover:bg-slate-100 transition-colors cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form :action="complaintData.actionUrl" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4 text-xs">
                    @csrf
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Invoice:</span>
                            <span class="font-mono font-bold text-slate-800" x-text="'#' + complaintData.invoiceNumber"></span>
                        </div>
                        <div class="flex justify-between mt-1">
                            <span class="text-slate-500">Toko:</span>
                            <span class="font-semibold text-slate-800" x-text="complaintData.storeName"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alasan Komplain</label>
                        <select name="reason" x-model="complaintData.reason" required class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs focus:border-rose-600 font-semibold">
                            <option value="Barang Rusak / Cacat">Barang Rusak / Cacat</option>
                            <option value="Barang Tidak Sesuai Deskripsi / Salah Kirim">Barang Tidak Sesuai Deskripsi / Salah Kirim</option>
                            <option value="Barang Kurang / Isi Paket Hilang">Barang Kurang / Isi Paket Hilang</option>
                            <option value="Pesanan Belum Diterima Padahal Status Terkirim">Pesanan Belum Diterima Padahal Status Terkirim</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jelaskan Detail Masalah / Kendala</label>
                        <textarea name="description" x-model="complaintData.description" rows="3" required
                                  placeholder="Ceritakan dengan jelas kendala barang yang Anda terima (misal: layar pecah, barang tidak menyala)..."
                                  class="w-full rounded-xl border border-slate-300 text-xs p-3 focus:border-rose-600"></textarea>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Foto Bukti Kerusakan / Unboxing (Opsional)</label>
                        <input type="file" name="photo" accept="image/*"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100">
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="showComplaintModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold shadow-xs cursor-pointer">
                            Kirim Komplain
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Batalkan Pesanan --}}
        <div x-show="showCancelModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="showCancelModal = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 text-xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-circle-xmark text-rose-600"></i> Batalkan Pesanan
                    </h3>
                    <button @click="showCancelModal = false" class="text-slate-400 hover:text-slate-600 w-7 h-7 rounded-lg flex items-center justify-center hover:bg-slate-100 transition-colors cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form :action="cancelData.actionUrl" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div class="p-3 bg-rose-50/60 rounded-xl border border-rose-200/80 text-rose-900 leading-relaxed">
                        <p class="font-semibold text-xs">Konfirmasi Pembatalan</p>
                        <p class="text-[11px] text-rose-700 mt-0.5">Apakah Anda yakin ingin membatalkan pesanan <span class="font-mono font-bold" x-text="'#' + cancelData.invoiceNumber"></span>? Stok barang akan segera dipulihkan.</p>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Pilih Alasan Pembatalan</label>
                        <select name="reason" x-model="cancelData.reason" required class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs focus:border-cyan-600 font-medium">
                            <option value="Ingin mengubah metode pembayaran / pesanan">Ingin mengubah metode pembayaran / pesanan</option>
                            <option value="Ingin mengubah alamat pengiriman">Ingin mengubah alamat pengiriman</option>
                            <option value="Ingin mengganti varian produk (warna/ukuran)">Ingin mengganti varian produk (warna/ukuran)</option>
                            <option value="Menemukan harga yang lebih murah">Menemukan harga yang lebih murah</option>
                            <option value="Tidak ingin melanjutkan pembelian">Tidak ingin melanjutkan pembelian</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" @click="showCancelModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium cursor-pointer">
                            Kembali
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold shadow-xs cursor-pointer">
                            Ya, Batalkan Pesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
