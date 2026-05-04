<?php

namespace App\Events;

use App\Models\CrmNotification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewCrmNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $recipientId;

    public int $notificationId;

    public string $type;

    public string $title;

    public string $senderName;

    public string $createdAt;

    public int $unreadCount;

    public function __construct(
        CrmNotification $notification,
        int $recipientId,
        int $unreadCount
    ) {
        $this->recipientId = $recipientId;
        $this->notificationId = $notification->id;
        $this->type = $notification->type;
        $this->title = $notification->title;
        $this->senderName = $notification->sender->name;
        $this->createdAt = $notification->created_at->toISOString();
        $this->unreadCount = $unreadCount;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notifications.'.$this->recipientId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new-notification';
    }
}
