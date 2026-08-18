<section>
    <header>
        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
            Informasi Profil Pengguna
        </h2>

        <p class="mt-1 text-xs text-slate-500">
            Perbarui nama lengkap dan alamat email yang terhubung dengan akun BelanjaIn Anda.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send.otp') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-5 space-y-4">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
            <input id="name" name="name" type="text" class="input text-xs rounded-md" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            <x-input-error class="mt-1.5 text-xs text-rose-500" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
            <input id="email" name="email" type="email" class="input text-xs rounded-md" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            <x-input-error class="mt-1.5 text-xs text-rose-500" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-xs text-amber-700 bg-amber-50 p-2.5 rounded border border-amber-200">
                        Email Anda belum terverifikasi.
                        <button form="send-verification" class="underline font-bold text-amber-900 ml-1">
                            Kirim ulang kode verifikasi OTP
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-xs font-semibold text-emerald-600">
                            Kode verifikasi baru telah dikirimkan ke email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="btn-primary text-xs h-9 px-4 rounded-md bg-cyan-700 hover:bg-cyan-800">
                Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-xs font-semibold text-emerald-600"
                >Berhasil disimpan.</p>
            @endif
        </div>
    </form>
</section>
