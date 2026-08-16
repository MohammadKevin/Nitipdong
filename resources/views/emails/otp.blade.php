<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f6f4ef; color: #3e4658; padding: 40px 20px; margin: 0; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header { background-color: #152238; padding: 24px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; letter-spacing: 0.5px; }
        .content { padding: 40px 30px; text-align: center; }
        .content h2 { color: #14213d; margin-top: 0; margin-bottom: 24px; font-size: 20px; }
        .content p { color: #8a93a6; font-size: 15px; line-height: 1.6; margin-bottom: 24px; }
        .otp-box { display: inline-block; background-color: #e9f8f2; border: 2px dashed #12a57f; border-radius: 12px; padding: 16px 32px; margin-bottom: 24px; }
        .otp-code { font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #12a57f; margin: 0; }
        .footer { background-color: #faf9f5; padding: 24px; text-align: center; border-top: 1px solid #f0eee6; }
        .footer p { margin: 0; color: #b3aca0; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>BelanjaIn</h1>
        </div>
        <div class="content">
            <h2>Halo, {{ $name }}!</h2>
            
            @if($type === 'register')
                <p>Terima kasih telah mendaftar di <strong>BelanjaIn</strong>. Untuk mengamankan akun Anda, silakan gunakan kode OTP berikut untuk menyelesaikan proses pendaftaran.</p>
            @else
                <p>Anda menerima email ini karena ada permintaan untuk mengubah alamat email pada akun <strong>BelanjaIn</strong> Anda. Silakan masukkan kode OTP berikut untuk melanjutkan.</p>
            @endif

            <div class="otp-box">
                <p class="otp-code">{{ $otpCode }}</p>
            </div>

            <p style="font-size: 13px; color: #b3aca0; margin-bottom: 0;">Kode OTP ini hanya berlaku selama 15 menit. Mohon jangan berikan kode ini kepada siapa pun.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} BelanjaIn. Hak Cipta Dilindungi.</p>
        </div>
    </div>
</body>
</html>
