<?php

namespace Tests\Feature\Api;

use App\Models\UnitOrder;
use App\Models\User;
use App\Notifications\NewSocialMediaLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ZapierLeadControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_ingest_a_lead_from_zapier()
    {
        Notification::fake();

        // Create a user with admin role to receive notification
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $payload = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+966500000000',
            'marketing_source' => 'TikTok',
            'campaign_name' => 'Summer Launch',
            'ad_squad' => 'Video Ads',
            'ad_set' => 'Riyadh',
            'ad_name' => 'Villa Tour',
        ];

        $response = $this->postJson('/api/zapier/social-media-lead', $payload);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Lead created successfully.']);

        $this->assertDatabaseHas('unit_orders', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'order_source' => 'social_media',
            'marketing_source' => 'TikTok',
            'campaign_name' => 'Summer Launch',
        ]);

        Notification::assertSentTo($admin, NewSocialMediaLead::class);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->postJson('/api/zapier/social-media-lead', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'phone', 'marketing_source']);
    }
}
