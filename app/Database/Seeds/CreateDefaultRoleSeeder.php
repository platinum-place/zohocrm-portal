<?php

namespace App\Database\Seeds;

use App\Models\RoleModel;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;
use ReflectionException;

class CreateDefaultRoleSeeder extends Seeder
{
    /**
     * @throws ReflectionException
     */
    public function run()
    {
        $roleModel = new RoleModel();
        $roleModel->insert(['name' => SUPERUSER]);
    }
}
