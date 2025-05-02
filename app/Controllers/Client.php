<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\UserModel;

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
            return redirect()->to('admin/clients')->with('message', 'Cliente no encontrado.');
        }

        $userModel = new UserModel();
        $users = $userModel->findAll();

        return view('client/edit', [
            'client' => $client,
            'users' => $users
        ]);
    }

    public function update($id)
    {
        $rules = [
            'client_secret' => [
                'rules' => 'required|min_length[10]|max_length[80]',
                'errors' => [
                    'required' => 'El campo Client Secret es obligatorio.',
                    'min_length' => 'El campo Client Secret debe tener al menos 10 caracteres.',
                    'max_length' => 'El campo Client Secret no puede exceder los 80 caracteres.'
                ]
            ],
//            'redirect_uri' => [
//                'rules' => 'required|valid_url',
//                'errors' => [
//                    'required' => 'El campo Redirect URI es obligatorio.',
//                    'valid_url' => 'Debe proporcionar una URI válida.'
//                ]
//            ],
            'grant_types' => [
                'rules' => 'required|min_length[5]|max_length[80]',
                'errors' => [
                    'required' => 'El campo Grant Types es obligatorio.',
                    'min_length' => 'El campo Grant Types debe tener al menos 5 caracteres.',
                    'max_length' => 'El campo Grant Types no puede exceder los 80 caracteres.'
                ]
            ],
            'scope' => [
                'rules' => 'max_length[4000]',
                'errors' => [
                    'max_length' => 'El campo Scope no puede exceder los 4000 caracteres.'
                ]
            ],
//            'user_id' => [
//                'rules' => 'required',
//                'errors' => [
//                    'required' => 'Debe seleccionar un usuario para el cliente.',
//                ]
//            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'client_secret' => $this->request->getPost('client_secret'),
            'redirect_uri'  => $this->request->getPost('redirect_uri'),
            'grant_types'   => $this->request->getPost('grant_types'),
            'scope'         => $this->request->getPost('scope'),
            'user_id'       => $this->request->getPost('user_id'),
        ];

        $this->clientModel->update($id, $updateData);

        return redirect()->to('admin/clients')->with('alert', 'Cliente actualizado con éxito.');
    }

    public function create()
    {
        $userModel = new UserModel();
        $users = $userModel->findAll();

        return view('client/create', [
            'users' => $users
        ]);
    }

    public function store()
    {
        $rules = [
            'client_id' => [
                'rules' => 'required|min_length[5]|max_length[80]|is_unique[oauth_clients.client_id]',
                'errors' => [
                    'required' => 'El campo Client ID es obligatorio.',
                    'min_length' => 'El campo Client ID debe tener al menos 5 caracteres.',
                    'max_length' => 'El campo Client ID no puede exceder los 80 caracteres.',
                    'is_unique' => 'El Client ID ya está en uso.',
                ]
            ],
            'client_secret' => [
                'rules' => 'required|min_length[10]|max_length[80]',
                'errors' => [
                    'required' => 'El campo Client Secret es obligatorio.',
                    'min_length' => 'El campo Client Secret debe tener al menos 10 caracteres.',
                    'max_length' => 'El campo Client Secret no puede exceder los 80 caracteres.',
                ]
            ],
            'redirect_uri' => [
                'rules' => 'required|valid_url',
                'errors' => [
                    'required' => 'El campo Redirect URI es obligatorio.',
                    'valid_url' => 'Debe proporcionar una URI válida.',
                ]
            ],
            'grant_types' => [
                'rules' => 'required|min_length[5]|max_length[80]',
                'errors' => [
                    'required' => 'El campo Grant Types es obligatorio.',
                    'min_length' => 'El campo Grant Types debe tener al menos 5 caracteres.',
                    'max_length' => 'El campo Grant Types no puede exceder los 80 caracteres.',
                ]
            ],
            'scope' => [
                'rules' => 'max_length[4000]',
                'errors' => [
                    'max_length' => 'El campo Scope no puede exceder los 4000 caracteres.',
                ]
            ],
            'user_id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Debe seleccionar un usuario para el cliente.',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'client_id'     => $this->request->getPost('client_id'),
            'client_secret' => $this->request->getPost('client_secret'),
            'redirect_uri'  => $this->request->getPost('redirect_uri'),
            'grant_types'   => $this->request->getPost('grant_types'),
            'scope'         => $this->request->getPost('scope'),
            'user_id'       => $this->request->getPost('user_id'),
        ];

        $this->clientModel->insert($data);

        return redirect()
            ->to('admin/clients')
            ->with('alert', 'Cliente creado con éxito.');
    }

    public function delete($id)
    {
        if ($this->clientModel->delete($id)) {
            return redirect()
                ->to('admin/clients')
                ->with('alert', 'Cliente eliminado con éxito.');
        } else {
            return redirect()
                ->to('admin/clients')
                ->with('alert', 'Ocurrió un problema al intentar eliminar al cliente.');
        }
    }
}