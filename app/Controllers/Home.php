<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Home extends ResourceController
{
    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        //return view('welcome_message');
        return $this->respond([
            'app_name' => 'IT - Insurance Tech',
            'version' => '1.0.0',
        ]);
    }
}
