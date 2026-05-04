<?php

use App\Models\CrmNotification;
use App\Models\CrmNotificationRecipient;
use App\Models\User;
use App\Services\CrmNotificationService;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::create(['name' => 'sales_manager', 'guard_name' => 'web']);
    Role::create(['name' => 'sales', 'guard_name' => 'web']);
    $this->user = auth()->user();
    $this->user->assignRole('sales_manager');
    $this->service = app(CrmNotificationService::class);
});

it('creates notification and recipients on send', function () {
    Event::fake();

    $recipient = User::factory()->create();

    $notification = $this->service->send(
        'individual',
        $this->user,
        'Test Title',
        '<p>Test content</p>',
        [$recipient->id]
    );

    expect($notification)->toBeInstanceOf(CrmNotification::class);
    expect($notification->type)->toBe('individual');
    expect($notification->sender_id)->toBe($this->user->id);
    expect($notification->title)->toBe('Test Title');

    $recipientRecord = CrmNotificationRecipient::where('notification_id', $notification->id)
        ->where('user_id', $recipient->id)
        ->first();

    expect($recipientRecord)->not->toBeNull();
    expect($recipientRecord->read_at)->toBeNull();
});

it('sends to group resolving sales role users', function () {
    Event::fake();

    $salesUser1 = User::factory()->create(['is_active' => true]);
    $salesUser2 = User::factory()->create(['is_active' => true]);
    $salesUser1->assignRole('sales');
    $salesUser2->assignRole('sales');

    $notification = $this->service->sendToGroup($this->user, 'Group Test', '<p>Group</p>');

    expect($notification->type)->toBe('group');
    expect(CrmNotificationRecipient::where('notification_id', $notification->id)->count())->toBe(2);
});

it('marks notification as read', function () {
    Event::fake();

    $recipient = User::factory()->create();
    $notification = $this->service->send('individual', $this->user, 'Read Test', 'content', [$recipient->id]);

    expect(CrmNotificationRecipient::where('notification_id', $notification->id)->whereNull('read_at')->count())->toBe(1);

    $this->service->markAsRead($notification->id, $recipient->id);

    $record = CrmNotificationRecipient::where('notification_id', $notification->id)->first();
    expect($record->read_at)->not->toBeNull();
});

it('sanitizes HTML content on send', function () {
    Event::fake();

    $recipient = User::factory()->create();
    $dirty = '<p>Hello</p><script>alert("xss")</script><strong>Bold</strong>';

    $notification = $this->service->send('individual', $this->user, 'Sanitize Test', $dirty, [$recipient->id]);

    expect($notification->content)->not->toContain('<script>');
    expect($notification->content)->toContain('<p>Hello</p>');
    expect($notification->content)->toContain('<strong>Bold</strong>');
});
