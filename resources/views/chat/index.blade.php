<x-dynamic-component :component="auth()->user()->role === 'super_admin' ? 'super-admin-layout' : (auth()->user()->role === 'admin' ? 'admin-layout' : 'app-layout')">
    @if(in_array(auth()->user()->role, ['super_admin', 'admin']))
        <x-slot name="title">
            {{ __('Pesan & Percakapan') }}
        </x-slot>
    @else
        <x-slot name="header">
            <h2 class="font-bold text-lg text-slate-900 leading-tight">
                {{ __('Pesan & Percakapan') }}
            </h2>
        </x-slot>
    @endif

    <div class="py-6 sm:py-8 h-[calc(100vh-100px)] flex">
        <div class="w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col h-full">
            
            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-4 shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Pesan & Percakapan</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola obrolan langsung Anda dengan pembeli, toko, atau admin.</p>
                </div>

                @if(auth()->user()->role === 'super_admin' && isset($admins) && $admins->isNotEmpty())
                    <form id="startAdminChatForm" method="POST" class="flex gap-2 w-full sm:w-auto" onsubmit="
                        const select = document.getElementById('admin_select');
                        if(!select.value) { alert('Pilih admin terlebih dahulu!'); return false; }
                        this.action = '/chat/start/' + select.value;
                        return true;
                    ">
                        @csrf
                        <select id="admin_select" class="input text-xs rounded-md bg-white flex-1 sm:w-48 h-9">
                            <option value="">-- Chat dengan Admin --</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->getRouteKey() }}">{{ $admin->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn-primary text-xs h-9 px-3.5 rounded-md bg-cyan-700 hover:bg-cyan-800 whitespace-nowrap">Mulai Chat</button>
                    </form>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-card border border-slate-200/80 flex-1 overflow-hidden flex flex-col">
                <div class="divide-y divide-slate-100 overflow-y-auto">
                    @forelse ($conversations as $conv)
                        @php
                            $partner = $conv->user_one_id === auth()->id() ? $conv->userTwo : $conv->userOne;
                            $lastMsg = $conv->messages->first();
                        @endphp
                        <a href="{{ route('chat.show', $conv) }}" class="p-4 sm:p-5 flex items-center justify-between hover:bg-slate-50/80 transition-colors block">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($partner->name) }}&background=0891b2&color=fff&size=50" class="w-10 h-10 rounded-full object-cover shrink-0 border border-slate-200" alt="User">
                                <div class="min-w-0">
                                    <h4 class="font-bold text-slate-900 text-xs truncate">
                                        {{ $partner->name }} 
                                        <span class="text-[9px] font-semibold text-cyan-800 bg-cyan-50 border border-cyan-200 px-1.5 py-0.5 rounded-full ml-1 uppercase">{{ $partner->role }}</span>
                                    </h4>
                                    <p class="text-xs text-slate-500 truncate mt-0.5">
                                        {{ $lastMsg ? $lastMsg->message : 'Mulai percakapan baru...' }}
                                    </p>
                                </div>
                            </div>
                            <span class="text-[10px] text-slate-400 whitespace-nowrap shrink-0 ml-4">{{ $conv->updated_at->diffForHumans() }}</span>
                        </a>
                    @empty
                        <div class="p-16 flex flex-col items-center justify-center text-center">
                            <div class="w-12 h-12 bg-cyan-50 text-cyan-700 rounded-xl flex items-center justify-center text-xl mb-3 border border-cyan-200">
                                <i class="fa-regular fa-comment-dots"></i>
                            </div>
                            <h3 class="text-slate-900 font-bold text-sm">Belum ada obrolan</h3>
                            <p class="text-xs text-slate-500 mt-1 max-w-xs">Riwayat pesan Anda dengan pengguna lain akan muncul di sini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>