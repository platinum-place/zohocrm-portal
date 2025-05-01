<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Zoho extends BaseConfig
{
    public $user;
    public $url_token;
    public $url_crm_api;
    public $redirect_uri;

    public $client_id;
    public $client_secret;
    public $refresh_token;

    public function __construct()
    {
        parent::__construct();

        $this->user = env('ZOHO_USER', '');
        $this->url_token = env('ZOHO_URL_TOKEN', '');
        $this->url_crm_api = env('ZOHO_URL_CRM_API', '');
        $this->redirect_uri = env('ZOHO_REDIRECT_URI', '');
        $this->client_id = env('ZOHO_CLIENT_ID', '');
        $this->client_secret = env('ZOHO_CLIENT_SECRET', '');
        $this->refresh_token = env('ZOHO_REFRESH_TOKEN', '');
    }
}
