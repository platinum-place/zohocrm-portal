<?php

namespace App\Libraries\Bshaffer;

use OAuth2\GrantType\AuthorizationCode;
use OAuth2\GrantType\ClientCredentials;
use OAuth2\Server;
use OAuth2\Storage\Pdo;

/**
 * Docs: https://bshaffer.github.io/oauth2-server-php-docs/
 */
class OAuth2
{
    public $server;

    protected $storage;

    protected $dsn;

    protected $db_username;

    protected $db_password;

    public function __construct()
    {
        $this->dsn = 'mysql:dbname=' . getenv('database.default.database') . ';host=' . getenv('database.default.hostname');

        $this->db_username = getenv('database.default.username');

        $this->db_password = getenv('database.default.password');

        $this->initialize();
    }

    public function initialize()
    {
        // $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
        $this->storage = new Pdo(array('dsn' => $this->dsn, 'username' => $this->db_username, 'password' => $this->db_password));

        // Pass a storage object or array of storage objects to the OAuth2 server class
        $this->server = new Server($this->storage);

        // Add the "Client Credentials" grant type (it is the simplest of the grant types)
        $this->server->addGrantType(new ClientCredentials($this->storage));

        // Add the "Authorization Code" grant type (this is where the oauth magic happens)
        $this->server->addGrantType(new AuthorizationCode($this->storage));
    }
}