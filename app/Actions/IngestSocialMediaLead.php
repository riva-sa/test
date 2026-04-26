<?php

namespace App\Actions;

use App\Models\UnitOrder;
use App\Models\User;
use App\Notifications\NewSocialMediaLead;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Actions\NormalizePhoneAction;

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
            'email' => $data['email'] ?? null,
            'phone' => (new NormalizePhoneAction())->execute($data['phone']),
            'message' => $data['message'] ?? null,
            'marketing_source' => $data['marketing_source'],
            'campaign_name' => $data['campaign_name'] ?? null,
            'ad_squad' => $data['ad_squad'] ?? null,
            'ad_set' => $data['ad_set'] ?? null,
            'ad_name' => $data['ad_name'] ?? null,
            'order_source' => UnitOrder::ORDER_SOURCE_SOCIAL_MEDIA,
            'status' => 0, // New
        ];

        // US8: Capture additional form fields into basic_order_notes
        $additionalFields = $this->captureAdditionalFields($data);
        if (!empty($additionalFields)) {
            $attributes['message'] = ($attributes['message'] ?? '') . "\n" . $additionalFields;
        }

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
        $notifiableUsers = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'sales_manager']);
        })->get();

        Notification::send($notifiableUsers, new NewSocialMediaLead($order));
        
        return $order;
    }

    /**
     * US8: Capture additional form fields into basic_order_notes
     */
    protected function captureAdditionalFields(array $data): string
    {
        $notes = [];
        
        // Define standard fields to exclude from additional notes
        $standardFields = [
            'name', 'email', 'phone', 'marketing_source', 'campaign_name', 
            'ad_squad', 'ad_set', 'ad_name', 'external_id', 'message',
            'order_source', 'status', 'project_id', 'unit_id', 'user_id'
        ];

        foreach ($data as $key => $value) {
            // If the field is not a standard field and is not empty
            if (!in_array($key, $standardFields) && !empty($value)) {
                // Prettify the key (replace underscores with spaces and capitalize)
                $label = ucwords(str_replace(['_', '-'], ' ', $key));
                $notes[] = "{$label}: {$value}";
            }
        }

        return implode("\n", $notes);
    }
}
