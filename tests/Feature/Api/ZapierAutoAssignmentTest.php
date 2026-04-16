<?php

namespace Tests\Feature\Api;

use App\Models\UnitOrder;
use App\Models\User;
use App\Notifications\UnitOrderUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ZapierAutoAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create the sales role
        Role::create(['name' => 'sales', 'guard_name' => 'web']);
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
    }

    /** @test */
    public function it_automatically_assigns_social_media_lead_to_sales_representative()
    {
        Notification::fake();

        // Create an active sales user
        $salesUser = User::factory()->create([
            'is_active' => true,
            'on_vacation' => false,
        ]);
        $salesUser->assignRole('sales');

        $payload = [
            'name' => 'Lead to Assign',
            'email' => 'assign@test.com',
            'phone' => '+966500000000',
            'marketing_source' => 'TikTok',
            'external_id' => 'ext_123',
        ];

        // Trigger ingestion
        $response = $this->postJson('/api/zapier/social-media-lead', $payload);

        $response->assertStatus(201);

        $order = UnitOrder::where('external_id', 'ext_123')->first();
        
        // Verify assignment
        $this->assertEquals($salesUser->id, $order->assigned_sales_user_id);

        // Verify notification was sent to sales user
        Notification::assertSentTo(
            $salesUser,
            UnitOrderUpdated::class,
            function ($notification, $channels) use ($order) {
                return $notification->type === 'order_assigned' && 
                       $notification->order->id === $order->id;
            }
        );
    }
}
