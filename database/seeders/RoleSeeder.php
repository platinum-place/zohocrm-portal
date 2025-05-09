<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ADMIN_ROLE,
        ];

        foreach ($roles as $role) {
            if (!Role::where('name', $role)->exists()) {
               Role::create(['name' => $role]);
            }
        }
    }
}
