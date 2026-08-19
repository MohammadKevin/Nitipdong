<x-seller-layout>
    <x-slot name="title">
        Dompet Toko & Penarikan Dana - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="space-y-6" x-data="{
        showWithdrawModal: false,
        withdrawAmount: '',
        bankName: '{{ addslashes($store->bank_name ?? 'BCA') }}',
        accountNumber: '{{ addslashes($store->bank_account_number ?? '') }}',
        accountHolder: '{{ addslashes($store->bank_account_holder ?? Auth::user()->name) }}'
    }">
        {{-- Header Banner --}}
        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-card border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-sm border border-emerald-200">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Dompet & Saldo Toko</h1>
                </div>
                <p class="text-xs text-slate-500 mt-1">Kelola pendapatan dari penjualan produk dan ajukan penarikan dana (payout) ke rekening bank Anda.</p>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" @click="showWithdrawModal = true"
                        class="btn-primary text-xs h-9.5 px-4.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold flex items-center gap-2 shadow-xs transition-all cursor-pointer">
                    <i class="fa-solid fa-money-bill-transfer text-xs"></i>
                    <span>Tarik Dana (Payout)</span>
                </button>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="flex items-center gap-3 px-4 py-3.5 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl text-xs font-semibold shadow-xs">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="flex items-center gap-3 px-4 py-3.5 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl text-xs font-semibold shadow-xs">
                <i class="fa-solid fa-circle-xmark text-rose-600 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Metrics Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Available Balance Card --}}
            <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-2xl p-5 text-white shadow-card flex flex-col justify-between relative overflow-hidden">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-emerald-100 uppercase tracking-wider">Saldo Tersedia</span>
                        <div class="w-8 h-8 rounded-xl bg-white/15 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-vault"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-extrabold mt-2 tracking-tight">Rp {{ number_format($store->balance, 0, ',', '.') }}</h3>
                </div>
                <div class="mt-4 pt-3 border-t border-white/20 flex items-center justify-between text-[11px] text-emerald-100">
                    <span>Dapat ditarik kapan saja</span>
                    <button type="button" @click="showWithdrawModal = true" class="underline font-bold hover:text-white cursor-pointer">Tarik &rarr;</button>
                </div>
            </div>

            {{-- Total Gross Sales --}}
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-card flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500">Omset Penjualan (GMV)</span>
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center text-sm border border-blue-200">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-xl font-extrabold text-slate-900">Rp {{ number_format($grossSales, 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Dari seluruh pesanan selesai</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-500">
                    Komisi Platform 5%: <strong class="text-rose-600">-Rp {{ number_format($platformCommission, 0, ',', '.') }}</strong>
                </div>
            </div>

            {{-- Total Withdrawn --}}
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-card flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500">Total Telah Dicairkan</span>
                    <div class="w-8 h-8 rounded-xl bg-cyan-50 text-cyan-700 flex items-center justify-center text-sm border border-cyan-200">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-xl font-extrabold text-slate-900">Rp {{ number_format($totalWithdrawn, 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Sukses masuk rekening bank</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-emerald-700 font-semibold flex items-center gap-1">
                    <i class="fa-solid fa-circle-check text-[10px]"></i> Payout Berhasil
                </div>
            </div>

            {{-- Pending Withdrawal --}}
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-card flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500">Sedang Diproses</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center text-sm border border-amber-200">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-xl font-extrabold {{ $pendingWithdrawal > 0 ? 'text-amber-600' : 'text-slate-900' }}">
                        Rp {{ number_format($pendingWithdrawal, 0, ',', '.') }}
                    </h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Menunggu transfer admin</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-500">
                    Status permohonan aktif
                </div>
            </div>
        </div>

        {{-- Withdrawal History Table Card --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-card overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-slate-400 text-sm"></i>
                    <h3 class="font-bold text-sm text-slate-900">Riwayat Penarikan Dana (Payout History)</h3>
                </div>
                <span class="text-xs text-slate-400">{{ $withdrawals->total() }} Transaksi</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="py-3.5 px-5">ID & Tanggal</th>
                            <th class="py-3.5 px-5">Jumlah Penarikan</th>
                            <th class="py-3.5 px-5">Rekening Tujuan</th>
                            <th class="py-3.5 px-5">Status</th>
                            <th class="py-3.5 px-5">Catatan Admin / Bukti</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($withdrawals as $w)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-5">
                                <span class="font-mono font-bold text-slate-800">#WD-{{ $w->id }}</span>
                                <span class="block text-[10px] text-slate-400 mt-0.5">{{ $w->created_at->translatedFormat('d M Y, H:i') }}</span>
                            </td>
                            <td class="py-4 px-5 font-extrabold text-sm text-slate-900">
                                Rp {{ number_format($w->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-5">
                                <span class="font-bold text-slate-800">{{ $w->bank_name }}</span>
                                <span class="block font-mono text-[11px] text-slate-500">{{ $w->account_number }}</span>
                                <span class="block text-[10px] text-slate-400">a.n. {{ $w->account_holder }}</span>
                            </td>
                            <td class="py-4 px-5">
                                @if($w->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="fa-solid fa-clock text-[9px]"></i> Menunggu Admin
                                    </span>
                                @elseif($w->status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fa-solid fa-circle-check text-[9px]"></i> Ditransfer
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="fa-solid fa-circle-xmark text-[9px]"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-5 max-w-xs">
                                @if($w->admin_note)
                                    <p class="text-[11px] text-slate-700">{{ $w->admin_note }}</p>
                                @endif
                                @if($w->proof_url)
                                    <a href="{{ asset('storage/' . $w->proof_url) }}" target="_blank"
                                       class="inline-flex items-center gap-1 text-[10px] font-semibold text-cyan-700 hover:underline mt-1">
                                        <i class="fa-solid fa-paperclip text-[9px]"></i> Lihat Bukti Transfer
                                    </a>
                                @elseif(!$w->admin_note)
                                    <span class="text-slate-400 text-[10px]">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-receipt text-3xl mb-2 text-slate-300"></i>
                                <p class="text-xs font-semibold text-slate-600">Belum Ada Riwayat Penarikan Dana</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Saldo yang Anda tarik ke rekening akan dicatat rapi di sini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($withdrawals->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $withdrawals->links() }}
                </div>
            @endif
        </div>

        {{-- Modal Tarik Dana --}}
        <div x-show="showWithdrawModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="showWithdrawModal = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                    <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-money-bill-transfer text-emerald-600"></i> Tarik Saldo Toko
                    </h3>
                    <button @click="showWithdrawModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form action="{{ route('seller.wallet.withdraw') }}" method="POST" class="mt-4 space-y-4 text-xs">
                    @csrf

                    <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200">
                        <span class="text-[10px] font-semibold text-emerald-700 uppercase tracking-wider block">Saldo Tersedia Saat Ini</span>
                        <span class="text-lg font-extrabold text-emerald-900 block mt-0.5">Rp {{ number_format($store->balance, 0, ',', '.') }}</span>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jumlah Penarikan (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-400">Rp</span>
                            <input type="number" name="amount" x-model="withdrawAmount" min="10000" max="{{ $store->balance }}"
                                   placeholder="Contoh: 100000"
                                   required
                                   class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:border-emerald-600 focus:ring-1 focus:ring-emerald-500 font-bold">
                        </div>
                        <div class="flex items-center justify-between mt-1 text-[10px] text-slate-400">
                            <span>Minimal penarikan Rp 10.000</span>
                            <button type="button" @click="withdrawAmount = {{ (int) $store->balance }}" class="text-emerald-600 font-bold hover:underline cursor-pointer">Tarik Semua</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nama Bank</label>
                            <select name="bank_name" x-model="bankName" required class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs focus:border-emerald-600">
                                <option value="BCA">BCA</option>
                                <option value="Bank Mandiri">Bank Mandiri</option>
                                <option value="BRI">BRI</option>
                                <option value="BNI">BNI</option>
                                <option value="BSI">BSI</option>
                                <option value="CIMB Niaga">CIMB Niaga</option>
                                <option value="SeaBank">SeaBank</option>
                                <option value="Jago">Bank Jago</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nomor Rekening</label>
                            <input type="text" name="account_number" x-model="accountNumber" placeholder="Contoh: 8271928192" required
                                   class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs focus:border-emerald-600 font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Pemilik Rekening</label>
                        <input type="text" name="account_holder" x-model="accountHolder" placeholder="Sesuai buku tabungan" required
                               class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs focus:border-emerald-600">
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="showWithdrawModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold shadow-xs cursor-pointer">
                            Ajukan Penarikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-seller-layout>
