<?php


use CodeIgniter\Database\Migration;

class CreateOauthClientsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'client_id'     => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => false],
            'client_secret' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'redirect_uri'  => ['type' => 'VARCHAR', 'constraint' => 2000, 'null' => true],
            'grant_types'   => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'scope'         => ['type' => 'VARCHAR', 'constraint' => 4000, 'null' => true],
            'user_id'       => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
        ]);
        $this->forge->addPrimaryKey('client_id');
        $this->forge->createTable('oauth_clients');
    }

    public function down()
    {
        $this->forge->dropTable('oauth_clients');
    }
}
