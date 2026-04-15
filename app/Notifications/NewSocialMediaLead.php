<?php

namespace App\Notifications;

use App\Models\UnitOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSocialMediaLead extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(UnitOrder $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('طلب مبيعات جديد - ' . $this->order->marketing_source)
            ->view('emails.unit-order-notification', [
                'emailData' => [
                    'order' => $this->order,
                    'project' => $this->order->project,
                ],
                'recipientType' => 'admin',
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'name' => $this->order->name,
            'source' => $this->order->marketing_source,
            'campaign' => $this->order->campaign_name,
        ];
    }
}
