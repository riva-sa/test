<?php

namespace App\Actions;

use App\Models\UnitOrder;
use App\Models\User;
use App\Notifications\NewSocialMediaLead;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class IngestSocialMediaLead
{
    /**
     * Execute the lead ingestion action.
     *
     * @param array $data
     * @return UnitOrder
     */
    public function execute(array $data): UnitOrder
    {
        $attributes = [
            'name' => $data['name'],
            'email' => $data['email'] ?? "[EMAIL_ADDRESS]",
            'phone' => $data['phone'],
            'message' => $data['message'] ?? null,
            'marketing_source' => $data['marketing_source'],
            'campaign_name' => $data['campaign_name'] ?? null,
            'ad_squad' => $data['ad_squad'] ?? null,
            'ad_set' => $data['ad_set'] ?? null,
            'ad_name' => $data['ad_name'] ?? null,
            'order_source' => UnitOrder::ORDER_SOURCE_SOCIAL_MEDIA,
            'status' => 0, // New
        ];

        if (!empty($data['external_id'])) {
            $order = UnitOrder::firstOrCreate(
                ['external_id' => $data['external_id']],
                $attributes
            );
        } else {
            $order = UnitOrder::create($attributes);
        }

        Log::info('Social media lead ingested', [
            'order_id' => $order->id,
            'email' => $order->email,
            'marketing_source' => $order->marketing_source,
            'campaign' => $order->campaign_name,
        ]);

        // Notify admins and sales managers
        $notifiableUsers = User::role(['admin', 'sales_manager'])->get();
        Notification::send($notifiableUsers, new NewSocialMediaLead($order));
        
        return $order;
    }
}
