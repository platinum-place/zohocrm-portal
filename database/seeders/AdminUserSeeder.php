<?php

namespace Database\Seeders;

use App\Enums\User\RolesEnum;
use App\Models\User\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'username' => 'admin',
        ]);

        $user->assignRole(RolesEnum::ADMIN);
    }
}
