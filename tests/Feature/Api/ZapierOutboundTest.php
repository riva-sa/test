<?php

namespace Tests\Feature\Api;

use App\Models\UnitOrder;
use App\Models\User;
use App\Observers\UnitOrderObserver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ZapierOutboundTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Enable forwarding and set a fake webhook URL
        Config::set('order_forwarding.enabled', true);
        Config::set('order_forwarding.webhook_url', 'https://hooks.zapier.com/fake-hook');
        Config::set('order_forwarding.sources', [
            UnitOrder::ORDER_SOURCE_MANAGER,
            UnitOrder::ORDER_SOURCE_FRONTEND_UNIT
        ]);
        Config::set('order_forwarding.exclude_sources', [
            UnitOrder::ORDER_SOURCE_SOCIAL_MEDIA
        ]);
    }

    /** @test */
    public function it_sends_webhook_notification_to_zapier_when_unit_order_is_created_manually()
    {
        Http::fake();

        $order = UnitOrder::create([
            'name' => 'Outbound Test',
            'email' => 'outbound@test.com',
            'phone' => '123456789',
            'order_source' => UnitOrder::ORDER_SOURCE_MANAGER,
            'marketing_source' => 'Manual',
            'campaign_name' => 'Test Campaign'
        ]);

        Http::assertSent(function ($request) use ($order) {
            return $request->url() === 'https://hooks.zapier.com/fake-hook' &&
                   $request['unit_order_id'] === $order->id &&
                   $request['customer']['name'] === 'Outbound Test' &&
                   $request['customer']['email'] === 'outbound@test.com' &&
                   $request['customer']['phone'] === '123456789' &&
                   $request['marketing']['campaign'] === 'Test Campaign' &&
                   $request['order_source'] === UnitOrder::ORDER_SOURCE_MANAGER;
        });
    }

    /** @test */
    public function it_does_not_send_webhook_for_excluded_social_media_source_to_prevent_loops()
    {
        Http::fake();

        UnitOrder::create([
            'name' => 'Social Lead',
            'email' => 'social@test.com',
            'phone' => '987654321',
            'order_source' => UnitOrder::ORDER_SOURCE_SOCIAL_MEDIA,
        ]);

        Http::assertNothingSent();
    }
}
