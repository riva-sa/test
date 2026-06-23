<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UnitOrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Get the middleware the mailable should be sent through.
     */
    public function middleware(): array
    {
        return [new \Illuminate\Queue\Middleware\RateLimited('emails')];
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): \DateTime
    {
        return now()->addHours(24);
    }

    public $emailData;

    public $recipientType;

    /**
     * Create a new message instance.
     */
    public function __construct($emailData, $recipientType = 'general')
    {
        $this->emailData = $emailData;
        $this->recipientType = $recipientType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = 'طلب وحدة جديد - '.$this->emailData['project']->name;

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
            view: 'emails.unit-order-notification',
            with: [
                'emailData' => $this->emailData,
                'recipientType' => $this->recipientType,
            ]
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
