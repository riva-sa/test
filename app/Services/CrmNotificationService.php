<?php

namespace App\Services;

use App\Events\NewCrmNotification;
use App\Models\CrmNotification;
use App\Models\CrmNotificationRecipient;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class CrmNotificationService
{
    private function sanitize(string $content): string
    {
        $config = (new HtmlSanitizerConfig())
            ->allowElement('p')
            ->allowElement('br')
            ->allowElement('strong')
            ->allowElement('em')
            ->allowElement('ul')
            ->allowElement('ol')
            ->allowElement('li')
            ->allowElement('h2')
            ->allowElement('h3')
            ->allowElement('a', ['href', 'title', 'target']);

        return (new HtmlSanitizer($config))->sanitize($content);
    }

    public function send(string $type, User $sender, string $title, string $content, array $recipientIds): CrmNotification
    {
        $notification = CrmNotification::create([
            'type' => $type,
            'sender_id' => $sender->id,
            'title' => $title,
            'content' => $this->sanitize($content),
        ]);

        $now = now();
        $rows = array_map(fn ($userId) => [
            'notification_id' => $notification->id,
            'user_id' => $userId,
            'created_at' => $now,
        ], $recipientIds);

        CrmNotificationRecipient::insert($rows);

        foreach ($recipientIds as $userId) {
            $unreadCount = CrmNotificationRecipient::where('user_id', $userId)->unread()->count();
            broadcast(new NewCrmNotification($notification, $userId, $unreadCount));
        }

        Log::info('crm_notification_sent', [
            'event' => 'crm_notification_sent',
            'type' => $type,
            'sender_id' => $sender->id,
            'recipient_count' => count($recipientIds),
            'notification_id' => $notification->id,
        ]);

        return $notification;
    }

    public function sendToGroup(User $sender, string $title, string $content): CrmNotification
    {
        $recipientIds = User::role('sales')->where('is_active', true)->pluck('id')->toArray();

        return $this->send('group', $sender, $title, $content, $recipientIds);
    }

    public function sendAnnouncement(User $sender, string $title, string $content): CrmNotification
    {
        $crmRoles = ['sales', 'sales_manager', 'Admin', 'developer', 'follow_up', 'project_manager'];
        $recipientIds = User::role($crmRoles)->where('is_active', true)->pluck('id')->toArray();

        return $this->send('announcement', $sender, $title, $content, $recipientIds);
    }

    public function markAsRead(int $notificationId, int $userId): void
    {
        CrmNotificationRecipient::where('notification_id', $notificationId)
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        Log::info('crm_notification_read', [
            'event' => 'crm_notification_read',
            'notification_id' => $notificationId,
            'user_id' => $userId,
        ]);
    }
}
