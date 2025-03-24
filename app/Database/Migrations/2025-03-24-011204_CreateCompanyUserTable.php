<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCompanyUserTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'company_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey(['company_id', 'user_id'], true);

        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('company_user');
    }

    public function down()
    {
        $this->forge->dropTable('company_user');
    }
}
