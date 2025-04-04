<?php

namespace App\Controllers;

use App\Libraries\Bshaffer\OAuth2;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use OAuth2\Request;

class OAuth extends ResourceController
{
    protected $format = 'json';
    protected $oauth;
    protected $oauth_request;

    public function __construct()
    {
        $this->oauth = new OAuth2();
        $this->oauth_request = new Request();
    }

    public function token()
    {
        $this->oauth_request = $this->oauth->server->handleTokenRequest(
            $this->oauth_request->createFromGlobals()
        );

        $code = $this->oauth_request->getStatusCode();
        $body = $this->oauth_request->getResponseBody();
        $response = json_decode($body);

        if ($code == 200) {
            return $this->respond($response);
        } else {
            return $this->fail([
                'error_type' => $response->error,
                'description' => $response->error_description
            ]);
        }
    }
}
