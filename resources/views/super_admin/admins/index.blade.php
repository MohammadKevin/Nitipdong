<x-super-admin-layout>
    <x-slot name="title">Kelola Admin Operasional — NitipDong</x-slot>
    <x-slot name="pageTitle">Admin Operasional</x-slot>

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
    }" class="space-y-5">

        {{-- Top Header & Action --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-1">
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    Admin Operasional Platform
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Kelola daftar akun staf administrator yang berhak memoderasi produk, toko, dan komplain pelanggan.</p>
            </div>
            
            <button type="button" @click="showCreateModal = true"
                    class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-lg shadow-xs flex items-center gap-2 transition-colors cursor-pointer shrink-0">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Admin Baru</span>
            </button>
        </div>

        {{-- Notification Alerts --}}
        @if(session('success'))
            <div class="flex items-center gap-2.5 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-lg text-xs font-semibold shadow-xs">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-lg text-xs font-medium space-y-1 shadow-xs">
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
            <div class="bg-white p-4 rounded-lg border border-slate-200/90 shadow-xs flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-700 border border-blue-200/60 flex items-center justify-center text-sm shrink-0 font-mono-num">
                    <i class="fa-solid fa-users-gear"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block font-mono-num">Total Admin</span>
                    <span class="text-xl font-bold text-slate-900 font-mono-num">{{ $totalAdmins }} Staf</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg border border-slate-200/90 shadow-xs flex items-center gap-3 sm:col-span-2">
                <form action="{{ route('super_admin.admins.index') }}" method="GET" class="w-full flex items-center gap-2">
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari nama admin, alamat email, atau no. telepon..."
                               class="w-full h-8.5 pl-8 pr-3 rounded-lg border border-slate-200 bg-slate-50 text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono-num placeholder:text-slate-400 transition-colors">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 text-xs"></i>
                    </div>
                    <button type="submit" class="h-8.5 px-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-lg transition-colors cursor-pointer shrink-0 shadow-xs">
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('super_admin.admins.index') }}" class="h-8.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-lg flex items-center justify-center transition-colors border border-slate-200">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Table List Admin --}}
        <div class="bg-white rounded-lg border border-slate-200/90 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100 font-mono-num">
                        <tr>
                            <th class="py-3 px-5">Admin / Pengguna</th>
                            <th class="py-3 px-5">Kontak &amp; Telepon</th>
                            <th class="py-3 px-5">Peran / Hak Akses</th>
                            <th class="py-3 px-5">Tanggal Dibuat</th>
                            <th class="py-3 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($admins as $admin)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-5">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $admin->avatar_url }}" alt="{{ $admin->name }}" class="w-8 h-8 rounded-lg object-cover border border-slate-200 bg-slate-100">
                                        <div>
                                            <p class="font-semibold text-slate-900 text-xs">{{ $admin->name }}</p>
                                            <p class="text-[11px] text-slate-400 font-mono-num">{{ $admin->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-5">
                                    <span class="font-mono-num text-slate-600">{{ $admin->phone ?: '-' }}</span>
                                </td>
                                <td class="py-3.5 px-5">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200/60 font-mono-num">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> Admin Operasional
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 text-slate-400 font-mono-num text-[11px]">
                                    {{ $admin->created_at->translatedFormat('d M Y, H:i') }}
                                </td>
                                <td class="py-3.5 px-5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        {{-- Edit Button --}}
                                        <button type="button"
                                                @click="openEdit({{ $admin->toJson() }}, '{{ route('super_admin.admins.update', $admin) }}')"
                                                class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-md text-xs font-semibold transition-colors flex items-center gap-1 cursor-pointer border border-slate-200 shadow-2xs"
                                                title="Edit Admin">
                                            <i class="fa-solid fa-pen-to-square text-[10px]"></i> Edit
                                        </button>

                                        {{-- Delete Button --}}
                                        <form action="{{ route('super_admin.admins.destroy', $admin) }}" method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun admin {{ addslashes($admin->name) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="p-1 bg-slate-100 hover:bg-rose-50 text-slate-400 hover:text-rose-600 rounded-md text-xs transition-colors cursor-pointer border border-slate-200"
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
                                    <i class="fa-solid fa-user-shield text-3xl mb-2 text-slate-300"></i>
                                    <p class="font-bold text-slate-700 text-xs">Belum Ada Admin Operasional</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Klik tombol Tambah Admin Baru di atas untuk mendaftarkan akun staf.</p>
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
                 class="bg-white rounded-xl max-w-md w-full p-5 shadow-xl border border-slate-200 text-xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-user-plus text-blue-600"></i> Tambah Admin Operasional
                    </h3>
                    <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-md hover:bg-slate-100 transition-colors cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <form action="{{ route('super_admin.admins.store') }}" method="POST" class="mt-4 space-y-3.5">
                    @csrf
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap Admin <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required placeholder="Contoh: Budi Prasetyo" class="w-full h-8.5 px-3 rounded-lg border border-slate-200 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" required placeholder="admin.budi@nitipdong.com" class="w-full h-8.5 px-3 rounded-lg border border-slate-200 text-xs font-mono-num focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nomor WhatsApp / HP (Opsional)</label>
                        <input type="text" name="phone" placeholder="081234567890" class="w-full h-8.5 px-3 rounded-lg border border-slate-200 text-xs font-mono-num focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Password Baru <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required placeholder="Minimal 6 karakter" class="w-full h-8.5 px-3 rounded-lg border border-slate-200 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>

                    <div class="p-3 bg-blue-50/70 rounded-lg border border-blue-200/60 text-blue-950 text-[11px] leading-relaxed">
                        <i class="fa-solid fa-circle-info text-blue-700 mr-1"></i>
                        Akun yang dibuat akan langsung memiliki peran <strong>Admin Operasional</strong> dengan akses ke panel moderasi toko, produk, kategori, dan komplain pelanggan.
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="showCreateModal = false" class="px-3.5 py-1.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-xs cursor-pointer">
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
                 class="bg-white rounded-xl max-w-md w-full p-5 shadow-xl border border-slate-200 text-xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-user-pen text-blue-600"></i> Edit Data Admin
                    </h3>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-md hover:bg-slate-100 transition-colors cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <form :action="editData.actionUrl" method="POST" class="mt-4 space-y-3.5">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" x-model="editData.name" required class="w-full h-8.5 px-3 rounded-lg border border-slate-200 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alamat Email</label>
                        <input type="email" name="email" x-model="editData.email" required class="w-full h-8.5 px-3 rounded-lg border border-slate-200 text-xs font-mono-num focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nomor WhatsApp / HP</label>
                        <input type="text" name="phone" x-model="editData.phone" class="w-full h-8.5 px-3 rounded-lg border border-slate-200 text-xs font-mono-num focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Ganti Password (Kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" placeholder="Masukkan password baru..." class="w-full h-8.5 px-3 rounded-lg border border-slate-200 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="showEditModal = false" class="px-3.5 py-1.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-xs cursor-pointer">
                            Perbarui Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-super-admin-layout>
