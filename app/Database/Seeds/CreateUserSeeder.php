<?php

namespace App\Database\Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;

class CreateUserSeeder extends Seeder
{
    public function run()
    {
        helper('string_util');
        $faker = \Faker\Factory::create();

        $username = $faker->userName;
        $password = generate_secure_password(6);

        $this->db->table('oauth_users')->insert([
            'username' => $username,
            'password' => sha1($password),
        ]);

        CLI::write('--------------------------------------------------------------------------------', 'green');
        CLI::write('Username: ' . $username, 'green');
        CLI::write('Password: ' . $password, 'yellow');
        CLI::write('--------------------------------------------------------------------------------', 'green');
    }
}
