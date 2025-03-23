<?php

namespace App\Clients;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class ZohoClient
{
    protected $config;

    public function __construct()
    {
        $this->config = config('zoho');
    }

    /**
     * @throws RequestException
     */
    protected function generateRefreshToken(string $code)
    {
        $response = Http::asForm()->post(config('zoho.url.token'), [
            'grant_type' => 'authorization_code',
            'client_id' => config('zoho.client_id'),
            'client_secret' => config('zoho.client_secret'),
            'redirect_uri' => config('zoho.url.redirect'),
            'code' => $code,
        ]);

        if (!$response->successful()) {
            $response->throw();
        }

        return $response->json();
    }

    /**
     * @throws RequestException
     */
    protected function generaAccessToken()
    {
        $response = Http::asForm()->post(config('zoho.url.token'), [
            'grant_type' => 'refresh_token',
            'client_id' => config('zoho.client_id'),
            'client_secret' => config('zoho.client_secret'),
            'redirect_uri' => config('zoho.url.redirect'),
            'code' => config('zoho.refresh_token'),
        ]);

        if (!$response->successful()) {
            $response->throw();
        }

        return $response->json();
    }

    /**
     * @throws RequestException
     */
    public function createToken(): array
    {
        $access_token = $this->generaAccessToken();

        return $access_token;
    }
}
