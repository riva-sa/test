<?php

namespace App\Mail;

use App\Models\Broker;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BrokerContractMail extends Mailable
{
    use Queueable, SerializesModels;

    public Broker $broker;

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

    public function __construct(Broker $broker)
    {
        $this->broker = $broker;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'عقد الوساطة جاهز للتوقيع - بوابة الوسطاء');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.broker-contract',
            with: ['broker' => $this->broker],
        );
    }
}
