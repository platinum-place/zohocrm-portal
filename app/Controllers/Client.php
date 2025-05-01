<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClientModel;
use App\Models\RoleModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Client extends BaseController
{
    protected $helpers = ['form'];

    protected ClientModel $clientModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
    }

    public function index()
    {
        $clients = $this->clientModel->findAll();

        return view('client/index', [
            'clients' => $clients,
        ]);
    }

    public function edit($id)
    {
        $client = $this->clientModel->find($id);

        if (!$client) {
            return redirect()->to('admin/users')->with('message', 'Cliente no encontrado.');
        }

        $userModel = new UserModel();
        $users = $userModel->findAll();

        return view('client/edit', [
            'client' => $client,
            'users' => $users,
        ]);

    }

    public function update($id)
    {
        $rules = [
            'user_id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Debes selecionar un usuario para el usuario.',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'user_id' => $this->request->getPost('user_id'),
        ];

        $this->clientModel->update($id, $updateData);

        return redirect()->to('admin/clients')->with('alert', 'Cliente actualizado con éxito.');
    }
}
