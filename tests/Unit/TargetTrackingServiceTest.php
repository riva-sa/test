<?php

namespace Tests\Unit;

use App\Models\LeaderboardConfig;
use App\Models\OrderStatusTransition;
use App\Models\SalesTarget;
use App\Models\User;
use App\Services\TargetTrackingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TargetTrackingServiceTest extends TestCase
{
    use RefreshDatabase;

    private TargetTrackingService $service;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        \Spatie\Permission\Models\Role::create(['name' => 'sales']);
        $this->service = app(TargetTrackingService::class);
        $this->user = User::factory()->create();
        $this->user->assignRole('sales');

        LeaderboardConfig::updateOrCreate(['target_type' => 'monthly_orders'], ['weight' => 25]);
        LeaderboardConfig::updateOrCreate(['target_type' => 'daily_orders'], ['weight' => 25]);
        LeaderboardConfig::updateOrCreate(['target_type' => 'reservations'], ['weight' => 25]);
        LeaderboardConfig::updateOrCreate(['target_type' => 'sales'], ['weight' => 25]);
    }

    public function test_get_progress_counts_correctly()
    {
        $now = Carbon::now();

        // Monthly / Daily orders (from 0 to anything)
        OrderStatusTransition::create([
            'unit_order_id' => 1,
            'user_id' => $this->user->id,
            'from_status' => 0,
            'to_status' => 1,
            'created_at' => $now,
        ]);

        // Reservation (to 2)
        OrderStatusTransition::create([
            'unit_order_id' => 1,
            'user_id' => $this->user->id,
            'from_status' => 1,
            'to_status' => 2,
            'created_at' => $now,
        ]);

        // Sale (to 4)
        OrderStatusTransition::create([
            'unit_order_id' => 1,
            'user_id' => $this->user->id,
            'from_status' => 2,
            'to_status' => 4,
            'created_at' => $now,
        ]);

        $this->assertEquals(1, $this->service->getProgress($this->user->id, 'monthly_orders', $now->copy()->startOfMonth(), $now->copy()->endOfMonth()));
        $this->assertEquals(1, $this->service->getProgress($this->user->id, 'reservations', $now->copy()->startOfMonth(), $now->copy()->endOfMonth()));
        $this->assertEquals(1, $this->service->getProgress($this->user->id, 'sales', $now->copy()->startOfMonth(), $now->copy()->endOfMonth()));
    }

    public function test_get_leaderboard_calculates_composite_score()
    {
        $now = Carbon::now();

        SalesTarget::create(['user_id' => $this->user->id, 'type' => 'monthly_orders', 'target_value' => 10]);
        SalesTarget::create(['user_id' => $this->user->id, 'type' => 'sales', 'target_value' => 5]);

        // 5 monthly orders = 50% of 10. Weight is 25. So 12.5 points.
        for ($i = 0; $i < 5; $i++) {
            OrderStatusTransition::create([
                'unit_order_id' => $i + 1,
                'user_id' => $this->user->id,
                'from_status' => 0,
                'to_status' => 1,
                'created_at' => $now,
            ]);
        }

        // 5 sales = 100% of 5. Weight is 25. So 25 points.
        for ($i = 0; $i < 5; $i++) {
            OrderStatusTransition::create([
                'unit_order_id' => $i + 1,
                'user_id' => $this->user->id,
                'from_status' => 2,
                'to_status' => 4,
                'created_at' => $now,
            ]);
        }

        $leaderboard = $this->service->getLeaderboard($now);
        $entry = $leaderboard->firstWhere('user.id', $this->user->id);

        // 12.5 + 25 = 37.5
        $this->assertEquals(37.5, $entry['composite_score']);
    }
}
