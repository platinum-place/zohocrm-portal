<?php

namespace App\Controllers\Api;


use App\Controllers\BaseController;
use App\Models\ClientModel;
use App\Models\RoleModel;
use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class User extends ResourceController
{
    protected $format = 'json';

    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $users = $this->userModel->findAll();

        return $this->response->setJSON($users);
    }

    public function show($id = null)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Usuario no encontrado'
                ]);
        }

        unset($user['password']);

        return $this->response->setJSON($user);
    }

}
