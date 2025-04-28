<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class User extends BaseController
{
    protected $helpers = ['form'];

    public function index()
    {
        return view('user/index');
    }
}
