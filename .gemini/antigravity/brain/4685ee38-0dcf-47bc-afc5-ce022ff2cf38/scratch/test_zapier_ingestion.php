<?php

use App\Actions\IngestSocialMediaLead;
use App\Models\UnitOrder;
use App\Models\User;

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$data = [
    'name' => 'Test Lead from Zapier',
    'phone' => '0500000000',
    'email' => 'zapier@test.com',
    'marketing_source' => 'Facebook',
    'campaign_name' => 'Spring Campaign 2024',
    'ad_squad' => 'Squad A',
    'ad_set' => 'Set 1',
    'ad_name' => 'Ad 1',
    'What is your favorite color?' => 'Blue',
    'How many rooms?' => '3'
];

$ingestAction = app(IngestSocialMediaLead::class);
$order = $ingestAction->execute($data);

echo "Order created with ID: " . $order->id . "\n";
echo "Source: " . $order->order_source . "\n";
echo "Message: \n" . $order->message . "\n";
