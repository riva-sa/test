<?php

namespace App\Livewire\Mannager;

use App\Models\CrmNotification;
use App\Models\User;
use App\Services\CrmNotificationService;
use Livewire\Component;

class Notifications extends Component
{
    public string $type = 'individual';

    public string $title = '';

    public string $content = '';

    public array $selectedUserIds = [];

    protected function rules(): array
    {
        return [
            'type' => 'required|in:individual,group,announcement,task',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'selectedUserIds' => 'required_if:type,individual|array',
        ];
    }

    public function getUsers()
    {
        return User::role(['sales', 'sales_manager', 'Admin', 'follow_up', 'developer'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function getSentNotificationsProperty()
    {
        return CrmNotification::where('sender_id', auth()->id())
            ->with(['recipients.user'])
            ->latest()
            ->take(20)
            ->get();
    }

    public function send()
    {
        abort_unless(auth()->user()->hasAnyRole(['sales_manager', 'Admin']), 403);

        $this->validate();

        $user = auth()->user();
        $service = app(CrmNotificationService::class);

        match ($this->type) {
            'group' => $service->sendToGroup($user, $this->title, $this->content),
            'announcement' => $service->sendAnnouncement($user, $this->title, $this->content),
            default => $service->send($this->type, $user, $this->title, $this->content, $this->selectedUserIds),
        };

        $this->reset(['title', 'content', 'selectedUserIds']);
        $this->type = 'individual';

        $this->dispatch('clear-trix');
        session()->flash('success', 'تم إرسال الإشعار بنجاح');
    }

    public function render()
    {
        return view('livewire.mannager.notifications', [
            'users' => $this->getUsers(),
            'sentNotifications' => $this->sentNotifications,
        ])->layout('layouts.custom');
    }
}
