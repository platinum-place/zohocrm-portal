<?php


use CodeIgniter\Database\Migration;

class CreateOauthUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'username'       => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => false],
            'password'       => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => false],
            'first_name'     => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'last_name'      => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'email'          => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'email_verified' => ['type' => 'BOOLEAN', 'null' => true],
            'scope'          => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('username');
        $this->forge->createTable('oauth_users');
    }

    public function down()
    {
        $this->forge->dropTable('oauth_users');
    }
}
