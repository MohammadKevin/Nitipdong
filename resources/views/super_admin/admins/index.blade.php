<x-super-admin-layout>
    <x-slot name="title">Kelola Admin Operasional — NitipDong</x-slot>

    <div x-data="{
        showCreateModal: false,
        showEditModal: false,
        editData: {
            id: null,
            name: '',
            email: '',
            phone: '',
            actionUrl: ''
        },
        openEdit(admin, actionUrl) {
            this.editData = {
                id: admin.id,
                name: admin.name,
                email: admin.email,
                phone: admin.phone || '',
                actionUrl: actionUrl
            };
            this.showEditModal = true;
        }
    }" class="space-y-6">

        {{-- Top Header & Action --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                    <i class="fa-solid fa-user-shield text-cyan-600"></i>
                    Admin Operasional
                </h1>
                <p class="text-xs text-slate-500 mt-1">Kelola daftar akun staf administrator yang berhak memoderasi produk, toko, dan komplain pelanggan.</p>
            </div>
            
            <button type="button" @click="showCreateModal = true"
                    class="px-4 py-2.5 bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs rounded-xl shadow-xs flex items-center gap-2 transition-all active:scale-95 cursor-pointer shrink-0">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Admin Baru</span>
            </button>
        </div>

        {{-- Notification Alerts --}}
        @if(session('success'))
            <div class="flex items-center gap-2.5 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl text-xs font-semibold animate-fade-up">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl text-xs font-medium space-y-1 animate-fade-up">
                <div class="font-bold flex items-center gap-1.5 text-rose-800">
                    <i class="fa-solid fa-circle-exclamation text-sm"></i> Terjadi Kesalahan:
                </div>
                <ul class="list-disc list-inside pl-1 text-[11px] text-rose-700">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Statistics & Search Toolbar --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-700 border border-cyan-200 flex items-center justify-center text-base shrink-0">
                    <i class="fa-solid fa-users-gear"></i>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Admin</span>
                    <span class="text-xl font-black text-slate-900">{{ $totalAdmins }} Staf</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs flex items-center gap-3.5 sm:col-span-2">
                <form action="{{ route('super_admin.admins.index') }}" method="GET" class="w-full flex items-center gap-2">
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari nama admin, alamat email, atau no. telepon..."
                               class="w-full h-10 pl-9 pr-4 rounded-xl border border-slate-200 bg-slate-50 text-xs focus:bg-white focus:border-cyan-600 focus:ring-1 focus:ring-cyan-500 font-medium">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-3 top-3.5 text-xs"></i>
                    </div>
                    <button type="submit" class="h-10 px-4 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition-colors cursor-pointer shrink-0">
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('super_admin.admins.index') }}" class="h-10 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs rounded-xl flex items-center justify-center transition-colors">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Table List Admin --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 text-slate-500 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3.5 px-4">Admin / Pengguna</th>
                            <th class="py-3.5 px-4">Kontak &amp; Telepon</th>
                            <th class="py-3.5 px-4">Peran / Hak Akses</th>
                            <th class="py-3.5 px-4">Tanggal Dibuat</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($admins as $admin)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $admin->avatar_url }}" alt="{{ $admin->name }}" class="w-9 h-9 rounded-xl object-cover border border-slate-200 bg-slate-100">
                                        <div>
                                            <p class="font-bold text-slate-900 text-xs">{{ $admin->name }}</p>
                                            <p class="text-[11px] text-slate-400 font-mono">{{ $admin->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-mono text-slate-600">{{ $admin->phone ?: '-' }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-cyan-50 text-cyan-800 border border-cyan-200">
                                        <i class="fa-solid fa-shield-halved text-[9px]"></i> Admin Operasional
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-500">
                                    {{ $admin->created_at->translatedFormat('d M Y, H:i') }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        {{-- Edit Button --}}
                                        <button type="button"
                                                @click="openEdit({{ $admin->toJson() }}, '{{ route('super_admin.admins.update', $admin) }}')"
                                                class="px-2.5 py-1.5 bg-slate-100 hover:bg-cyan-50 hover:text-cyan-800 text-slate-700 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1 cursor-pointer"
                                                title="Edit Admin">
                                            <i class="fa-solid fa-pen-to-square text-[11px]"></i> Edit
                                        </button>

                                        {{-- Delete Button --}}
                                        <form action="{{ route('super_admin.admins.destroy', $admin) }}" method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun admin {{ addslashes($admin->name) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="p-1.5 bg-slate-100 hover:bg-rose-50 text-slate-400 hover:text-rose-600 rounded-lg text-xs transition-colors cursor-pointer"
                                                    title="Hapus Admin">
                                                <i class="fa-regular fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2 text-lg">
                                        <i class="fa-solid fa-user-shield"></i>
                                    </div>
                                    <p class="font-bold text-slate-700 text-xs">Belum Ada Admin Operasional</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Klik tombol Tambah Admin Baru di atas untuk mendaftarkan akun admin staf.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($admins->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $admins->links() }}
                </div>
            @endif
        </div>

        {{-- MODAL TAMBAH ADMIN BARU --}}
        <div x-show="showCreateModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="showCreateModal = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 text-xs">
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                    <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-user-plus text-cyan-600"></i> Tambah Admin Operasional
                    </h3>
                    <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 w-7 h-7 rounded-lg flex items-center justify-center hover:bg-slate-100 transition-colors cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form action="{{ route('super_admin.admins.store') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap Admin <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required placeholder="Contoh: Budi Prasetyo" class="input text-xs">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" required placeholder="admin.budi@nitipdong.com" class="input text-xs font-mono">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nomor WhatsApp / HP (Opsional)</label>
                        <input type="text" name="phone" placeholder="081234567890" class="input text-xs font-mono">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Password Baru <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required placeholder="Minimal 6 karakter" class="input text-xs">
                    </div>

                    <div class="p-3 bg-cyan-50/70 rounded-xl border border-cyan-200 text-cyan-900 text-[11px] leading-relaxed">
                        <i class="fa-solid fa-circle-info text-cyan-700 mr-1"></i>
                        Akun yang dibuat akan langsung memiliki peran <strong>Admin Operasional</strong> dengan akses ke panel moderasi toko, produk, kategori, dan komplain pelanggan.
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-bold shadow-xs cursor-pointer">
                            Simpan Admin
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL EDIT ADMIN --}}
        <div x-show="showEditModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="showEditModal = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 text-xs">
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                    <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-user-pen text-cyan-600"></i> Edit Data Admin
                    </h3>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 w-7 h-7 rounded-lg flex items-center justify-center hover:bg-slate-100 transition-colors cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form :action="editData.actionUrl" method="POST" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" x-model="editData.name" required class="input text-xs">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alamat Email</label>
                        <input type="email" name="email" x-model="editData.email" required class="input text-xs font-mono">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nomor WhatsApp / HP</label>
                        <input type="text" name="phone" x-model="editData.phone" class="input text-xs font-mono">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Ganti Password (Kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" placeholder="Masukkan password baru..." class="input text-xs">
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-bold shadow-xs cursor-pointer">
                            Perbarui Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-super-admin-layout>
