<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create([
            'email' => fake()->unique()->safeEmail(),
            'password' => config('app.default_user.password'),
        ]));

        $this->withoutVite();
    }
}
