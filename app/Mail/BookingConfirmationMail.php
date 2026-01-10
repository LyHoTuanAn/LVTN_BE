<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;
    public $locale;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, ?string $locale = null)
    {
        $this->booking = $booking;
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

        $subject = __('emails.Booking Confirmation - :code', ['code' => $this->booking->code]);

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
            view: 'emails.booking-confirmation',
            with: [
                'booking' => $this->booking,
                'locale' => $this->locale,
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
