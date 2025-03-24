<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User;

class Login extends BaseController
{
    public function index()
    {
        return view('login/index');
    }

    public function login()
    {
        $model = new User();
        $user = $model->where('username', $this->request->getPost('username'))->first();

        if ($user && password_verify($this->request->getPost('password'), $user['password'])) {
            $session = session();
            $session->set([
                'user_id' => $user['id'],
                'username' => $user['username'],
                'zoho_id' => $user['zoho_id'],
                'logged_in' => true
            ]);
            return redirect()->to('/');
        } else {
            return redirect()->back()->with('alert', 'Credenciales inválidas.');
        }
    }
}
