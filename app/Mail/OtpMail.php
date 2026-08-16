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
    public $type; // 'register' or 'change_email'
    public $name;

    /**
     * Create a new message instance.
     */
    public function __construct($otpCode, $type, $name)
    {
        $this->otpCode = $otpCode;
        $this->type = $type;
        $this->name = $name;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->type === 'register' ? 'Verifikasi Pendaftaran Akun' : 'Verifikasi Perubahan Email';
        
        return new Envelope(
            subject: $subject . ' - ' . config('app.name', 'BelanjaIn'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
