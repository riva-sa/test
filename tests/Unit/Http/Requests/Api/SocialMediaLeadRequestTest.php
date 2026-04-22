<?php

namespace Tests\Unit\Http\Requests\Api;

use Tests\TestCase;
use App\Http\Requests\Api\SocialMediaLeadRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SocialMediaLeadRequestTest extends TestCase
{
    public function test_email_is_optional_and_can_be_null(): void
    {
        $request = new SocialMediaLeadRequest();
        $input = [
            'name' => 'John Doe',
            'email' => null,
            'phone' => '1234567890',
            'marketing_source' => 'facebook',
        ];

        $validator = Validator::make($input, $request->rules());
        $this->assertTrue($validator->passes());
        $this->assertNull($validator->validated()['email']);
    }

    public function test_email_is_optional_and_can_be_omitted(): void
    {
        $request = new SocialMediaLeadRequest();
        $input = [
            'name' => 'John Doe',
            'phone' => '1234567890',
            'marketing_source' => 'facebook',
        ];

        $validator = Validator::make($input, $request->rules());
        $this->assertTrue($validator->passes());
        $this->assertArrayNotHasKey('email', $validator->validated());
    }

    public function test_email_when_present_must_be_valid_email(): void
    {
        $request = new SocialMediaLeadRequest();
        $input = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'marketing_source' => 'facebook',
        ];

        $validator = Validator::make($input, $request->rules());
        $this->assertTrue($validator->passes());
        $this->assertEquals('john@example.com', $validator->validated()['email']);
    }

    public function test_email_when_present_and_invalid_fails_validation(): void
    {
        $request = new SocialMediaLeadRequest();
        $input = [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'phone' => '1234567890',
            'marketing_source' => 'facebook',
        ];

        $validator = Validator::make($input, $request->rules());
        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('email'));
    }
}