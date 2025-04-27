<?php

namespace App\Database\Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;

class CreateAdminRoleSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('roles')->insert([
            'name' => SUPERUSER,
        ]);

        CLI::write('--------------------------------------------------------------------------------', 'green');
        CLI::write('Role created', 'green');
        CLI::write('--------------------------------------------------------------------------------', 'green');
    }
}
