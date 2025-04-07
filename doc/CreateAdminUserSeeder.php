<?php

namespace doc;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;
use ReflectionException;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * @throws ReflectionException
     */
    public function run()
    {
        helper('string_util');

        $username = 'admin';
        $password = generate_secure_password(6);

        $user_id = $this->db->table('oauth_users')->insert([
            'username' => 'admin',
            'password' => password_hash($password, PASSWORD_BCRYPT),
        ]);

        CLI::write('--------------------------------------------------------------------------------', 'green');
        CLI::write('User ID: ' . $user_id, 'green');
        CLI::write('Username: ' . $username, 'green');
        CLI::write('Password: ' . $password, 'yellow');
        CLI::write('--------------------------------------------------------------------------------', 'green');
    }
}
