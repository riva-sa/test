<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            DeveloperRoleSeeder::class,
            ProjectSeder::class,
            ContentBlockSeeder::class,
            GuaranteeSeeder::class,
            FeatureSeeder::class,
            DeveloperRoleSeeder::class,
            ProjectUnitOrderSeeder::class,
        ]);
    }
}
