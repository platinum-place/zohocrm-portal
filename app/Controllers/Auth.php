<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;

class Auth extends BaseController
{
    use ResponseTrait;

    protected $helpers = ['url', 'form'];

    public function login()
    {
        return view('auth/login');
    }
}