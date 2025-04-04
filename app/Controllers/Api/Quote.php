<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class Quote extends ResourceController
{
    protected $format = 'json';

    public function estimateColectiva()
    {
        return $this->respond(['hola']);
    }
}