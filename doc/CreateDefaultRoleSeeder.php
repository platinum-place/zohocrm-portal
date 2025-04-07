<?php

namespace doc;

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
