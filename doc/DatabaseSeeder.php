<?php

namespace doc;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('CreateDefaultRoleSeeder');
        $this->call('CreateAdminUserSeeder');
    }
}
