<?php

use Tests\TestCase;
use Illuminate\Support\Facades\Validator;

class SimpleValidatorTest extends TestCase
{
    public function test_validator_works()
    {
        $validator = Validator::make(['email' => 'invalid-email'], ['email' => 'email']);
        $this->assertFalse($validator->passes());
    }
}