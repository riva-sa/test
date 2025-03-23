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
        // create roles
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

        // Create admin user directly without factory
        $user = User::create([
            'email' => 'admin@riva.sa',
            'password' => Hash::make('password'),
            'name' => "Admin",
            // Add any other required fields for your User model
        ]);

        // Assign the Admin role to the user
        $adminRole = ModelsRole::where('name', 'Admin')->first();
        $user->assignRole($adminRole);
    }
}