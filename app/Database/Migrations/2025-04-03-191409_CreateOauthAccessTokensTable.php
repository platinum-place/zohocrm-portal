<?php


use CodeIgniter\Database\Migration;

class CreateOauthAccessTokensTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'access_token' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => false],
            'client_id'    => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => false],
            'user_id'      => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'expires'      => ['type' => 'DATETIME', 'null' => false],
            'scope'        => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('access_token');
        $this->forge->createTable('oauth_access_tokens');
    }

    public function down()
    {
        $this->forge->dropTable('oauth_access_tokens');
    }
}
