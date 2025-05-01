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
            $existingRole = $this->db->table('roles')
                ->where('name', $role)
                ->get()
                ->getRow();

            if (!$existingRole) {
                $this->db->table('roles')->insert([
                    'name' => $role,
                ]);
                CLI::write("Rol '{$role}' creado", 'green');
            } else {
                $this->db->table('roles')
                    ->where('name', $role)
                    ->update([
                        'name' => $role,
                    ]);
                CLI::write("Rol '{$role}' actualizado", 'yellow');
            }
        }


        CLI::write('--------------------------------------------------------------------------------', 'green');
        CLI::write('Roles created', 'green');
        CLI::write('--------------------------------------------------------------------------------', 'green');
    }
}
