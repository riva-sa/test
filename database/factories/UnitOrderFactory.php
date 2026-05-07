<?php

namespace Database\Factories;

use App\Models\UnitOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UnitOrder>
 */
class UnitOrderFactory extends Factory
{
    protected $model = UnitOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'status' => 0,
            'message' => $this->faker->sentence(),
        ];
    }
}
