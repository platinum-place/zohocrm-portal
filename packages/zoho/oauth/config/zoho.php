<?php

return [
    'oauth' => [
        'redirect_uri' => env('ZOHO_OAUTH_API_REDIRECT_URL'),
        'client_id' => env('ZOHO_OAUTH_API_CLIENT_ID'),
        'client_secret' => env('ZOHO_OAUTH_API_CLIENT_SECRET'),
        'uri' => 'oauth/v2/token',
    ],
];
