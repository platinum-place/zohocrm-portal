<?php

namespace App\Database\Seeds;

use App\Models\RoleModel;
use App\Models\UserModel;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;

class CreateAdminUserSeeder extends Seeder
{
    public function run()
    {
        helper('string_util');

        $password = generate_secure_password(6);

        $data = [
            'name' => 'Admin',
            'username' => 'admin',
            'email' => '',
            'password' => $password,
        ];

        $userModel = new UserModel();
        $id = $userModel->insert($data);

        $userModel->assignRole($id, 1);

        CLI::write('ID: ' . $id, 'green');
        CLI::write('Username: ' . $data['username'], 'green');
        CLI::write('Password: ' . $data['password'], 'yellow');
    }
}
