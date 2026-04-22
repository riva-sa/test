<?php

namespace Tests\Unit\Actions;

use Tests\TestCase;
use App\Actions\NormalizePhoneAction;

class NormalizePhoneActionTest extends TestCase
{
    /** @test */
    public function it_formats_phone_numbers_correctly()
    {
        $action = new NormalizePhoneAction();

        // Test various phone number formats
        $this->assertEquals('+966501234567', $action->execute('050 123 4567'));
        $this->assertEquals('+966501234567', $action->execute('+96650-1234567'));
        $this->assertEquals('+966501234567', $action->execute('966501234567'));
        $this->assertEquals('+966551234567', $action->execute('055-123-4567'));
        $this->assertEquals('+966551234567', $action->execute('055 123 4567'));
    }

    /** @test */
    public function it_returns_unformatable_numbers_as_is()
    {
        $action = new NormalizePhoneAction();

        // Test numbers that cannot be formatted as valid Saudi mobile numbers
        // These should be returned as-is (to allow lead creation and flag for manual review)
        $result = $action->execute('0123456789'); // Doesn't start with 05
        $this->assertEquals('0123456789', $result); // Returned as-is

        $result = $action->execute('1234567890'); // Too short
        $this->assertEquals('1234567890', $result); // Returned as-is

        // Test empty string
        $result = $action->execute('');
        $this->assertEquals('', $result); // Returned as-is

        // Test non-numeric string
        $result = $action->execute('abc');
        $this->assertEquals('abc', $result); // Returned as-is
    }
}