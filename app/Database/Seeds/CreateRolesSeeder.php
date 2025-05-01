<?php

namespace App\Database\Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;

class CreateRolesSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            SUPERUSER,
        ];

        foreach ($roles as $role) {
            $this->db->table('roles')->insert([
                'name' => $role,
            ]);
        }

        CLI::write('--------------------------------------------------------------------------------', 'green');
        CLI::write('Roles created', 'green');
        CLI::write('--------------------------------------------------------------------------------', 'green');
    }
}
