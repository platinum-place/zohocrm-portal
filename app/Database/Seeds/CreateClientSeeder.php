<?php

namespace App\Database\Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;

class CreateClientSeeder extends Seeder
{
    public function run()
    {
        helper('string_util');

        $client_id = generate_uuid_string();
        $client_secret = generate_secure_password(16);

        $data = [
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'redirect_uri' => 'http://fake/',
        ];

        $this->db->table('oauth_clients')->insert($data);

        CLI::write('Client ID: ' . $client_id, 'green');
        CLI::write('Client Secret: ' . $client_secret, 'yellow');
    }
}
