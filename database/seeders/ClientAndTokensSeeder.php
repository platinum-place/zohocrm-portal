<?php

namespace Database\Seeders;

use App\Models\User\Client;
use App\Models\User\User;
use App\Models\Zoho\ZohoOauthRefreshToken;
use Illuminate\Database\Seeder;

class ClientAndTokensSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //        Client::insert([
        //            'id' => '219e9eba-ef93-4d48-88d6-e2149d49d5aa',
        //            'secret' => 'b006edb019d0824015ae62e0c6deb079',
        //            'name' => config('app.name'),
        //            'redirect_uris' => '',
        //            'grant_types' => 'client_credentials',
        //            'revoked' => false,
        //        ]);
        //        Client::firstWhere('id', '0196f9fd-bebf-70b6-9add-95bcdda4119d')->update(['id' => '219e9eba-ef93-4d48-88d6-e2149d49d5aa','secret' => 'b006edb019d0824015ae62e0c6deb079']);

        ZohoOauthRefreshToken::create([
            'refresh_token' => '1000.c96967ba181c367d896086bc6379592d.ac8fcf53cd16614731bd72443b13e7bf',
        ]);

        User::factory()->create([
            'username' => 'caribe',
            'password' => '3lw1yy37',
        ]);
    }
}
