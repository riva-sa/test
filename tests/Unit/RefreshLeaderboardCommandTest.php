<?php

use App\Models\LeaderboardConfig;
use App\Models\LeaderboardSnapshot;
use App\Models\UnitOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'sales', 'guard_name' => 'web']);

    LeaderboardConfig::updateOrCreate(['target_type' => 'monthly_orders'], ['weight' => 25.00]);
    LeaderboardConfig::updateOrCreate(['target_type' => 'daily_orders'], ['weight' => 25.00]);
    LeaderboardConfig::updateOrCreate(['target_type' => 'reservations'], ['weight' => 25.00]);
    LeaderboardConfig::updateOrCreate(['target_type' => 'sales'], ['weight' => 25.00]);
});

it('creates a snapshot row for each active sales rep', function () {
    $rep = User::factory()->create(['is_active' => true]);
    $rep->assignRole('sales');

    $this->artisan('leaderboard:refresh')->assertExitCode(0);

    expect(LeaderboardSnapshot::where('user_id', $rep->id)->count())->toBe(1);
});

it('counts reservations from current order status not transitions', function () {
    $rep = User::factory()->create(['is_active' => true]);
    $rep->assignRole('sales');

    // Two orders currently in status 2 (Sales Transaction)
    UnitOrder::create(['name' => 'Test A', 'phone' => '0501', 'order_source' => 'manager', 'status' => 2, 'assigned_sales_user_id' => $rep->id]);
    UnitOrder::create(['name' => 'Test B', 'phone' => '0502', 'order_source' => 'manager', 'status' => 2, 'assigned_sales_user_id' => $rep->id]);

    // One order that was in status 2 but is now status 1 — should NOT count
    UnitOrder::create(['name' => 'Test C', 'phone' => '0503', 'order_source' => 'manager', 'status' => 1, 'assigned_sales_user_id' => $rep->id]);

    $this->artisan('leaderboard:refresh')->assertExitCode(0);

    $snapshot = LeaderboardSnapshot::where('user_id', $rep->id)->first();
    expect($snapshot->reservations)->toBe(2);
});

it('accepts a --date option and stores snapshot for that date', function () {
    $rep = User::factory()->create(['is_active' => true]);
    $rep->assignRole('sales');

    $date = now()->subDays(3)->toDateString();

    $this->artisan("leaderboard:refresh --date={$date}")->assertExitCode(0);

    expect(
        LeaderboardSnapshot::where('user_id', $rep->id)->where('snapshot_date', $date)->exists()
    )->toBeTrue();
});

it('uses updateOrCreate so running twice does not duplicate rows', function () {
    $rep = User::factory()->create(['is_active' => true]);
    $rep->assignRole('sales');

    $this->artisan('leaderboard:refresh')->assertExitCode(0);
    $this->artisan('leaderboard:refresh')->assertExitCode(0);

    expect(LeaderboardSnapshot::where('user_id', $rep->id)->count())->toBe(1);
});

it('skips inactive reps', function () {
    $inactive = User::factory()->create(['is_active' => false]);
    $inactive->assignRole('sales');

    $this->artisan('leaderboard:refresh')->assertExitCode(0);

    expect(LeaderboardSnapshot::where('user_id', $inactive->id)->exists())->toBeFalse();
});
