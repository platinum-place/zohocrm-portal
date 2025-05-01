<?php


use CodeIgniter\Database\Migration;

class CreateOauthJwtTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'client_id'  => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => false],
            'subject'    => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'public_key' => ['type' => 'TEXT', 'null' => false],
        ]);
        $this->forge->addKey('client_id');
        $this->forge->createTable('oauth_jwt');
    }

    public function down()
    {
        $this->forge->dropTable('oauth_jwt');
    }
}
