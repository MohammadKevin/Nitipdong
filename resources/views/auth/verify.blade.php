<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="w-12 h-12 rounded-full bg-cyan-50 border border-cyan-200 text-cyan-700 flex items-center justify-center mx-auto mb-3 text-lg">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Verifikasi Kode OTP</h1>
        <p class="text-xs text-slate-500 mt-1">
            Masukkan 6 digit kode yang telah dikirimkan ke <strong class="text-slate-800">{{ auth()->user()->pending_email ?: auth()->user()->email }}</strong>
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('verification.verify.otp') }}" class="space-y-4">
        @csrf

        <div>
            <label for="otp" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider text-center mb-2">Kode OTP (6 Digit)</label>
            <input id="otp" class="input text-center text-xl tracking-[0.5em] font-mono h-12 rounded-md font-bold text-slate-900 border-slate-300 focus:border-cyan-500 focus:ring-cyan-500" type="text" name="otp" required autofocus autocomplete="off" maxlength="6" pattern="[0-9]{6}" placeholder="------" />
            <x-input-error :messages="$errors->get('otp')" class="mt-1.5 text-xs text-rose-500 text-center" />
        </div>

        <button type="submit" class="w-full btn-primary h-10 text-xs bg-cyan-700 hover:bg-cyan-800 font-semibold shadow-xs">
            Verifikasi Kode Sekarang
        </button>
    </form>

    <div class="mt-6 text-center border-t border-slate-100 pt-4" 
         x-data="{ 
            countdown: 30, 
            init() { 
                let timer = setInterval(() => { 
                    if (this.countdown > 0) { 
                        this.countdown--; 
                    } else { 
                        clearInterval(timer); 
                    } 
                }, 1000); 
            } 
         }">
        <p class="text-xs text-slate-500 mb-2">Tidak menerima email kode?</p>
        
        <form method="POST" action="{{ route('verification.send.otp') }}">
            @csrf
            <button type="submit" 
                    :disabled="countdown > 0"
                    class="text-xs font-bold transition-colors inline-flex items-center gap-1.5"
                    :class="countdown > 0 ? 'text-slate-400 cursor-not-allowed' : 'text-cyan-700 hover:underline cursor-pointer'">
                <i class="fa-solid fa-rotate-right" :class="{ 'animate-spin': countdown > 0 }"></i>
                <span x-show="countdown > 0" x-text="`Kirim Ulang Kode OTP (${countdown}s)`"></span>
                <span x-show="countdown === 0">Kirim Ulang Kode OTP Sekarang</span>
            </button>
        </form>
    </div>
</x-guest-layout>
