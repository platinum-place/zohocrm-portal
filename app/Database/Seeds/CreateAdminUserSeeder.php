<?php

namespace Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;
use Models\RoleModel;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * @throws \ReflectionException
     */
    public function run()
    {
        helper('string_util');
        $faker = \Faker\Factory::create();

        $username = $faker->userName;
        $password = generate_secure_password(6);

        $roleModel = new RoleModel();
        $role_id = $roleModel->where('name', SUPERUSER)->first()['id'];

        $this->db->table('oauth_users')->insert([
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role_id' => $role_id,
        ]);

        CLI::write('--------------------------------------------------------------------------------', 'green');
        CLI::write('Username: ' . $username, 'green');
        CLI::write('Password: ' . $password, 'yellow');
        CLI::write('--------------------------------------------------------------------------------', 'green');
    }
}
