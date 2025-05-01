<?php

namespace Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;

class CreateClientSeeder extends Seeder
{
    public function run()
    {
        helper('string_util');

        $client_id = generate_uuid_string();
        $client_secret = generate_secure_password(16);

        $this->db->table('oauth_clients')->insert([
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'redirect_uri' => '',
        ]);

        CLI::write('--------------------------------------------------------------------------------', 'green');
        CLI::write('Client ID: ' . $client_id, 'green');
        CLI::write('Client Secret: ' . $client_secret, 'yellow');
        CLI::write('--------------------------------------------------------------------------------', 'green');
    }
}
