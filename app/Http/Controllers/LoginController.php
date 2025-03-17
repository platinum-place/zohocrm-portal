<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        return view('login.index');
    }

    public function login(Request $request)
    {

    }

    public function logout(Request $request)
    {

    }
}
