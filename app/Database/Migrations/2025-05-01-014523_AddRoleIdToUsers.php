<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoleIdToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('oauth_users', [
            'role_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'          => false,
            ]
        ]);

        $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->forge->dropForeignKey('oauth_users', 'oauth_users_role_id_foreign');

        $this->forge->dropColumn('oauth_users', 'role_id');
    }
}