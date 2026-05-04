<?php

namespace Database\Seeders;

use App\Models\LeaderboardConfig;
use Illuminate\Database\Seeder;

class LeaderboardConfigSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'monthly_orders' => 25.00,
            'daily_orders' => 25.00,
            'reservations' => 25.00,
            'sales' => 25.00,
        ];

        foreach ($defaults as $type => $weight) {
            LeaderboardConfig::updateOrCreate(
                ['target_type' => $type],
                ['weight' => $weight, 'updated_at' => now()]
            );
        }

        $this->command->info('LeaderboardConfig seeded with equal weights (25% each).');
    }
}
