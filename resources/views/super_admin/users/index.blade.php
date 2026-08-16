<x-super-admin-layout>
    <x-slot name="title">
        Daftar Pengguna - {{ config('app.name', 'BelanjaIn') }}
    </x-slot>

    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-[#14213D]" style="font-family:'Poppins',sans-serif;">Daftar Pengguna</h1>
            <p class="text-xs text-[#8A93A6] mt-0.5">Kelola seluruh pengguna yang terdaftar di platform BelanjaIn.</p>
        </div>

        <form action="{{ route('super_admin.users.index') }}" method="GET" class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-72">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau email..." class="w-full bg-white border border-[#E7E3D8] rounded-xl pl-10 pr-4 py-2.5 text-xs focus:ring-2 focus:ring-[#12A57F]/20 focus:border-[#12A57F] transition-all">
                <svg class="w-4 h-4 text-[#B3ACA0] absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button type="submit" class="h-10 px-4 rounded-xl bg-[#12A57F] text-white text-xs font-semibold hover:bg-[#0F8E6D] transition-colors shadow-sm whitespace-nowrap">Cari</button>
            @if($search)
                <a href="{{ route('super_admin.users.index') }}" class="h-10 px-4 rounded-xl bg-white border border-[#E7E3D8] text-[#4B5566] text-xs font-semibold hover:bg-[#F0EEE6] transition-colors flex items-center justify-center whitespace-nowrap">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-[#EFEBDF] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-[#B3ACA0] border-b border-[#F0EEE6] font-medium bg-[#FAF9F5]">
                        <th class="py-4 px-6 font-medium">ID</th>
                        <th class="py-4 px-6 font-medium">Pengguna</th>
                        <th class="py-4 px-6 font-medium">Email</th>
                        <th class="py-4 px-6 font-medium">Role</th>
                        <th class="py-4 px-6 font-medium">Bergabung</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F5F3EE]">
                    @forelse($users as $user)
                    <tr class="hover:bg-[#FAF9F5] transition-colors">
                        <td class="py-3 px-6 font-mono text-[#B3ACA0]">{{ $user->id }}</td>
                        <td class="py-3 px-6">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" class="w-8 h-8 rounded-full object-cover shrink-0" alt="Avatar">
                                <div>
                                    <h4 class="font-semibold text-[#14213D]">{{ $user->name }}</h4>
                                    <p class="text-[10px] text-[#8A93A6] truncate max-w-[120px]">{{ $user->phone ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-6 text-[#4B5566]">{{ $user->email }}</td>
                        <td class="py-3 px-6">
                            @if($user->role === 'super_admin')
                                <span class="px-2.5 py-1 rounded-full bg-[#EAF1FF] text-[#3E6FE0] font-semibold text-[10px]">Super Admin</span>
                            @elseif($user->role === 'admin')
                                <span class="px-2.5 py-1 rounded-full bg-[#F3EEFC] text-[#8B5CF6] font-semibold text-[10px]">Admin</span>
                            @elseif($user->role === 'seller')
                                <span class="px-2.5 py-1 rounded-full bg-[#FFF6E7] text-[#C7860B] font-semibold text-[10px]">Seller</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-[#E9F8F2] text-[#12A57F] font-semibold text-[10px]">Customer</span>
                            @endif
                        </td>
                        <td class="py-3 px-6 text-[#8A93A6]">{{ $user->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-[#B3ACA0]">Tidak ada pengguna ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
        <div class="p-4 border-t border-[#F0EEE6] bg-white">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</x-super-admin-layout>
