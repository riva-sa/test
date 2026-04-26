<?php

require __DIR__ . '/vendor/autoload.php';

$request = new App\Http\Requests\Api\SocialMediaLeadRequest();
$input = [
    'name' => 'John Doe',
    'email' => null,
    'phone' => '1234567890',
    'marketing_source' => 'facebook',
];

var_dump($input);

$validated = $request->validate($input);

var_dump($validated);