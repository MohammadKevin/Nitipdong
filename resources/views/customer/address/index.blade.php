<x-app-layout>
    <div class="page-container py-5" x-data="{
        showCreateModal: false,
        showEditModal: false,
        editData: {
            id: null,
            label: 'Rumah',
            recipient_name: '',
            phone: '',
            full_address: '',
            city: '',
            province: '',
            postal_code: '',
            is_default: false,
            actionUrl: ''
        },
        openEdit(addr, url) {
            this.editData = {
                id: addr.id,
                label: addr.label || 'Rumah',
                recipient_name: addr.recipient_name,
                phone: addr.phone,
                full_address: addr.full_address,
                city: addr.city || '',
                province: addr.province || '',
                postal_code: addr.postal_code || '',
                is_default: Boolean(addr.is_default),
                actionUrl: url
            };
            this.showEditModal = true;
        }
    }">
        <nav class="flex text-xs text-slate-400 mb-4 items-center gap-1.5 flex-wrap" aria-label="Breadcrumb">
            <a href="{{ auth()->check() ? url('/?is_from_login=true') : url('/') }}" class="hover:text-cyan-700 transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <a href="{{ route('customer.dashboard') }}" class="hover:text-cyan-700 transition-colors">Akun Saya</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <span class="text-slate-700 font-medium">Buku Alamat Pengiriman</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-cyan-600 text-lg"></i>
                    Buku Alamat Pengiriman
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Kelola daftar alamat tujuan untuk pengiriman pesanan belanjaanmu</p>
            </div>
            <button @click="showCreateModal = true"
                    class="btn-primary h-9 px-4 text-xs flex items-center gap-2 bg-cyan-700 hover:bg-cyan-800 rounded-xl shadow-xs">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Alamat Baru</span>
            </button>
        </div>

        @if(session('success'))
            <div class="mb-5 flex items-center gap-2.5 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl text-xs font-semibold">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs space-y-1">
                @foreach($errors->all() as $error)
                    <p class="flex items-center gap-1.5"><i class="fa-solid fa-circle-exclamation text-rose-600"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if($addresses->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                @foreach($addresses as $addr)
                    <div class="bg-white rounded-xl border {{ $addr->is_default ? 'border-cyan-500 ring-2 ring-cyan-500/20' : 'border-slate-200/80' }} p-5 shadow-card flex flex-col justify-between relative transition-all">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $addr->label === 'Kantor' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                        <i class="fa-solid {{ $addr->label === 'Kantor' ? 'fa-building' : 'fa-house' }} text-[9px] mr-1"></i>
                                        {{ $addr->label }}
                                    </span>
                                    @if($addr->is_default)
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-cyan-50 text-cyan-800 border border-cyan-200 flex items-center gap-1">
                                            <i class="fa-solid fa-check-circle text-[9px] text-cyan-600"></i> Utama
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-1">
                                    <button @click="openEdit({{ $addr->toJson() }}, '{{ route('customer.addresses.update', $addr) }}')"
                                            class="w-7 h-7 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-cyan-700 flex items-center justify-center text-xs transition-colors" title="Edit Alamat">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    @if(!$addr->is_default)
                                    <form action="{{ route('customer.addresses.destroy', $addr) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus alamat ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-7 h-7 rounded-lg text-slate-400 hover:bg-rose-50 hover:text-rose-600 flex items-center justify-center text-xs transition-colors" title="Hapus Alamat">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>

                            <h3 class="text-sm font-bold text-slate-900 leading-tight">{{ $addr->recipient_name }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5 font-mono">{{ $addr->phone }}</p>

                            <p class="text-xs text-slate-700 mt-2.5 leading-relaxed whitespace-pre-line">
                                {{ $addr->full_address }}
                            </p>

                            @if($addr->city || $addr->province || $addr->postal_code)
                                <p class="text-[11px] text-slate-400 mt-1">
                                    {{ implode(', ', array_filter([$addr->city, $addr->province, $addr->postal_code])) }}
                                </p>
                            @endif
                        </div>

                        <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                            @if(!$addr->is_default)
                                <form action="{{ route('customer.addresses.set_default', $addr) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-semibold text-cyan-700 hover:text-cyan-800 hover:underline flex items-center gap-1">
                                        <i class="fa-regular fa-star text-[10px]"></i> Jadikan Alamat Utama
                                    </button>
                                </form>
                            @else
                                <span class="text-[11px] font-semibold text-emerald-600 flex items-center gap-1">
                                    <i class="fa-solid fa-shield-check text-xs"></i> Alamat pengiriman aktif saat ini
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center shadow-card max-w-lg mx-auto">
                <div class="w-16 h-16 rounded-full bg-cyan-50 border border-cyan-100 text-cyan-600 flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Alamat Pengiriman</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                    Simpan alamat rumah atau kantormu agar proses checkout belanjaan lebih cepat dan praktis.
                </p>
                <button @click="showCreateModal = true" class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white text-xs font-semibold shadow-sm transition-all">
                    <i class="fa-solid fa-plus"></i>
                    Tambah Alamat Sekarang
                </button>
            </div>
        @endif

        {{-- Modal Tambah Alamat --}}
        <div x-show="showCreateModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="showCreateModal = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                    <h3 class="font-bold text-sm text-slate-900">Tambah Alamat Baru</h3>
                    <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form action="{{ route('customer.addresses.store') }}" method="POST" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Label Alamat</label>
                        <select name="label" class="w-full h-9 rounded-lg border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                            <option value="Rumah">Rumah</option>
                            <option value="Kantor">Kantor</option>
                            <option value="Apartemen">Apartemen</option>
                            <option value="Kos">Kos</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nama Penerima <span class="text-rose-500">*</span></label>
                            <input type="text" name="recipient_name" required placeholder="Nama Lengkap"
                                   class="w-full h-9 rounded-lg border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nomor Telepon/HP <span class="text-rose-500">*</span></label>
                            <input type="text" name="phone" required placeholder="08123456789"
                                   class="w-full h-9 rounded-lg border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alamat Lengkap & Patokan <span class="text-rose-500">*</span></label>
                        <textarea name="full_address" rows="3" required placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, patokan..."
                                  class="w-full rounded-lg border border-slate-300 text-xs p-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500"></textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-2.5">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kota/Kab</label>
                            <input type="text" name="city" placeholder="Kota"
                                   class="w-full h-9 rounded-lg border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Provinsi</label>
                            <input type="text" name="province" placeholder="Provinsi"
                                   class="w-full h-9 rounded-lg border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kode Pos</label>
                            <input type="text" name="postal_code" placeholder="12345"
                                   class="w-full h-9 rounded-lg border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                    </div>

                    <label class="flex items-center gap-2 pt-1 cursor-pointer">
                        <input type="checkbox" name="is_default" value="1" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                        <span class="text-slate-700 font-medium">Jadikan sebagai alamat utama (default)</span>
                    </label>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-lg bg-cyan-700 hover:bg-cyan-800 text-white font-semibold shadow-xs">
                            Simpan Alamat
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Edit Alamat --}}
        <div x-show="showEditModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="showEditModal = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                    <h3 class="font-bold text-sm text-slate-900">Ubah Alamat Pengiriman</h3>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form :action="editData.actionUrl" method="POST" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Label Alamat</label>
                        <select name="label" x-model="editData.label" class="w-full h-9 rounded-lg border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                            <option value="Rumah">Rumah</option>
                            <option value="Kantor">Kantor</option>
                            <option value="Apartemen">Apartemen</option>
                            <option value="Kos">Kos</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nama Penerima <span class="text-rose-500">*</span></label>
                            <input type="text" name="recipient_name" x-model="editData.recipient_name" required
                                   class="w-full h-9 rounded-lg border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nomor Telepon/HP <span class="text-rose-500">*</span></label>
                            <input type="text" name="phone" x-model="editData.phone" required
                                   class="w-full h-9 rounded-lg border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alamat Lengkap & Patokan <span class="text-rose-500">*</span></label>
                        <textarea name="full_address" x-model="editData.full_address" rows="3" required
                                  class="w-full rounded-lg border border-slate-300 text-xs p-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500"></textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-2.5">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kota/Kab</label>
                            <input type="text" name="city" x-model="editData.city"
                                   class="w-full h-9 rounded-lg border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Provinsi</label>
                            <input type="text" name="province" x-model="editData.province"
                                   class="w-full h-9 rounded-lg border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kode Pos</label>
                            <input type="text" name="postal_code" x-model="editData.postal_code"
                                   class="w-full h-9 rounded-lg border border-slate-300 text-xs px-3 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500">
                        </div>
                    </div>

                    <label class="flex items-center gap-2 pt-1 cursor-pointer">
                        <input type="checkbox" name="is_default" value="1" :checked="editData.is_default" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                        <span class="text-slate-700 font-medium">Jadikan sebagai alamat utama (default)</span>
                    </label>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-lg bg-cyan-700 hover:bg-cyan-800 text-white font-semibold shadow-xs">
                            Perbarui Alamat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
