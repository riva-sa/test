<?php

namespace Tests\Feature;

use App\Models\UnitOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_manager_can_see_grandfathered_orders_but_not_new_ones()
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        \Spatie\Permission\Models\Role::create(['name' => 'project_manager']);
        $projectManager = User::factory()->create();
        $projectManager->assignRole('project_manager');

        $projectId = \Illuminate\Support\Facades\DB::table('projects')->insertGetId([
            'name' => 'Test Project',
            'slug' => 'test-project',
            'developer_id' => 1,
            'project_type_id' => 1,
            'address' => 'Test Address',
            'city_id' => 1,
            'state_id' => 1,
            'country' => 'Test Country',
            'status' => 1,
            'sales_manager_id' => $projectManager->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $oldOrderId = \Illuminate\Support\Facades\DB::table('unit_orders')->insertGetId([
            'project_id' => $projectId,
            'name' => 'Old Order',
            'phone' => '1234567890',
            'status' => 0,
            'created_at' => Carbon::parse(UnitOrder::ASSIGNMENT_CUTOFF_DATE)->subDay(),
            'updated_at' => now(),
        ]);

        $newOrderId = \Illuminate\Support\Facades\DB::table('unit_orders')->insertGetId([
            'project_id' => $projectId,
            'name' => 'New Order',
            'phone' => '0987654321',
            'status' => 0,
            'created_at' => Carbon::parse(UnitOrder::ASSIGNMENT_CUTOFF_DATE)->addDay(),
            'updated_at' => now(),
        ]);

        $visibleOrders = UnitOrder::accessibleBy($projectManager)->pluck('id')->toArray();

        $this->assertContains($oldOrderId, $visibleOrders);
        $this->assertNotContains($newOrderId, $visibleOrders);
    }
}
