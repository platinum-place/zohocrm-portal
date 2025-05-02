<?php


use CodeIgniter\Database\Migration;

class CreateOauthRefreshTokensTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'refresh_token' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => false],
            'client_id'     => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => false],
            'user_id'       => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'expires'       => ['type' => 'DATETIME', 'null' => false],
            'scope'         => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('refresh_token');
        $this->forge->createTable('oauth_refresh_tokens');
    }

    public function down()
    {
        $this->forge->dropTable('oauth_refresh_tokens');
    }
}
