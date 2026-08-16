<x-dynamic-component :component="auth()->user()->role === 'super_admin' ? 'super-admin-layout' : 'app-layout'">
    @if(auth()->user()->role === 'super_admin')
        <x-slot name="title">
            {{ __('Pesan & Percakapan') }}
        </x-slot>
    @else
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-[#14213D] leading-tight">
                {{ __('Pesan & Percakapan') }}
            </h2>
        </x-slot>
    @endif

    <div class="py-6 sm:py-8 h-[calc(100vh-100px)] flex">
        <div class="w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col h-full">
            
            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6 shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-[#14213D]" style="font-family:'Poppins',sans-serif;">Pesan Masuk</h1>
                    <p class="text-xs text-[#8A93A6] mt-0.5">Kelola obrolan Anda dengan pengguna lain.</p>
                </div>

                @if(auth()->user()->role === 'super_admin' && isset($admins) && $admins->isNotEmpty())
                    <form id="startAdminChatForm" method="POST" class="flex gap-2 w-full sm:w-auto" onsubmit="
                        const select = document.getElementById('admin_select');
                        if(!select.value) { alert('Pilih admin terlebih dahulu!'); return false; }
                        this.action = '/chat/start/' + select.value;
                        return true;
                    ">
                        @csrf
                        <select id="admin_select" class="text-xs rounded-xl border-[#E7E3D8] focus:ring-[#12A57F] focus:border-[#12A57F] bg-white flex-1 sm:w-48 py-2.5">
                            <option value="">-- Chat dengan Admin --</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-[#12A57F] text-white px-4 py-2.5 rounded-xl text-xs font-semibold hover:bg-[#0F8E6D] transition-colors whitespace-nowrap shadow-sm">Mulai Chat</button>
                    </form>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-[#EFEBDF] flex-1 overflow-hidden flex flex-col">
                <div class="divide-y divide-[#F0EEE6] overflow-y-auto">
                    @forelse ($conversations as $conv)
                        @php
                            $partner = $conv->user_one_id === auth()->id() ? $conv->userTwo : $conv->userOne;
                            $lastMsg = $conv->messages->first();
                        @endphp
                        <a href="{{ route('chat.show', $conv) }}" class="p-4 sm:p-6 flex items-center justify-between hover:bg-[#FAF9F5] transition-colors block">
                            <div class="flex items-center gap-4 min-w-0">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($partner->name) }}&background=12A57F&color=fff" class="w-12 h-12 rounded-full object-cover shrink-0 border border-[#E7E3D8]" alt="User">
                                <div class="min-w-0">
                                    <h4 class="font-bold text-[#14213D] truncate">{{ $partner->name }} <span class="text-[10px] font-semibold text-[#12A57F] bg-[#E9F8F2] px-2 py-0.5 rounded-full ml-1 capitalize">{{ $partner->role }}</span></h4>
                                    <p class="text-xs text-[#8A93A6] truncate mt-1">
                                        {{ $lastMsg ? $lastMsg->message : 'Mulai percakapan baru...' }}
                                    </p>
                                </div>
                            </div>
                            <span class="text-[10px] text-[#B3ACA0] whitespace-nowrap shrink-0 ml-4">{{ $conv->updated_at->diffForHumans() }}</span>
                        </a>
                    @empty
                        <div class="p-16 flex flex-col items-center justify-center text-center">
                            <div class="w-16 h-16 bg-[#FAF9F5] rounded-full flex items-center justify-center text-[#B3ACA0] mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <h3 class="text-[#14213D] font-bold text-sm">Belum ada obrolan</h3>
                            <p class="text-xs text-[#8A93A6] mt-1 max-w-xs">Riwayat pesan Anda dengan pengguna lain akan muncul di sini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>