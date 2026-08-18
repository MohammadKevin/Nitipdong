<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-[#14213D] mb-2" style="font-family:'Poppins',sans-serif;">Verifikasi Keamanan</h2>
        <p class="text-sm text-[#8A93A6]">
            Kami telah mengirimkan 6 digit kode rahasia (OTP) ke email <strong>{{ auth()->user()->pending_email ?: auth()->user()->email }}</strong>.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('verification.verify.otp') }}">
        @csrf

        <div>
            <x-input-label for="otp" value="Kode OTP (6 Digit)" />
            <x-text-input id="otp" class="block mt-1 w-full text-center text-xl tracking-[0.5em] font-mono" type="text" name="otp" required autofocus autocomplete="off" maxlength="6" pattern="[0-9]{6}" />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <button type="submit" class="w-full justify-center px-4 py-3 bg-[#12A57F] text-white rounded-xl font-bold hover:bg-[#0F8E6D] transition-colors shadow-md shadow-[#12A57F]/20">
                Verifikasi OTP
            </button>
        </div>
    </form>

    <div class="mt-6 text-center border-t border-[#E7E3D8] pt-4">
        <p class="text-sm text-[#8A93A6] mb-3">Tidak menerima email?</p>
        
        <form method="POST" action="{{ route('verification.send.otp') }}">
            @csrf
            <button type="submit" class="text-sm font-semibold text-[#12A57F] hover:text-[#0F8E6D] hover:underline underline-offset-4">
                Kirim Ulang Kode OTP
            </button>
        </form>
    </div>
</x-guest-layout>
