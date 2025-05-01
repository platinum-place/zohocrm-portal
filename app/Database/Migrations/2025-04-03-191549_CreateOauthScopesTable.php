<?php


use CodeIgniter\Database\Migration;

class CreateOauthScopesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'scope'      => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => false],
            'is_default' => ['type' => 'BOOLEAN', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('scope');
        $this->forge->createTable('oauth_scopes');
    }

    public function down()
    {
        $this->forge->dropTable('oauth_scopes');
    }
}
