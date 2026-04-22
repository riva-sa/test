<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Validator;

// Test email validation directly
$validator = Validator::make(['email' => 'invalid-email'], ['email' => 'email']);
var_dump("Testing 'invalid-email':");
var_dump($validator->passes());
var_dump($validator->errors()->getMessages());

$validator2 = Validator::make(['email' => 'test@example.com'], ['email' => 'email']);
var_dump("Testing 'test@example.com':");
var_dump($validator2->passes());
var_dump($validator2->errors()->getMessages());

// Test with nullable
$validator3 = Validator::make(['email' => 'invalid-email'], ['email' => 'nullable|email']);
var_dump("Testing 'invalid-email' with nullable:");
var_dump($validator3->passes());
var_dump($validator3->errors()->getMessages());