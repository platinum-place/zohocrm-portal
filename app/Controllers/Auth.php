<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    protected $helpers = ['form'];

    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        return view('auth/login');
    }

    public function login()
    {
        $rules = [
            'username' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo Uusario es obligatorio',
                ]
            ],
            'password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'La contraseña es obligatoria',
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            $session = session();

            $userData = [
                'user' => $user,
                'logged_in' => true
            ];

            $session->set($userData);
            return redirect()->to(site_url('/'));
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Usuario o contraseña incorrectos');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}