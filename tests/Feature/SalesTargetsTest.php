<?php

namespace Tests\Feature;

use App\Models\LeaderboardConfig;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SalesTargetsTest extends TestCase
{
    use RefreshDatabase;

    private User $manager;

    private User $salesRep;

    protected function setUp(): void
    {
        parent::setUp();

        \Spatie\Permission\Models\Role::create(['name' => 'sales_manager']);
        \Spatie\Permission\Models\Role::create(['name' => 'sales']);

        $this->manager = User::factory()->create();
        $this->manager->assignRole('sales_manager');

        $this->salesRep = User::factory()->create(['name' => 'Test Rep', 'is_active' => true]);
        $this->salesRep->assignRole('sales');

        LeaderboardConfig::updateOrCreate(['target_type' => 'monthly_orders'], ['weight' => 25]);
    }

    public function test_manager_can_access_targets_page()
    {
        $this->actingAs($this->manager)
            ->get(route('manager.targets'))
            ->assertStatus(200)
            ->assertSee('أهداف المبيعات')
            ->assertSee('Test Rep');
    }

    public function test_manager_can_set_targets()
    {
        Livewire::actingAs($this->manager)
            ->test(\App\Livewire\Mannager\SalesTargets::class)
            ->set("targets.{$this->salesRep->id}.monthly_orders", 50)
            ->call('saveTargets');

        $this->assertDatabaseHas('sales_targets', [
            'user_id' => $this->salesRep->id,
            'type' => 'monthly_orders',
            'target_value' => 50,
        ]);
    }

    public function test_leaderboard_page_loads()
    {
        $this->actingAs($this->manager)
            ->get(route('manager.leaderboard'))
            ->assertStatus(200)
            ->assertSee('لوحة المتصدرين');
    }
}
