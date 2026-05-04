<?php

namespace App\Livewire\Mannager;

use App\Models\CrmNotificationRecipient;
use App\Services\CrmNotificationService;
use Livewire\Component;

class Announcements extends Component
{
    public int $perPage = 15;

    public function loadMore(): void
    {
        $this->perPage += 15;
    }

    public function markAllRead(): void
    {
        CrmNotificationRecipient::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function markRead(int $notificationId): void
    {
        app(CrmNotificationService::class)->markAsRead($notificationId, auth()->id());
    }

    public function render()
    {
        $recipients = CrmNotificationRecipient::where('user_id', auth()->id())
            ->with('notification.sender')
            ->latest('created_at')
            ->take($this->perPage)
            ->get();

        $totalCount = CrmNotificationRecipient::where('user_id', auth()->id())->count();
        $hasMore = $totalCount > $this->perPage;

        return view('livewire.mannager.announcements', [
            'recipients' => $recipients,
            'hasMore' => $hasMore,
            'totalCount' => $totalCount,
        ])->layout('layouts.custom');
    }
}
