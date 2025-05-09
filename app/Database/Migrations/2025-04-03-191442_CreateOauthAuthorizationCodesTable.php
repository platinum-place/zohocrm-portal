<?php


use CodeIgniter\Database\Migration;

class CreateOauthAuthorizationCodesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'authorization_code' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => false],
            'client_id'          => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => false],
            'user_id'            => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'redirect_uri'       => ['type' => 'VARCHAR', 'constraint' => 2000, 'null' => true],
            'expires'            => ['type' => 'DATETIME', 'null' => false],
            'scope'              => ['type' => 'TEXT', 'null' => true],
            'id_token'           => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('authorization_code');
        $this->forge->createTable('oauth_authorization_codes');
    }

    public function down()
    {
        $this->forge->dropTable('oauth_authorization_codes');
    }
}
