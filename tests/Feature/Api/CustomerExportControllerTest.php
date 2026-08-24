<?php

namespace Tests\Feature\Api;

use App\Models\Project;
use App\Models\Unit;
use App\Models\UnitOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerExportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.agent_api_key' => 'test-api-key']);
    }

    /** @test */
    public function it_requires_api_key_for_export_endpoints()
    {
        $this->getJson('/api/customers/export')
            ->assertStatus(401);

        $this->getJson('/api/customers/0501234567')
            ->assertStatus(401);
    }

    /** @test */
    public function it_exports_all_customers_with_their_orders()
    {
        $project = Project::factory()->create(['name' => 'Al-Yasmeen Project']);
        $unit = Unit::factory()->create([
            'project_id' => $project->id,
            'unit_type' => 'Villa',
            'unit_number' => '101',
            'unit_price' => 1500000,
        ]);

        $order1 = UnitOrder::factory()->create([
            'name' => 'Ahmed Ali',
            'phone' => '0501234567',
            'email' => 'ahmed@example.com',
            'status' => 0,
            'unit_id' => $unit->id,
            'project_id' => $project->id,
        ]);

        $order2 = UnitOrder::factory()->create([
            'name' => 'Ahmed Ali',
            'phone' => '0501234567',
            'email' => 'ahmed@example.com',
            'status' => 1,
            'unit_id' => $unit->id,
            'project_id' => $project->id,
        ]);

        $response = $this->withHeader('X-AGENT-API-KEY', 'test-api-key')
            ->getJson('/api/customers/export');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath('data.0.phone', '0501234567')
            ->assertJsonPath('data.0.total_orders', 2);

        $this->assertCount(2, $response->json('data.0.orders'));
    }

    /** @test */
    public function it_returns_single_customer_details_and_orders_by_phone()
    {
        $project = Project::factory()->create(['name' => 'Riyadh Palms']);
        $unit = Unit::factory()->create([
            'project_id' => $project->id,
            'unit_type' => 'Apartment',
            'unit_number' => 'B20',
            'unit_price' => 850000,
        ]);

        UnitOrder::factory()->create([
            'name' => 'Sara Mohamed',
            'phone' => '0599998877',
            'email' => 'sara@example.com',
            'status' => 4,
            'unit_id' => $unit->id,
            'project_id' => $project->id,
        ]);

        $response = $this->withHeader('X-AGENT-API-KEY', 'test-api-key')
            ->getJson('/api/customers/0599998877');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Sara Mohamed',
                    'phone' => '0599998877',
                    'email' => 'sara@example.com',
                    'total_orders' => 1,
                    'latest_status' => 'مكتمل',
                ],
            ]);

        $this->assertCount(1, $response->json('data.orders'));
    }

    /** @test */
    public function it_returns_404_if_customer_not_found()
    {
        $response = $this->withHeader('X-AGENT-API-KEY', 'test-api-key')
            ->getJson('/api/customers/0000000000');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'العميل غير موجود أو لا توجد طلبات مرتبطة به.',
            ]);
    }
}
