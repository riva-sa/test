<?php

use App\Models\UnitOrder;
use App\Models\User;
use App\Notifications\UnitOrderUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'sales', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'sales_manager', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
});

it('sends in-app and email notification to assigned agent on new order', function () {
    Notification::fake();

    $agent = User::factory()->create();
    $agent->assignRole('sales');

    $order = UnitOrder::create([
        'name' => 'Test Customer',
        'phone' => '0501234567',
        'order_source' => 'manager',
        'assigned_sales_user_id' => $agent->id,
        'status' => 0,
    ]);

    app(\App\Services\NotificationService::class)->notifyNewOrder($order);

    Notification::assertSentTo($agent, UnitOrderUpdated::class, function ($notification) {
        return $notification->type === 'new_order';
    });
});

it('sends email notification to all sales managers on new order', function () {
    Notification::fake();

    $agent = User::factory()->create();
    $agent->assignRole('sales');

    $manager = User::factory()->create();
    $manager->assignRole('sales_manager');

    $order = UnitOrder::create([
        'name' => 'Test Customer',
        'phone' => '0501234567',
        'order_source' => 'manager',
        'assigned_sales_user_id' => $agent->id,
        'status' => 0,
    ]);

    app(\App\Services\NotificationService::class)->notifyNewOrder($order);

    Notification::assertSentTo($manager, UnitOrderUpdated::class, function ($notification) {
        return $notification->type === 'new_order_admin';
    });
});

it('new_order_admin notification uses mail channel only', function () {
    $order = UnitOrder::make([
        'name' => 'Test',
        'phone' => '050',
        'status' => 0,
    ]);

    $notification = new UnitOrderUpdated($order, 'new_order_admin', []);

    expect($notification->via(new User))->toBe(['mail']);
});

it('new_order notification uses database and mail channels', function () {
    $order = UnitOrder::make([
        'name' => 'Test',
        'phone' => '050',
        'status' => 0,
    ]);

    $notification = new UnitOrderUpdated($order, 'new_order', []);
    $channels = $notification->via(new User);

    expect($channels)->toContain('database')
        ->and($channels)->toContain('mail');
});
