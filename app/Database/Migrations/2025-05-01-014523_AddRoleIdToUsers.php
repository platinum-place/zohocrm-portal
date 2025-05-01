<?php


use CodeIgniter\Database\Migration;

class AddRoleIdToUsers extends Migration
{
    public function up()
    {
        // Primero agregamos la columna
        $this->forge->addColumn('oauth_users', [
            'role_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'          => true,
                'after'         => 'password'
            ]
        ]);

        // Agregamos la llave foránea
        $sql = "ALTER TABLE oauth_users 
                ADD CONSTRAINT oauth_users_role_id_foreign 
                FOREIGN KEY (role_id) REFERENCES roles(id) 
                ON DELETE CASCADE ON UPDATE CASCADE";
        $this->db->query($sql);
    }

    public function down()
    {
        // Primero eliminamos la llave foránea
        $sql = "ALTER TABLE oauth_users DROP FOREIGN KEY oauth_users_role_id_foreign";
        $this->db->query($sql);

        // Luego eliminamos la columna
        $this->forge->dropColumn('oauth_users', 'role_id');
    }
}