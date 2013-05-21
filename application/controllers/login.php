<?php
/**
 * 	Controller for login / account creation actions
 */
class Login_Controller extends Base_Controller{
	public $restful = true;
	
	/**
	 * 	GET Login Page
	 */
	public function get_index(){
		return View::make('login.index');
	}
	
	
	/**
	 * 	POST to log user in
	 */
	public function post_index(){
		//Retrieve POST values
		$username = Input::get('email');
		$password = Input::get('password');
		$user_details = Input::all();
		
		//Rules for login form submission
		$rules = array('email' => 'required', 'password' => 'required');
		$validation = Validator::make($user_details, $rules);
		
		//Validate input against rules
		if($validation->fails()){
			return Redirect::to('login')->with_input()->with_errors($validation);
		}
		
		//Check that the user account exists
		$user = User::where_email($username)->first();
		if(!isset($user)){
			return Redirect::to('login')->with('error', 'That email does not exist.');
		}
		
		//If the user's token field isn't blank, he/she hasn't confirmed their account via email
		if($user->token != ''){
			return Redirect::to('login')->with('error', 'Please click the link sent to your email to verify your account.');
		}
		
		//Attempt to log user in
		$credentials = array('username' => $username, 'password' => $password);
		if(Auth::attempt($credentials)){
			return Redirect::home();
		}
		else{
			return Redirect::to('login')->with('error', 'Incorrect login credentials');
		}
	}
	
	/**
	 * 	GET Signup Page
	 */
	public function get_signup(){
		return View::make('login.signup');
	}
	
	/**
	 * 	POST to create user account
	 */
	public function post_signup(){
		//Retrieve POST values
		$username = Input::get('email');
		$password = Input::get('password');
		$user_details = Input::all();
		
		//Rules for signup form submission
		$rules = array('email'		=>	'required|unique:users',
						'password'	=>	'required',
						'fname'		=>	'required',
						'lname'		=>	'required'
						);
		$validation = Validator::make($user_details, $rules);
		if($validation->fails()){
			return Redirect::to('signup')->with_input()->with_errors($validation);
		}
		else{
			//Create user token for email verification
			$token = substr(urlencode(Hash::make($username)), 0, 25);
			
			//Create new user
			$user = new User();
			$user->email = $username;
			$user->password = Hash::make($password);
			$user->fname = Input::get('fname');
			$user->lname = Input::get('lname');
			$user->token = $token;
			$user->save();
			
			//Send email to user for email account verification
			Message::to($username)
				->from('info@madison.org', 'Madison')
				->subject('Madison Email Confirmation')
				->body('Please confirm your email for user ' . $user->id . ' by clicking <a href="' . URL::to_action('verify', array($user->id, $token)) . '">here</a>')
				->html(true)
				->send();
			
			return Redirect::to('login')->with('message', 'An email has been sent to your email address.  Please follow the instructions in the email to confirm your email address before logging in.');
		}
		
	}
	
	/**
	 * 	Verify users from link sent via email upon signup
	 */
	public function get_verify($id, $token){
		$user = User::find($id);
		
		if(!strcmp($token, $user->token)){
			$user->token = '';
			$user->save();
			
			Auth::login($user->id);
			
			return Redirect::home()->with('success_message', 'Your email has been verified and you have been logged in.  Welcome ' . $user->fname);
		}else{
			return Redirect::to('login')->with('error', 'The verification link is invalid.');
		}
		
	}
}
