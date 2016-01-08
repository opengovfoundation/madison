<?php

namespace App\Http\Controllers;

use Input;
use Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function token()
    {
        return csrf_token();
    }

    public function login()
    {
        $email = Input::get('email');
        $password = Input::get('password');

        if (Auth::attempt(Input::only('email', 'password'))) {
            return Auth::user();
        } else {
            return $this->growlMessage('invalid email / password', 'error');
        }
    }

    public function logout()
    {
        return Auth::logout();
    }
}
