<?php

namespace Database\Seeders;

use App\Models\LeaderboardSnapshot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LeaderboardTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $salesAgents = User::role('sales')->take(10)->get();

        if ($salesAgents->isEmpty()) {
            return;
        }

        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::today()->subDays($i);

            foreach ($salesAgents as $agent) {
                LeaderboardSnapshot::updateOrCreate(
                    [
                        'user_id' => $agent->id,
                        'snapshot_date' => $date->toDateString(),
                    ],
                    [
                        'monthly_orders' => rand(1, 10),
                        'daily_orders' => rand(0, 3),
                        'reservations' => rand(0, 5),
                        'sales' => rand(0, 2),
                        'composite_score' => rand(10, 100) / 1.5,
                    ]
                );
            }
        }
    }
}
