Halo, {{ $name }}!

@if($type === 'register')
Terima kasih telah mendaftar di NitipDong.
Gunakan kode OTP berikut untuk menyelesaikan proses verifikasi pendaftaran akun Anda:
@else
Anda menerima pesan ini karena ada permintaan perubahan alamat email akun NitipDong.
Gunakan kode OTP berikut untuk melanjutkan verifikasi:
@endif

==============================
KODE OTP ANDA: {{ $otpCode }}
==============================

Kode OTP ini bersifat rahasia dan hanya berlaku selama 15 menit.
Mohon jangan berikan kode ini kepada siapa pun demi keamanan akun Anda.

Salam hangat,
Tim NitipDong
Hak Cipta © {{ date('Y') }} NitipDong. Dilindungi Undang-Undang.
