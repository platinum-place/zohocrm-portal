<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class User extends BaseController
{
    protected $helpers = ['form'];

    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $userModel = new UserModel();

        $users = $userModel->findAll();

        return view('user/index', [
            'users' => $users,
        ]);
    }

    public function edit($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('admin/users')->with('message', 'Usuario no encontrado.');
        }

        return view('user/edit', [
            'user' => $user,
        ]);
    }

    public function update($id)
    {
        $rules = [
            'username' => [
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'El campo Usuario es obligatorio.',
                    'min_length' => 'El campo Usuario debe tener al menos 3 caracteres.',
                    'max_length' => 'El campo Usuario no puede exceder los 100 caracteres.'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'El campo Correo Electrónico es obligatorio.',
                    'valid_email' => 'Debe proporcionar un correo electrónico válido.'
                ]
            ],
            'first_name' => [
                'rules' => 'required|min_length[2]|max_length[100]',
                'errors' => [
                    'required' => 'El campo Nombre es obligatorio.',
                    'min_length' => 'El campo Nombre debe tener al menos 2 caracteres.',
                    'max_length' => 'El campo Nombre no puede exceder los 100 caracteres.'
                ]
            ],
            'last_name' => [
                'rules' => 'required|min_length[2]|max_length[100]',
                'errors' => [
                    'required' => 'El campo Apellido es obligatorio.',
                    'min_length' => 'El campo Apellido debe tener al menos 2 caracteres.',
                    'max_length' => 'El campo Apellido no puede exceder los 100 caracteres.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('admin/users')->with('error', 'Usuario no encontrado.');
        }

        $updateData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
        ];

        $this->userModel->update($id, $updateData);

        return redirect()->to('admin/users')->with('alert', 'Usuario actualizado con éxito.');
    }

    public function resetPassword($id)
    {
        helper('string_util');

        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('admin/users')->with('error', 'Usuario no encontrado.');
        }

        $password = generate_secure_password(16);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $this->userModel->update($id, ['password' => $hashedPassword]);

        return redirect()->to('admin/users')->with('alert', 'Contraseña restablecida con éxito. Nueva contraseña: ' . esc($password));
    }
}
