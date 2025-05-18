<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::create(['name' => ADMIN_ROLE]);

        $user = User::factory()->create([
            'username' => 'admin',
        ]);

        $user->assignRole($role);
    }
}
