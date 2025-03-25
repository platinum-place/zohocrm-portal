<?php

namespace App\Libraries;

use Exception;

class ZohoClient
{
    protected $client;

    protected $config;

    public function __construct()
    {
        $this->client = \Config\Services::curlrequest();
        $this->config = config('Zoho');
    }

    /**
     * @throws Exception
     */
    protected function generateRefreshToken(string $code)
    {
        $response = $this->client->request('post', $this->config->url_token, [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => $this->config->client_id,
                'client_secret' => $this->config->client_secret,
                'redirect_uri' => $this->config->redirect_uri,
                'code' => $code,
            ],
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new Exception($response->getBody());
        }

        return json_decode($response->getBody(), true);
    }

    /**
     * @throws Exception
     */
    protected function generaAccessToken()
    {
        $response = $this->client->request('post', $this->config->url_token, [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'client_id' => $this->config->client_id,
                'client_secret' => $this->config->client_secret,
                'refresh_token' => $this->config->refresh_token,
            ],
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new Exception($response->getBody());
        }

        return json_decode($response->getBody(), true);
    }

    /**
     * @throws Exception
     */
    public function recordCountInAModule(string $module_api_name, string $criteria)
    {
        $token = $this->generaAccessToken();

        $response = $this->client->request('get', $this->config->url_crm_api . "/$module_api_name/actions/count", [
            'headers' => [
                'Authorization' => 'Zoho-oauthtoken ' . $token['access_token'],
            ],
            'query' => [
                'criteria' => $criteria,
            ],
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new Exception($response->getBody());
        }

        return json_decode($response->getBody(), true);
    }
}