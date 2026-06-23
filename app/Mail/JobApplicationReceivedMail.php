<?php

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobApplicationReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public JobApplication $application;

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

    public function __construct(JobApplication $application)
    {
        $this->application = $application;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تم استلام طلب التوظيف الخاص بك - Thank you for applying',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.job-application-received',
            with: ['application' => $this->application],
        );
    }
}
