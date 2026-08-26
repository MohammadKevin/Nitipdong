<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otpCode;
    public $type;
    public $name;

    public function __construct($otpCode, $type, $name)
    {
        $this->otpCode = $otpCode;
        $this->type = $type;
        $this->name = $name ?: 'Pengguna NitipDong';
    }

    public function envelope(): Envelope
    {
        $action = match ($this->type) {
            'change_email' => 'Perubahan Email',
            'resend'       => 'Kirim Ulang OTP',
            default        => 'Verifikasi Akun',
        };

        return new Envelope(
            subject: "[{$this->otpCode}] Kode {$action} - NitipDong",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
