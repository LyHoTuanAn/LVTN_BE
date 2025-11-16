<?php

namespace App\Mail;

use App\Models\Otp;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public Otp $otp;
    public $locale;

    /**
     * Create a new message instance.
     */
    public function __construct(Otp $otp, ?string $locale = null)
    {
        $this->otp = $otp;
        $this->locale = $locale ?? App::getLocale();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Set locale for email translation
        $originalLocale = App::getLocale();
        App::setLocale($this->locale);

        $subject = __('Your OTP Code');

        // Restore original locale
        App::setLocale($originalLocale);

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
            with: [
                'otpCode' => $this->otp->otp_code,
                'type' => $this->otp->type,
                'expiresIn' => 5, // minutes
                'locale' => $this->locale, // Pass locale to view
            ],
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
