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
}
