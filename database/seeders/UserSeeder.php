<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role as ModelsRole;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // create a roles
        $roles = [
            [
                'name' => 'Admin',
                'guard_name' => 'web',
            ],
            [
                'name' => 'User',
                'guard_name' => 'web',
            ],
            [
                'name' => 'sales_manager',
                'guard_name' => 'web',
            ],
            [
                'name' => 'project_manager',
                'guard_name' => 'web',
            ]

        ];

        foreach ($roles as $role) {
            ModelsRole::create($role);
        }

        User::factory()
            ->create([
                'email' => 'admin@riva.sa',
                'password' => Hash::make('password'),
                'name' => config('app.default_user.name'),
            ]);
    }
}
