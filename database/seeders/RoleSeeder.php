<?php

namespace Database\Seeders;

use App\Enums\User\RolesEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guards = config('auth.guards');

        foreach (RolesEnum::cases() as $role) {
            foreach ($guards as $key => $guard) {
                app(Role::class)->findOrCreate($role->value, $key);
            }
        }
    }
}
