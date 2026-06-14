<?php

namespace App\Mail;

use App\Models\Broker;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BrokerApplicationStatusMail extends Mailable
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

    public function __construct(Broker $broker)
    {
        $this->broker = $broker;
    }

    public function envelope(): Envelope
    {
        $subject = $this->broker->isApproved()
            ? 'تم اعتماد حسابك في بوابة الوسطاء'
            : 'تحديث بخصوص طلب التسجيل في بوابة الوسطاء';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.broker-application-status',
            with: ['broker' => $this->broker],
        );
    }
}
