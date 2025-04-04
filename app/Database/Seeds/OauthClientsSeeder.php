<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OauthClientsSeeder extends Seeder
{
    public function run()
    {
        helper('string_util');

        $data = [
            'client_id' => generate_uuid_string(),
            'client_secret' => bin2hex(random_bytes(16)),
            'redirect_uri' => 'http://fake/',
        ];

        $this->db->table('oauth_clients')->insert($data);
    }
}
