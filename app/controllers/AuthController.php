<?php

class AuthController extends BaseController 
{
	public function token() {
		return csrf_token();
	}

	public function login() {
		$email = Input::get('email');
		$password = Input::get('password');

		if(Auth::attempt(Input::only('email', 'password'))){
			return Auth::user();
		} else {
			return $this->growlMessage('invalid email / password', 'error');
		}

	}
}