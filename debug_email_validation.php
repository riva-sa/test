<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Api\SocialMediaLeadRequest;

$request = new SocialMediaLeadRequest();
$input = [
    'name' => 'John Doe',
    'email' => 'invalid-email',
    'phone' => '1234567890',
    'marketing_source' => 'facebook',
];

var_dump($input);

$validator = Validator::make($input, $request->rules());

var_dump($validator->passes());
var_dump($validator->errors()->getMessages());