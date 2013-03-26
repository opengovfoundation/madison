<?php
class User_Controller extends Base_Controller{
	
	/*
	public function action_login(){
		return View::make('user.login');
	}
	public function action_signup(){
		return View::make('user.signup');
	}
	
	
	public function action_authenticate(){
		$email = Input::get('email');
		$password = Input::get('password');
		$new_user = Input::get('new_user', 'off');
		
		if($new_user == 'on'){
			try{
				$user = new User();
				$user->email = $email;
				$user->password = Hash::make($password);
				$user->fname = Input::get('fname');
				$user->lname = Input::get('lname');
				$user->save();
				Auth::login($user);
				
				return Redirect::home();
			}catch(Exception $e){
				echo "Failed to create user! ($e->getMessage())";
			}
		}else{
			$credentials = array(
				'username' => $email,
				'password' => $password
			);
			if(Auth::attempt($credentials)){
				return Redirect::home();
			}else{
				return Redirect::to('login')->with_errors('Failed to log in!');
			}
		}
	}
	*/
}



