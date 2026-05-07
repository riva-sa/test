<?php

use App\Models\LeaderboardSnapshot;
use App\Models\OrderStatusTransition;
use App\Models\UnitOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'sales', 'guard_name' => 'web']);
});

it('calculates and stores daily leaderboard snapshots', function () {
    $agent = User::factory()->create(['is_active' => true]);
    $agent->assignRole('sales');

    $order1 = UnitOrder::factory()->create(['assigned_sales_user_id' => $agent->id]);
    $order2 = UnitOrder::factory()->create(['assigned_sales_user_id' => $agent->id]);

    // Create some transitions
    OrderStatusTransition::create([
        'user_id' => $agent->id,
        'unit_order_id' => $order1->id,
        'from_status' => 0,
        'to_status' => 1,
        'created_at' => now(),
    ]);

    OrderStatusTransition::create([
        'user_id' => $agent->id,
        'unit_order_id' => $order2->id,
        'from_status' => 1,
        'to_status' => 2, // Sales Transaction
        'created_at' => now(),
    ]);

    $this->artisan('leaderboard:refresh')
        ->assertExitCode(0);

    expect(LeaderboardSnapshot::count())->toBe(1);

    $snapshot = LeaderboardSnapshot::first();
    expect($snapshot->user_id)->toBe($agent->id)
        ->and($snapshot->snapshot_date->toDateString())->toBe(today()->toDateString())
        ->and($snapshot->daily_orders)->toBe(1) // from_status 0
        ->and($snapshot->reservations)->toBe(1); // to_status 2
});
