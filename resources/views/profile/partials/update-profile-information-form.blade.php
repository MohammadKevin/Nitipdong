<section x-data="{ avatarPreview: '{{ $user->avatar_url }}' }">
    <header class="border-b border-slate-100 pb-4">
        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fa-regular fa-id-card text-cyan-700"></i>
            Informasi Profil & Data Diri
        </h2>
        <p class="mt-1 text-xs text-slate-500">
            Perbarui foto profil, nama lengkap, nomor telepon, dan alamat email Anda.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send.otp') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-5 space-y-5">
        @csrf
        @method('patch')

        {{-- Foto Profil Upload Box --}}
        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Foto Profil</label>
            <div class="flex items-center gap-4">
                <div class="relative shrink-0">
                    <img :src="avatarPreview" alt="Avatar Preview" class="w-16 h-16 rounded-2xl object-cover border-2 border-slate-200 shadow-2xs">
                    <label for="avatar" class="absolute -bottom-1 -right-1 w-6 h-6 rounded-lg bg-cyan-700 hover:bg-cyan-800 text-white flex items-center justify-center cursor-pointer shadow-xs transition-colors" title="Ganti Foto">
                        <i class="fa-solid fa-camera text-[10px]"></i>
                    </label>
                </div>
                <div class="flex-1 min-w-0">
                    <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden"
                           @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { avatarPreview = e.target.result; }; reader.readAsDataURL(file); }">
                    <label for="avatar" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 hover:border-cyan-600 bg-slate-50 hover:bg-white text-xs font-semibold text-slate-700 cursor-pointer transition-all shadow-2xs">
                        <i class="fa-solid fa-arrow-up-from-bracket text-[11px] text-cyan-700"></i>
                        Pilih Foto Baru
                    </label>
                    <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, WEBP. Maksimal 2MB.</p>
                </div>
            </div>
            <x-input-error class="mt-1.5 text-xs text-rose-500" :messages="$errors->get('avatar')" />
        </div>

        {{-- Nama Lengkap --}}
        <div>
            <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-regular fa-user text-xs"></i>
                </div>
                <input id="name" name="name" type="text" class="input text-xs rounded-xl !pl-11 h-10 w-full" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            </div>
            <x-input-error class="mt-1.5 text-xs text-rose-500" :messages="$errors->get('name')" />
        </div>

        {{-- Alamat Email --}}
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-regular fa-envelope text-xs"></i>
                </div>
                <input id="email" name="email" type="email" class="input text-xs rounded-xl !pl-11 h-10 w-full" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            </div>
            <x-input-error class="mt-1.5 text-xs text-rose-500" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2.5">
                    <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-800 flex items-start gap-2">
                        <i class="fa-solid fa-circle-exclamation text-amber-500 mt-0.5 shrink-0"></i>
                        <div>
                            <span>Email Anda belum terverifikasi.</span>
                            <button form="send-verification" class="underline font-bold text-amber-900 hover:text-amber-950 ml-1">
                                Kirim ulang kode verifikasi OTP
                            </button>
                        </div>
                    </div>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-xs font-semibold text-emerald-600 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check"></i>
                            Kode verifikasi baru telah dikirimkan ke email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Nomor Telepon --}}
        <div>
            <label for="phone" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nomor Telepon / WhatsApp</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-phone text-xs"></i>
                </div>
                <input id="phone" name="phone" type="text" placeholder="08xxxxxxxxxx" class="input text-xs rounded-xl !pl-11 h-10 w-full" value="{{ old('phone', $user->phone) }}" autocomplete="tel" />
            </div>
            <x-input-error class="mt-1.5 text-xs text-rose-500" :messages="$errors->get('phone')" />
        </div>

        {{-- Submit Action --}}
        <div class="flex items-center gap-3 pt-3 border-t border-slate-100">
            <button type="submit" class="btn-primary text-xs h-10 px-5 rounded-xl bg-cyan-700 hover:bg-cyan-800 font-semibold shadow-xs flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-xs font-semibold text-emerald-600 flex items-center gap-1.5"
                >
                    <i class="fa-solid fa-circle-check"></i>
                    Profil berhasil diperbarui!
                </div>
            @endif
        </div>
    </form>
</section>
