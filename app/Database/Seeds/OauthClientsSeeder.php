<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OauthClientsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'client_id'     => 'testclient',
            'client_secret' => 'testpass',
            'redirect_uri'  => 'http://fake/',
        ];

        $this->db->table('oauth_clients')->insert($data);
    }
}
