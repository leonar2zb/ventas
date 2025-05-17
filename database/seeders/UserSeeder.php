<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manager = Role::where('name', 'Manager')->first();
        if (!$manager) {
            throw new \Exception("Role Manager not found. RoleSeeder must be executed firstly. Please read the readme.md file and follow the instruction.");
        }

        User::firstOrCreate([
            'name' => 'Manager',
            'email' => 'manager@mycompany.com',
            'password' => bcrypt('password'),
            'role_id' => $manager->id,
        ]);
    }
}
