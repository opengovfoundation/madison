<?php
/**
 * 	Controller for User Login/Signup API actions
 */
class UserManageApiController extends ApiController{

	public function __construct(){
		parent::__construct();
	}

	public function getLogin(){
		return View::make('login.api.index');
	}

	public function postLogin(){
		//Retrieve POST values
		$email = Input::get('email');
		$password = Input::get('password');
		$previous_page = Input::get('previous_page');
		$user_details = Input::all();

		//Rules for login form submission
		$rules = array('email' => 'required', 'password' => 'required');
		$validation = Validator::make($user_details, $rules);

		//Validate input against rules
		if($validation->fails()){
			return Response::json( array( 'status' => 'error', 'errors' => $validation->messages()->getMessages() ) );
		}

		//Check that the user account exists
		$user = User::where('email', $email)->first();

		if(!isset($user)){
			return Response::json( array( 'status' => 'error', 'errors' => array('No such user') ) );
		}

		//If the user's token field isn't blank, he/she hasn't confirmed their account via email
		if($user->token != ''){
			return Response::json( array( 'status' => 'error',
				'errors' => array('Please click the link sent to your email to verify your account.') ) );
		}

		//Attempt to log user in
		$credentials = array('email' => $email, 'password' => $password);

		if(Auth::attempt($credentials)){
			return Response::json( array( 'status' => 'ok', 'errors' => array() ) );
		}
		else {
			return Response::json( array( 'status' => 'error',
				'errors' => array('The email address or password is incorrect.') ) );
		}
	}

	/**
	 * 	GET Signup Page
	 */
	public function getSignup(){
		return View::make('login.api.signup');
	}

	/**
	 * 	POST to create user account
	 */
	public function postSignup(){
		//Retrieve POST values
		$email = Input::get('email');
		$password = Input::get('password');
		$fname = Input::get('fname');
		$lname = Input::get('lname');
		$user_details = Input::all();

		//Rules for signup form submission
		$rules = array('email'		=>	'required|unique:users',
						'password'	=>	'required',
						'fname'		=>	'required',
						'lname'		=>	'required'
						);
		$validation = Validator::make($user_details, $rules);
		if($validation->fails()){
			return Response::json( array( 'status' => 'error',
				'errors' => $validation->messages()->getMessages() ) );
		}
		else{
			//Create user token for email verification
			$token = str_random();

			//Create new user
			$user = new User();
			$user->email = $email;
			$user->password = Hash::make($password);
			$user->fname = $fname;
			$user->lname = $lname;
			$user->token = $token;
			$user->save();

			//Send email to user for email account verification
			Mail::queue('email.signup', array('token'=>$token), function ($message) use ($email, $fname) {
    			$message->subject('Welcome to the Madison Community');
    			$message->from('sayhello@opengovfoundation.org', 'Madison');
    			$message->to($email); // Recipient address
			});

			return Response::json( array( 'status' => 'ok', 'errors' => array(), 'message' =>
				'An email has been sent to your email address.  Please follow the instructions in the email to confirm your email address before logging in.') );
		}

	}
}
