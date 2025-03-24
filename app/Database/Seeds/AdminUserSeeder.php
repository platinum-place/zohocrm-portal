<?php

namespace App\Database\Seeds;

use App\Models\User;
use CodeIgniter\Database\Seeder;
use ReflectionException;

class AdminUserSeeder extends Seeder
{
    /**
     * @throws ReflectionException
     */
    public function run()
    {
        $user = new User();

        $user->save([
            'username' => 'admin',
            'email' => 'admin@gruponobe.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'zoho_id' => '',
            'is_admin' => true,
        ]);
    }
}
