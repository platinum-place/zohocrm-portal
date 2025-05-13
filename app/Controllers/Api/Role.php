<?php

namespace App\Controllers\Api;


use App\Controllers\BaseController;
use App\Models\ClientModel;
use App\Models\RoleModel;
use CodeIgniter\RESTful\ResourceController;

class Role extends ResourceController
{
    protected $format = 'json';

    protected RoleModel $roleModel;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
    }

    public function index()
    {
        $Roles = $this->roleModel->findAll();

        return $this->response->setJSON($Roles);
    }

    public function show($id = null)
    {
        $Role = $this->roleModel->find($id);

        if (!$Role) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Usuario no encontrado'
                ]);
        }

        return $this->response->setJSON($Role);
    }
}
