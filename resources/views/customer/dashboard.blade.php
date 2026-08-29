<x-app-layout>
    @php
        $pendingOrders = $orders->where('status', 'pending');
        $processingOrders = $orders->where('status', 'processing');
        $shippedOrders = $orders->where('status', 'shipped');
        $completedOrders = $orders->where('status', 'completed');
        $cancelledOrders = $orders->where('status', 'cancelled');
        $complaintOrders = $orders->filter(fn($o) => $o->complaint !== null);

        $pendingCount = $pendingOrders->count();
        $processingCount = $processingOrders->count();
        $shippedCount = $shippedOrders->count();
        $completedCount = $completedOrders->count();
        $cancelledCount = $cancelledOrders->count();
        $complaintCount = $complaintOrders->count();
        $totalOrdersCount = $orders->count();

        $wishlistCount = auth()->user()->wishlists()->count();
        $addressCount = auth()->user()->addresses()->count();

        // Calculate Member Loyalty Tier
        $totalSpent = $completedOrders->sum('total_amount');
        if ($completedCount >= 10 || $totalSpent >= 5000000) {
            $loyaltyTier = [
                'name'        => 'Platinum VIP',
                'badge'       => 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white border-purple-400',
                'icon'        => 'fa-crown text-amber-300',
                'color'       => 'text-purple-600',
                'next_label'  => 'Tier Tertinggi (Sultan)',
                'progress'    => 100,
                'target'      => 10,
                'current'     => $completedCount,
            ];
        } elseif ($completedCount >= 5 || $totalSpent >= 2000000) {
            $loyaltyTier = [
                'name'        => 'Gold Member',
                'badge'       => 'bg-gradient-to-r from-amber-500 to-amber-600 text-white border-amber-300',
                'icon'        => 'fa-medal text-amber-100',
                'color'       => 'text-amber-600',
                'next_label'  => (10 - $completedCount) . ' pesanan lagi menuju Platinum',
                'progress'    => min(100, round(($completedCount / 10) * 100)),
                'target'      => 10,
                'current'     => $completedCount,
            ];
        } elseif ($completedCount >= 2 || $totalSpent >= 500000) {
            $loyaltyTier = [
                'name'        => 'Silver Member',
                'badge'       => 'bg-gradient-to-r from-slate-500 to-slate-700 text-white border-slate-400',
                'icon'        => 'fa-shield-halved text-slate-200',
                'color'       => 'text-slate-700',
                'next_label'  => (5 - $completedCount) . ' pesanan lagi menuju Gold',
                'progress'    => min(100, round(($completedCount / 5) * 100)),
                'target'      => 5,
                'current'     => $completedCount,
            ];
        } else {
            $loyaltyTier = [
                'name'        => 'Bronze Member',
                'badge'       => 'bg-gradient-to-r from-amber-700 to-amber-900 text-white border-amber-600',
                'icon'        => 'fa-award text-amber-200',
                'color'       => 'text-amber-800',
                'next_label'  => (2 - $completedCount) . ' pesanan lagi menuju Silver',
                'progress'    => min(100, round(($completedCount / 2) * 100)),
                'target'      => 2,
                'current'     => $completedCount,
            ];
        }

        // Active Order for highlight banner
        $highlightOrder = $pendingOrders->first() ?: ($shippedOrders->first() ?: $processingOrders->first());
    @endphp

    <div class="page-container py-6" x-data="{
        activeTab: '{{ request('payment') === 'success' ? 'processing' : (request('tab') ?: 'all') }}',
        searchQuery: '',
        isReordering: null,
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
        isCancelling: false,
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
            this.isCancelling = false;
            this.showCancelModal = true;
        },
        copyToClipboard(text) {
            navigator.clipboard.writeText(text);
            this.copiedInvoice = text;
            if (window.toast) {
                window.toast.success('Nomor Invoice #' + text + ' berhasil disalin!', 'Tersalin');
            }
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
        },
        async reorderItem(url, productName, orderId) {
            this.isReordering = orderId;
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ quantity: 1 })
                });
                const data = await res.json();
                if (res.ok && data.status === 'success') {
                    if (window.toast) {
                        window.toast.success(
                            productName + ' berhasil ditambahkan kembali ke keranjang belanja!',
                            'Beli Lagi Berhasil',
                            {
                                action: {
                                    label: 'Buka Keranjang',
                                    url: '{{ route('customer.cart.index') }}'
                                }
                            }
                        );
                    }
                    window.dispatchEvent(new CustomEvent('cart-updated'));
                } else {
                    if (window.toast) {
                        window.toast.error(data.message || 'Gagal menambahkan produk ke keranjang.');
                    }
                }
            } catch (e) {
                if (window.toast) {
                    window.toast.error('Terjadi kesalahan jaringan.');
                }
            } finally {
                this.isReordering = null;
            }
        }
    }">

        <!-- Breadcrumb & Top Bar -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6 font-medium">
            <a href="{{ url('/') }}" class="hover:text-cyan-600 transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-house text-[11px]"></i> Beranda
            </a>
            <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
            <span class="text-slate-900 font-semibold">Pusat Akun Pelanggan</span>
            <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
            <span class="text-cyan-700 font-bold">Pesanan Saya</span>
        </nav>

        <!-- Notification Banner / Priority Action (If has pending/shipped order) -->
        @if($highlightOrder)
            <div class="mb-6 rounded-2xl p-4 sm:p-5 border transition-all duration-300 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4 {{ $highlightOrder->status === 'pending' ? 'bg-amber-500/10 border-amber-300 text-amber-950' : ($highlightOrder->status === 'shipped' ? 'bg-cyan-500/10 border-cyan-300 text-cyan-950' : 'bg-blue-500/10 border-blue-300 text-blue-950') }}">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center text-lg shrink-0 {{ $highlightOrder->status === 'pending' ? 'bg-amber-500 text-white shadow-xs' : ($highlightOrder->status === 'shipped' ? 'bg-cyan-600 text-white shadow-xs' : 'bg-blue-600 text-white') }}">
                        @if($highlightOrder->status === 'pending')
                            <i class="fa-solid fa-wallet animate-pulse"></i>
                        @elseif($highlightOrder->status === 'shipped')
                            <i class="fa-solid fa-truck-fast"></i>
                        @else
                            <i class="fa-solid fa-box-open"></i>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-sm font-bold tracking-tight">
                                @if($highlightOrder->status === 'pending')
                                    Menunggu Pembayaran Pesanan #{{ $highlightOrder->invoice_number }}
                                @elseif($highlightOrder->status === 'shipped')
                                    Paket Sedang Diantar oleh NitipDongExpress (NDX)
                                @else
                                    Pesanan Sedang Diproses Penjual
                                @endif
                            </h2>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $highlightOrder->status === 'pending' ? 'bg-amber-200 text-amber-900 border border-amber-300' : 'bg-cyan-200 text-cyan-900 border border-cyan-300' }}">
                                Total Rp {{ number_format($highlightOrder->total_amount, 0, ',', '.') }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 mt-0.5">
                            @if($highlightOrder->status === 'pending')
                                Selesaikan pembayaran sebelum batas waktu berakhir agar stok pesanan tidak dibatalkan otomatis.
                            @elseif($highlightOrder->status === 'shipped')
                                No. Resi: <strong class="font-mono text-slate-900">{{ $highlightOrder->tracking_number ?? 'NDX-PROSES' }}</strong> • Estimasi tiba tepat waktu.
                            @else
                                Toko <strong class="text-slate-900">{{ $highlightOrder->store->name ?? 'Penjual' }}</strong> sedang mengemas produk pesanan Anda.
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 w-full md:w-auto shrink-0">
                    @if($highlightOrder->status === 'pending')
                        <a href="{{ route('customer.order.payment', $highlightOrder) }}" class="w-full md:w-auto text-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-arrow-right"></i> Bayar Sekarang
                        </a>
                    @elseif($highlightOrder->status === 'shipped')
                        <a href="{{ route('orders.tracking', $highlightOrder) }}" class="w-full md:w-auto text-center px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-map-location-dot"></i> Lacak Live GPS
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <!-- Main 2-Column Layout (Sidebar + Workspace) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- LEFT SIDEBAR: NitipDong Account Hub -->
            <aside class="lg:col-span-4 xl:col-span-3 space-y-6">

                <!-- User Profile & Loyalty Card -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs relative overflow-hidden">
                    <div class="flex items-center gap-3.5 pb-4 border-b border-slate-100">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-cyan-600 to-cyan-400 text-white flex items-center justify-center font-bold text-lg shadow-sm shrink-0 uppercase">
                            {{ substr(auth()->user()->name, 0, 2) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="text-sm font-bold text-slate-900 truncate flex items-center gap-1.5">
                                {{ auth()->user()->name }}
                                <i class="fa-solid fa-circle-check text-cyan-600 text-xs" title="Akun Terverifikasi"></i>
                            </h2>
                            <p class="text-xs text-slate-500 truncate mt-0.5 font-mono">{{ auth()->user()->email }}</p>
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full mt-1.5 border {{ $loyaltyTier['badge'] }}">
                                <i class="fa-solid {{ $loyaltyTier['icon'] }}"></i> {{ $loyaltyTier['name'] }}
                            </span>
                        </div>
                    </div>

                    <!-- Quick Wallet & Shopping Stats -->
                    <div class="grid grid-cols-3 gap-2 py-3.5 text-center border-b border-slate-100 text-xs">
                        <div class="px-1">
                            <span class="text-[10px] text-slate-400 font-bold block uppercase tracking-wider">Pesanan</span>
                            <span class="font-extrabold text-slate-900 text-sm mt-0.5 block">{{ $totalOrdersCount }}</span>
                        </div>
                        <div class="border-x border-slate-100 px-1">
                            <span class="text-[10px] text-slate-400 font-bold block uppercase tracking-wider">Wishlist</span>
                            <span class="font-extrabold text-rose-600 text-sm mt-0.5 block">{{ $wishlistCount }}</span>
                        </div>
                        <div class="px-1">
                            <span class="text-[10px] text-slate-400 font-bold block uppercase tracking-wider">Alamat</span>
                            <span class="font-extrabold text-cyan-700 text-sm mt-0.5 block">{{ $addressCount }}</span>
                        </div>
                    </div>

                    <!-- Navigation Links Group 1: Belanja -->
                    <div class="pt-4 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 block mb-1">Aktivitas Saya</span>
                        
                        <a href="{{ route('customer.dashboard') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold bg-cyan-50 text-cyan-800 border border-cyan-100 transition-colors">
                            <span class="flex items-center gap-2.5">
                                <i class="fa-solid fa-box-archive text-cyan-600 text-sm w-4 text-center"></i>
                                Pesanan Saya
                            </span>
                            @if($pendingCount + $shippedCount > 0)
                                <span class="px-1.5 py-0.5 bg-cyan-600 text-white text-[10px] font-extrabold rounded-full">
                                    {{ $pendingCount + $shippedCount }}
                                </span>
                            @endif
                        </a>

                        <a href="{{ route('customer.cart.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-cyan-700 transition-colors">
                            <span class="flex items-center gap-2.5">
                                <i class="fa-solid fa-cart-shopping text-slate-400 text-sm w-4 text-center"></i>
                                Keranjang Belanja
                            </span>
                        </a>

                        <a href="{{ route('customer.address.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-cyan-700 transition-colors">
                            <span class="flex items-center gap-2.5">
                                <i class="fa-solid fa-map-location-dot text-slate-400 text-sm w-4 text-center"></i>
                                Buku Alamat Pengiriman
                            </span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $addressCount }}</span>
                        </a>

                        <a href="{{ route('chat.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-cyan-700 transition-colors">
                            <span class="flex items-center gap-2.5">
                                <i class="fa-solid fa-comments text-slate-400 text-sm w-4 text-center"></i>
                                Chat Penjual & Bantuan
                            </span>
                        </a>

                        <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-cyan-700 transition-colors">
                            <span class="flex items-center gap-2.5">
                                <i class="fa-regular fa-bell text-slate-400 text-sm w-4 text-center"></i>
                                Notifikasi Akun
                            </span>
                        </a>
                    </div>

                    <!-- Navigation Links Group 2: Pengaturan Akun -->
                    <div class="pt-4 border-t border-slate-100 mt-4 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 block mb-1">Pengaturan</span>
                        
                        <a href="{{ route('profile.edit') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-cyan-700 transition-colors">
                            <span class="flex items-center gap-2.5">
                                <i class="fa-regular fa-user text-slate-400 text-sm w-4 text-center"></i>
                                Profil & Keamanan
                            </span>
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="pt-1">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-rose-600 hover:bg-rose-50 transition-colors text-left cursor-pointer">
                                <i class="fa-solid fa-arrow-right-from-bracket text-rose-500 text-sm w-4 text-center"></i>
                                Keluar dari Akun
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Seller Hub Promo Card -->
                @if(!$userStore)
                    <div class="rounded-2xl p-5 bg-gradient-to-br from-slate-900 to-slate-800 text-white relative overflow-hidden shadow-xs border border-slate-800">
                        <div class="w-20 h-20 bg-cyan-500/20 rounded-full blur-2xl absolute -right-4 -bottom-4 pointer-events-none"></div>
                        <div class="flex items-center gap-2 text-cyan-400 text-xs font-extrabold uppercase tracking-wider mb-2">
                            <i class="fa-solid fa-store"></i> Seller Center
                        </div>
                        <h3 class="font-extrabold text-sm text-white leading-snug">Punya Produk untuk Dijual?</h3>
                        <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                            Buka toko gratis di NitipDong, jangkau pembeli di seluruh Indonesia, dan nikmati integrasi logistik NitipDongExpress.
                        </p>
                        <a href="{{ route('store.register') }}" class="mt-4 inline-flex items-center justify-center gap-1.5 w-full py-2.5 px-4 bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs rounded-xl shadow-sm transition-colors cursor-pointer">
                            <i class="fa-solid fa-rocket"></i> Buka Toko Gratis Sekarang
                        </a>
                    </div>
                @else
                    <div class="rounded-2xl p-4 bg-gradient-to-br from-cyan-900/40 to-slate-900 border border-cyan-800/40 text-white shadow-xs">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-cyan-600/30 border border-cyan-500/40 flex items-center justify-center text-cyan-300 font-bold text-sm">
                                    <i class="fa-solid fa-shop"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold text-cyan-400 block uppercase">Toko Anda</span>
                                    <h3 class="font-bold text-xs text-white truncate">{{ $userStore->name }}</h3>
                                </div>
                            </div>
                            <a href="{{ route('seller.dashboard') }}" class="px-3 py-1.5 bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-[11px] rounded-lg transition-colors">
                                Kelola Toko
                            </a>
                        </div>
                    </div>
                @endif

            </aside>

            <!-- RIGHT WORKSPACE: Orders & Transaction Center -->
            <main class="lg:col-span-8 xl:col-span-9 space-y-6">

                <!-- Header Title & Quick Search -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h1 class="text-lg font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                                <i class="fa-solid fa-bag-shopping text-cyan-600"></i>
                                Riwayat Pesanan Saya
                            </h1>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Pantau progres pesanan, pengiriman NitipDongExpress, dan kelola ulasan produk Anda.
                            </p>
                        </div>

                        <!-- Instant Search Input -->
                        <div class="relative w-full sm:w-72">
                            <input type="text" x-model="searchQuery" placeholder="Cari invoice / nama produk..."
                                   class="w-full h-9 pl-8 pr-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-cyan-600 focus:ring-1 focus:ring-cyan-600 transition-all">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs absolute left-2.5 top-2.5"></i>
                        </div>
                    </div>

                    <!-- Horizontal Clean Status Tabs (Pill style with counts) -->
                    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pt-5 border-t border-slate-100 mt-4 pb-1">
                        
                        <button @click="activeTab = 'all'" :class="activeTab === 'all' ? 'bg-slate-900 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-medium'"
                                class="px-3.5 py-2 rounded-xl text-xs shrink-0 transition-all flex items-center gap-1.5 cursor-pointer">
                            <span>Semua</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="activeTab === 'all' ? 'bg-slate-800 text-slate-300' : 'bg-slate-200 text-slate-600'">
                                {{ $totalOrdersCount }}
                            </span>
                        </button>

                        <button @click="activeTab = 'pending'" :class="activeTab === 'pending' ? 'bg-amber-600 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-medium'"
                                class="px-3.5 py-2 rounded-xl text-xs shrink-0 transition-all flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-regular fa-clock text-[11px]"></i>
                            <span>Belum Bayar</span>
                            @if($pendingCount > 0)
                                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-500 text-white font-bold animate-pulse">
                                    {{ $pendingCount }}
                                </span>
                            @endif
                        </button>

                        <button @click="activeTab = 'processing'" :class="activeTab === 'processing' ? 'bg-blue-600 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-medium'"
                                class="px-3.5 py-2 rounded-xl text-xs shrink-0 transition-all flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-boxes-packing text-[11px]"></i>
                            <span>Diproses</span>
                            @if($processingCount > 0)
                                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-blue-500 text-white font-bold">
                                    {{ $processingCount }}
                                </span>
                            @endif
                        </button>

                        <button @click="activeTab = 'shipped'" :class="activeTab === 'shipped' ? 'bg-cyan-600 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-medium'"
                                class="px-3.5 py-2 rounded-xl text-xs shrink-0 transition-all flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-truck-fast text-[11px]"></i>
                            <span>Dikirim (NDX)</span>
                            @if($shippedCount > 0)
                                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-cyan-500 text-white font-bold">
                                    {{ $shippedCount }}
                                </span>
                            @endif
                        </button>

                        <button @click="activeTab = 'completed'" :class="activeTab === 'completed' ? 'bg-emerald-700 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-medium'"
                                class="px-3.5 py-2 rounded-xl text-xs shrink-0 transition-all flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-regular fa-circle-check text-[11px]"></i>
                            <span>Selesai</span>
                            @if($completedCount > 0)
                                <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="activeTab === 'completed' ? 'bg-emerald-800 text-emerald-100' : 'bg-slate-200 text-slate-600'">
                                    {{ $completedCount }}
                                </span>
                            @endif
                        </button>

                        <button @click="activeTab = 'cancelled'" :class="activeTab === 'cancelled' ? 'bg-rose-700 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-medium'"
                                class="px-3.5 py-2 rounded-xl text-xs shrink-0 transition-all flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-regular fa-circle-xmark text-[11px]"></i>
                            <span>Dibatalkan</span>
                            @if($cancelledCount > 0)
                                <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="activeTab === 'cancelled' ? 'bg-rose-800 text-rose-100' : 'bg-slate-200 text-slate-600'">
                                    {{ $cancelledCount }}
                                </span>
                            @endif
                        </button>

                        <button @click="activeTab = 'complaint'" :class="activeTab === 'complaint' ? 'bg-purple-700 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-medium'"
                                class="px-3.5 py-2 rounded-xl text-xs shrink-0 transition-all flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-triangle-exclamation text-[11px]"></i>
                            <span>Komplain</span>
                            @if($complaintCount > 0)
                                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-purple-500 text-white font-bold">
                                    {{ $complaintCount }}
                                </span>
                            @endif
                        </button>
                    </div>
                </div>

                <!-- ORDER CARDS LISTING -->
                <div class="space-y-4">
                    @forelse($orders as $order)
                        @php
                            $orderItems = $order->orderItems;
                            $hasReviewed = $orderItems->every(fn($item) => $item->review !== null);
                            $itemsSearchText = $orderItems->pluck('product.name')->join(' ') . ' ' . $order->invoice_number . ' ' . ($order->store->name ?? '');
                        @endphp

                        <div x-show="(activeTab === 'all' || 
                                     (activeTab === 'pending' && '{{ $order->status }}' === 'pending') || 
                                     (activeTab === 'processing' && '{{ $order->status }}' === 'processing') || 
                                     (activeTab === 'shipped' && '{{ $order->status }}' === 'shipped') || 
                                     (activeTab === 'completed' && '{{ $order->status }}' === 'completed') || 
                                     (activeTab === 'cancelled' && in_array('{{ $order->status }}', ['cancelled', 'rejected'])) || 
                                     (activeTab === 'complaint' && '{{ $order->complaint ? 'true' : 'false' }}' === 'true')) &&
                                     ('{{ strtolower(addslashes($itemsSearchText)) }}'.includes(searchQuery.toLowerCase().trim()))"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden transition-all hover:border-slate-300">

                            <!-- Order Card Header: Store & Status -->
                            <div class="px-5 py-3.5 bg-slate-50/80 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-1.5 font-extrabold text-slate-900">
                                        <i class="fa-solid fa-store text-cyan-600"></i>
                                        <span>{{ $order->store->name ?? 'NitipDong Store' }}</span>
                                    </div>
                                    <span class="text-slate-300">|</span>
                                    <button type="button" @click="copyToClipboard('{{ $order->invoice_number }}')" class="font-mono text-slate-500 hover:text-cyan-700 font-bold flex items-center gap-1 cursor-pointer">
                                        <span>#{{ $order->invoice_number }}</span>
                                        <i class="fa-regular fa-copy text-[10px]" :class="copiedInvoice === '{{ $order->invoice_number }}' ? 'text-emerald-600' : ''"></i>
                                    </button>
                                </div>

                                <div class="flex items-center gap-2">
                                    <span class="text-slate-400 text-[11px] hidden sm:inline-block">
                                        {{ $order->created_at->translatedFormat('d M Y, H:i') }}
                                    </span>

                                    <!-- Dynamic Status Badge -->
                                    @if($order->status === 'pending')
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 flex items-center gap-1">
                                            <i class="fa-regular fa-clock text-[10px]"></i> Menunggu Pembayaran
                                        </span>
                                    @elseif($order->status === 'processing')
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 flex items-center gap-1">
                                            <i class="fa-solid fa-box-archive text-[10px]"></i> Sedang Dikemas
                                        </span>
                                    @elseif($order->status === 'shipped')
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-200 flex items-center gap-1">
                                            <i class="fa-solid fa-truck-fast text-[10px]"></i> Dalam Pengiriman NDX
                                        </span>
                                    @elseif($order->status === 'completed')
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i> Selesai
                                        </span>
                                    @elseif(in_array($order->status, ['cancelled', 'rejected']))
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200 flex items-center gap-1">
                                            <i class="fa-regular fa-circle-xmark text-[10px]"></i> Dibatalkan
                                        </span>
                                    @endif

                                    @if($order->complaint)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-100 text-purple-800 border border-purple-200">
                                            Komplain: {{ ucfirst($order->complaint->status) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Order Card Body: Items List -->
                            <div class="divide-y divide-slate-100 px-5">
                                @foreach($orderItems as $item)
                                    @php
                                        $product = $item->product;
                                        $itemReview = $item->review;
                                    @endphp
                                    <div class="py-4 flex items-center justify-between gap-4">
                                        <div class="flex items-center gap-3.5 min-w-0">
                                            <div class="w-16 h-16 rounded-xl border border-slate-200 bg-slate-50 overflow-hidden shrink-0 p-1 flex items-center justify-center">
                                                <img src="{{ $product?->image_url ?? asset('img/saksershop-logo.png') }}" alt="{{ $product?->name ?? 'Produk' }}" class="w-full h-full object-contain">
                                            </div>
                                            <div class="min-w-0">
                                                <h3 class="text-xs font-bold text-slate-900 truncate">
                                                    {{ $product?->name ?? 'Produk Dihapus' }}
                                                </h3>
                                                <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-500">
                                                    @if($item->variant)
                                                        <span class="px-1.5 py-0.5 bg-slate-100 text-slate-600 rounded font-medium text-[10px]">
                                                            Varian: {{ $item->variant }}
                                                        </span>
                                                    @endif
                                                    <span>{{ $item->quantity }} barang &times; Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-right shrink-0">
                                            <span class="font-extrabold text-xs text-slate-900 block">
                                                Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                            </span>

                                            <!-- Review Status / Button per item -->
                                            @if($order->status === 'completed')
                                                @if($itemReview)
                                                    <div class="flex items-center justify-end gap-1 text-[11px] text-amber-500 font-bold mt-1">
                                                        <i class="fa-solid fa-star text-[10px]"></i>
                                                        <span>{{ $itemReview->rating }}/5</span>
                                                        <span class="text-slate-400 font-normal">(Diulas)</span>
                                                    </div>
                                                @else
                                                    <button type="button" @click="openReview({{ $order->id }}, {{ $item->id }}, '{{ addslashes($product?->name ?? 'Produk') }}', '{{ $product?->image_url ?? asset('img/saksershop-logo.png') }}', '{{ route('customer.reviews.store', $order) }}')"
                                                            class="mt-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition-colors inline-flex items-center gap-1 cursor-pointer">
                                                        <i class="fa-regular fa-star"></i> Tulis Ulasan
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Order Card Footer: Shipping Info & Action Buttons -->
                            <div class="px-5 py-4 bg-slate-50/50 border-t border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-xs">
                                <!-- Logistics & NDX Tracking Tag -->
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-cyan-100/60 text-cyan-800 border border-cyan-200 text-[11px] font-bold">
                                        <i class="fa-solid fa-truck text-cyan-600"></i>
                                        {{ $order->shipping_courier ?? 'NitipDongExpress' }} - {{ $order->shipping_service ?? 'Reguler' }}
                                    </span>

                                    @if($order->tracking_number)
                                        <span class="text-slate-500 font-mono text-[11px]">
                                            Resi: <strong class="text-slate-800">{{ $order->tracking_number }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <!-- Total Payment & Action Buttons -->
                                <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto">
                                    <div class="text-left sm:text-right">
                                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Total Pesanan:</span>
                                        <span class="text-sm font-black text-slate-900 tracking-tight">
                                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <!-- ACTION 1: Pending Payment -->
                                        @if($order->status === 'pending')
                                            <button type="button" @click="openCancelModal('{{ $order->invoice_number }}', '{{ route('customer.order.cancel', $order) }}')"
                                                    class="px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 transition-colors cursor-pointer">
                                                Batalkan
                                            </button>
                                            <a href="{{ route('customer.order.payment', $order) }}" class="px-4 py-2 rounded-xl text-xs font-extrabold bg-amber-500 hover:bg-amber-600 text-white shadow-xs transition-colors flex items-center gap-1.5 cursor-pointer">
                                                <i class="fa-solid fa-credit-card"></i> Bayar Sekarang
                                            </a>
                                        @endif

                                        <!-- ACTION 2: Shipped / In Transit -->
                                        @if($order->status === 'shipped')
                                            <a href="{{ route('orders.tracking', $order) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-cyan-50 text-cyan-800 hover:bg-cyan-100 border border-cyan-200 transition-colors flex items-center gap-1.5 cursor-pointer">
                                                <i class="fa-solid fa-map-location-dot text-cyan-600"></i> Lacak Pengiriman
                                            </a>
                                            <form method="POST" action="{{ route('customer.order.confirm_received', $order) }}" onsubmit="return confirm('Apakah Anda yakin paket pesanan ini telah diterima dengan baik?')">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition-colors flex items-center gap-1.5 cursor-pointer">
                                                    <i class="fa-solid fa-check-double"></i> Terima Barang
                                                </button>
                                            </form>
                                        @endif

                                        <!-- ACTION 3: Completed -->
                                        @if($order->status === 'completed')
                                            <a href="{{ route('orders.invoice', $order) }}" target="_blank" class="px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-200/60 border border-slate-200 transition-colors flex items-center gap-1">
                                                <i class="fa-solid fa-file-invoice"></i> Invoice
                                            </a>
                                            
                                            @if(!$order->complaint)
                                                <button type="button" @click="openComplaint({{ $order }}, '{{ route('customer.complaints.store', $order) }}')"
                                                        class="px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-purple-700 hover:bg-purple-50 border border-slate-200 transition-colors cursor-pointer">
                                                    Komplain
                                                </button>
                                            @endif

                                            @if($orderItems->first() && $orderItems->first()->product)
                                                <button type="button" @click="reorderItem('{{ route('customer.cart.store', $orderItems->first()->product) }}', '{{ addslashes($orderItems->first()->product->name) }}', {{ $order->id }})"
                                                        :disabled="isReordering === {{ $order->id }}"
                                                        class="px-3.5 py-2 rounded-xl text-xs font-bold bg-cyan-600 hover:bg-cyan-700 text-white shadow-xs transition-colors flex items-center gap-1.5 cursor-pointer">
                                                    <i class="fa-solid" :class="isReordering === {{ $order->id }} ? 'fa-spinner animate-spin' : 'fa-rotate-right'"></i>
                                                    Beli Lagi
                                                </button>
                                            @endif
                                        @endif

                                        <!-- ACTION 4: Cancelled -->
                                        @if(in_array($order->status, ['cancelled', 'rejected']) && $orderItems->first() && $orderItems->first()->product)
                                            <button type="button" @click="reorderItem('{{ route('customer.cart.store', $orderItems->first()->product) }}', '{{ addslashes($orderItems->first()->product->name) }}', {{ $order->id }})"
                                                    :disabled="isReordering === {{ $order->id }}"
                                                    class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-900 hover:bg-slate-800 text-white shadow-xs transition-colors flex items-center gap-1.5 cursor-pointer">
                                                <i class="fa-solid" :class="isReordering === {{ $order->id }} ? 'fa-spinner animate-spin' : 'fa-rotate-right'"></i>
                                                Pesan Ulang
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-2xl border border-slate-200/90 p-12 text-center shadow-xs">
                            <div class="w-16 h-16 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center mx-auto text-2xl mb-4">
                                <i class="fa-solid fa-basket-shopping"></i>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900">Belum Ada Riwayat Pesanan</h3>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto leading-relaxed">
                                Jelajahi katalog jutaan produk original, nikmati promo Flash Sale kilat, dan manfaatkan gratis ongkir NitipDongExpress!
                            </p>
                            <a href="{{ url('/products') }}" class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors">
                                <i class="fa-solid fa-magnifying-glass"></i> Mulai Belanja Sekarang
                            </a>
                        </div>
                    @endforelse
                </div>

            </main>
        </div>

        <!-- ========================================== -->
        <!-- INTERACTIVE MODALS -->
        <!-- ========================================== -->

        <!-- 1. MODAL TULIS ULASAN PRODUK -->
        <div x-show="showReviewModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div @click.outside="showReviewModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-star text-amber-500"></i> Beri Ulasan & Penilaian Produk
                    </h3>
                    <button @click="showReviewModal = false" class="text-slate-400 hover:text-slate-600 text-sm cursor-pointer">&times;</button>
                </div>

                <form :action="reviewData.actionUrl" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="order_item_id" :value="reviewData.orderItemId">

                    <!-- Product Summary -->
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <img :src="reviewData.productImage" alt="" class="w-12 h-12 rounded-lg object-contain bg-white border border-slate-200 p-0.5">
                        <div class="min-w-0 flex-1">
                            <span class="text-xs font-bold text-slate-900 truncate block" x-text="reviewData.productName"></span>
                            <span class="text-[10px] text-slate-500">Bagikan kepuasan belanja Anda untuk membantu pembeli lain</span>
                        </div>
                    </div>

                    <!-- Star Rating Interactive -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 text-center">Kualitas Produk & Pelayanan</label>
                        <div class="flex items-center justify-center gap-2 text-2xl py-2">
                            <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                <button type="button" 
                                        @click="reviewData.rating = star"
                                        @mouseenter="hoverRating = star"
                                        @mouseleave="hoverRating = reviewData.rating"
                                        class="text-amber-400 hover:scale-125 transition-transform cursor-pointer">
                                    <i :class="(hoverRating >= star || reviewData.rating >= star) ? 'fa-solid fa-star' : 'fa-regular fa-star text-slate-300'"></i>
                                </button>
                            </template>
                        </div>
                        <input type="hidden" name="rating" :value="reviewData.rating">
                    </div>

                    <!-- Review Text -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Ulasan Tertulis</label>
                        <textarea name="comment" x-model="reviewData.comment" rows="3" required placeholder="Ceritakan kepuasan Anda mengenai kualitas barang, kecepatan pengiriman, atau respon penjual..."
                                  class="w-full text-xs rounded-xl border border-slate-200 p-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-600"></textarea>
                    </div>

                    <!-- Photo Upload -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Unggah Foto Produk (Opsional)</label>
                        <input type="file" name="images[]" multiple accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100 cursor-pointer">
                    </div>

                    <!-- Anonymous Checkbox -->
                    <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                        <input type="checkbox" name="is_anonymous" value="1" x-model="reviewData.is_anonymous" class="rounded text-cyan-600 focus:ring-cyan-500">
                        <span>Sembunyikan nama saya pada ulasan (Tampilkan sebagai Anonim)</span>
                    </label>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="showReviewModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl cursor-pointer">Batal</button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white rounded-xl shadow-xs transition-colors cursor-pointer">Kirim Ulasan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 2. MODAL KOMPLAIN PESANAN -->
        <div x-show="showComplaintModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div @click.outside="showComplaintModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden">
                <div class="px-6 py-4 bg-purple-50 border-b border-purple-100 flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-purple-900 flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-purple-600"></i> Ajukan Komplain Pesanan
                    </h3>
                    <button @click="showComplaintModal = false" class="text-slate-400 hover:text-slate-600 text-sm cursor-pointer">&times;</button>
                </div>

                <form :action="complaintData.actionUrl" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                        <span class="text-slate-500">Invoice:</span> <strong class="text-slate-900 font-mono" x-text="'#' + complaintData.invoiceNumber"></strong> •
                        <span class="text-slate-500">Toko:</span> <strong class="text-slate-900" x-text="complaintData.storeName"></strong>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Komplain</label>
                        <select name="reason" x-model="complaintData.reason" class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:border-purple-600 focus:ring-1 focus:ring-purple-600">
                            <option value="Barang Rusak / Cacat">Barang Rusak / Cacat saat diterima</option>
                            <option value="Barang Tidak Sesuai Deskripsi">Barang Tidak Sesuai Foto / Deskripsi</option>
                            <option value="Jumlah / Varian Kurang">Jumlah Barang Kurang / Varian Salah</option>
                            <option value="Paket Tidak Diterima">Paket Belum Pernah Diterima</option>
                            <option value="Kendala Lainnya">Kendala Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Detail Kendala</label>
                        <textarea name="description" x-model="complaintData.description" rows="3" required placeholder="Jelaskan secara rinci kondisi produk atau kendala yang Anda alami..."
                                  class="w-full text-xs rounded-xl border border-slate-200 p-3 focus:border-purple-600 focus:ring-1 focus:ring-purple-600"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Foto Bukti Kerusakan / Kendala</label>
                        <input type="file" name="photo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="showComplaintModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl cursor-pointer">Batal</button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold bg-purple-700 hover:bg-purple-800 text-white rounded-xl shadow-xs transition-colors cursor-pointer">Kirim Klaim Komplain</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 3. MODAL BATALKAN PESANAN -->
        <div x-show="showCancelModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div @click.outside="showCancelModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
                <div class="px-6 py-4 bg-rose-50 border-b border-rose-100 flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-rose-900 flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600"></i> Batalkan Pesanan
                    </h3>
                    <button @click="showCancelModal = false" class="text-slate-400 hover:text-slate-600 text-sm cursor-pointer">&times;</button>
                </div>

                <form :action="cancelData.actionUrl" method="POST" class="p-6 space-y-4" @submit="isCancelling = true">
                    @csrf
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Apakah Anda yakin ingin membatalkan pesanan <strong class="font-mono text-slate-900" x-text="'#' + cancelData.invoiceNumber"></strong>? Stok produk dan kuota voucher akan otomatis dikembalikan.
                    </p>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Pembatalan</label>
                        <select name="cancel_reason" x-model="cancelData.reason" class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:border-rose-600 focus:ring-1 focus:ring-rose-600">
                            <option value="Ingin mengubah metode pembayaran / salah pesan">Ingin mengubah metode pembayaran / salah pesan</option>
                            <option value="Ingin mengubah alamat pengiriman">Ingin mengubah alamat pengiriman</option>
                            <option value="Menemukan harga lebih murah">Menemukan harga lebih murah</option>
                            <option value="Lupa memasukkan voucher diskon">Lupa memasukkan voucher diskon</option>
                            <option value="Alasan lainnya">Alasan lainnya</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="showCancelModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl cursor-pointer">Batal</button>
                        <button type="submit" :disabled="isCancelling" class="px-5 py-2 text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white rounded-xl shadow-xs transition-colors flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-solid" :class="isCancelling ? 'fa-spinner animate-spin' : 'fa-check'"></i>
                            Konfirmasi Pembatalan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
