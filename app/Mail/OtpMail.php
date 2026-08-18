<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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
        $this->name = $name;
    }

    public function envelope(): Envelope
    {
        $subject = $this->type === 'register' ? 'Verifikasi Pendaftaran Akun' : 'Verifikasi Perubahan Email';
        
        return new Envelope(
            subject: $subject . ' - ' . config('app.name', 'BelanjaIn'),
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
