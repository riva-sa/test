<?php

use App\Models\LeaderboardAdjustment;
use App\Models\LeaderboardSnapshot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'sales', 'guard_name' => 'web']);
});

it('admin can save a valid point adjustment', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    $rep = User::factory()->create(['is_active' => true]);
    $rep->assignRole('sales');

    $snapshot = LeaderboardSnapshot::create([
        'user_id' => $rep->id,
        'snapshot_date' => today()->toDateString(),
        'monthly_orders' => 5,
        'daily_orders' => 2,
        'reservations' => 3,
        'sales' => 1,
        'composite_score' => 42.50,
    ]);

    Livewire::actingAs($admin)
        ->test(\App\Livewire\Mannager\Leaderboard::class)
        ->call('openAdjustmentModal', $rep->id, $rep->name, 42.50, today()->toDateString())
        ->set('editingAdjustedValue', 38.00)
        ->set('editingReason', 'Order was reverted to open status')
        ->call('saveAdjustment');

    expect(LeaderboardAdjustment::where('user_id', $rep->id)->count())->toBe(1);

    $adj = LeaderboardAdjustment::where('user_id', $rep->id)->first();
    expect($adj->adjusted_value)->toBe(38.0)
        ->and($adj->original_value)->toBe(42.5)
        ->and($adj->reason)->toBe('Order was reverted to open status')
        ->and($adj->adjusted_by)->toBe($admin->id);
});

it('rejects adjustment with negative value', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    $rep = User::factory()->create(['is_active' => true]);
    $rep->assignRole('sales');

    Livewire::actingAs($admin)
        ->test(\App\Livewire\Mannager\Leaderboard::class)
        ->call('openAdjustmentModal', $rep->id, $rep->name, 50.0, today()->toDateString())
        ->set('editingAdjustedValue', -5)
        ->set('editingReason', 'Some reason')
        ->call('saveAdjustment');

    expect(LeaderboardAdjustment::count())->toBe(0);
});

it('rejects adjustment without a reason', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    $rep = User::factory()->create(['is_active' => true]);
    $rep->assignRole('sales');

    Livewire::actingAs($admin)
        ->test(\App\Livewire\Mannager\Leaderboard::class)
        ->call('openAdjustmentModal', $rep->id, $rep->name, 50.0, today()->toDateString())
        ->set('editingAdjustedValue', 30)
        ->set('editingReason', '')
        ->call('saveAdjustment');

    expect(LeaderboardAdjustment::count())->toBe(0);
});
