<?php
/**
 * 	Controller for user actions
 */
class UserController extends BaseController{

	public function __construct(){
		parent::__construct();
	}

	public function getIndex(User $user){
		//Set data array
		$data = array(
			'user'			=> $user,
			'page_id'		=> 'user_profile',
			'page_title'	=> $user->fname . ' ' . substr($user->lname, 0, 1) . "'s Profile"
		);

		//Render view and return
		return View::make('user.index', $data);
	}

	public function getEdit($id=null){
		if(!Auth::check()){
			return Redirect::to('user/login')->with('error', 'Please log in to edit user profile');
		}else if(Auth::user()->id != $id){
			return Redirect::back()->with('error', 'You do not have access to that profile.');
		}else if($id == null){
			return Response::error('404');
		}

		//Set data array
		$data = array(
			'page_id'		=> 'edit_profile',
			'page_title'	=> 'Edit Your Profile'
		);

		return View::make('user.edit.index', $data);
	}

	public function putEdit($id=null){
		if(!Auth::check()){
			return Redirect::to('user/login')->with('error', 'Please log in to edit user profile');
		}else if(Auth::user()->id != $id){
			return Redirect::back()->with('error', 'You do not have access to that profile.');
		}else if($id == null){
			return Response::error('404');
		}

		$email = Input::get('email');
		$fname = Input::get('fname');
		$lname = Input::get('lname');
		$url = Input::get('url');
		$phone = Input::get('phone');
		$verify = Input::get('verify');

		if(empty($phone)) {
			return Redirect::to('user/edit/' . $id)->with('error', 'A phone number is required to request verified status.');
		}

		$user_details = Input::all();

		if(Auth::user()->email != $email) {
			$rules = array(
				'fname'		=> 'required',
				'lname'		=> 'required',
				'email'		=> 'required|unique:users'
			);
		} else {
			$rules = array(
				'fname'		=> 'required',
				'lname'		=> 'required',
				'phone'		=> 'required'
			);
		}

		$validation = Validator::make($user_details, $rules);

		if($validation->fails()){
			return Redirect::to('user/edit/' . $id)->withInput()->withErrors($validation);
		}

		$user = User::find($id);
		$user->email = $email;
		$user->fname = $fname;
		$user->lname = $lname;
		$user->url = $url;
		$user->phone = $phone;
		// Don't allow oauth logins to update the user's data anymore,
		// since they've set values within Madison.
		$user->oauth_update = false;
		$user->save();

		if(isset($verify)){
			$meta = new UserMeta();
			$meta->meta_key = 'verify';
			$meta->meta_value = 'pending';
			$meta->user_id = $id;
			$meta->save();

			return Redirect::back()->with('success_message', 'Your profile has been updated')->with('message', 'Your verified status has been requested.');
		}

		return Redirect::back()->with('success_message', 'Your profile has been updated.');
	}

	public function putIndex($id = null){
		return Response::error('404');
	}

	public function postIndex($id = null){
		return Response::error('404');
	}

	public function getLogin(){
		$previous_page = Input::old('previous_page');

		if(!isset($previous_page)){
			$previous_page = URL::previous();
		}

		$data = array(
			'page_id'		=> 'login',
			'page_title'	=> 'Log In',
			'previous_page'	=> $previous_page
		);

		return View::make('login.index', $data);
	}

	public function postLogin(){
		//Retrieve POST values
		$email = Input::get('email');
		$password = Input::get('password');
		$previous_page = Input::get('previous_page');
		$remember = Input::get('remember');
		$user_details = Input::all();

		//Rules for login form submission
		$rules = array('email' => 'required', 'password' => 'required');
		$validation = Validator::make($user_details, $rules);

		//Validate input against rules
		if($validation->fails()){
			return Redirect::to('user/login')->withInput()->withErrors($validation);
		}

		//Check that the user account exists
		$user = User::where('email', $email)->first();

		if(!isset($user)){
			return Redirect::to('user/login')->with('error', 'That email does not exist.');
		}

		//If the user's token field isn't blank, he/she hasn't confirmed their account via email
		if($user->token != ''){
			return Redirect::to('user/login')->with('error', 'Please click the link sent to your email to verify your account.');
		}

		//Attempt to log user in
		$credentials = array('email' => $email, 'password' => $password);

		if(Auth::attempt($credentials, ($remember == 'true') ? true : false)){
			Auth::user()->last_login = new DateTime;
			Auth::user()->save();
			if(isset($previous_page)){
				return Redirect::to($previous_page)->with('message', 'You have been successfully logged in.');
			}else{
				return Redirect::to('/docs/')->with('message', 'You have been successfully logged in.');
			}
		}
		else{
			return Redirect::to('user/login')->with('error', 'Incorrect login credentials')->withInput(array('previous_page' => $previous_page));
		}
	}

	/**
	 * 	GET Signup Page
	 */
	public function getSignup(){
		$data = array(
			'page_id'		=> 'signup',
			'page_title'	=> 'Sign Up for Madison'
		);

		return View::make('login.signup', $data);
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
			return Redirect::to('user/signup')->withInput()->withErrors($validation);
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

			return Redirect::to('user/login')->with('message', 'An email has been sent to your email address.  Please follow the instructions in the email to confirm your email address before logging in.');
		}

	}

	/**
	 * 	Verify users from link sent via email upon signup
	 */
	public function getVerify($token){
		echo $token;
		$user = User::where('token', $token)->first();

		if(isset($user)){
			$user->token = '';
			$user->save();

			Auth::login($user);

			return Redirect::to('/')->with('success_message', 'Your email has been verified and you have been logged in.  Welcome ' . $user->fname);
		}else{
			return Redirect::to('user/login')->with('error', 'The verification link is invalid.');
		}

	}

	/**
	 * Login user with Facebook
	 */
	public function getFacebookLogin(){

		// get data from input
		$code = Input::get('code');

		// get fb service
		$fb = OAuth::consumer('Facebook');

		// check if code is valid

		// if code is provided get user data and sign in
		if(!empty($code)){

			// This was a callback request from facebook, get the token
			$token = $fb->requestAccessToken( $code );

			// Send a request with it
			$result = json_decode( $fb->request( '/me' ), true );

			// Remap the $result to something that matches our schema.
			$user_info = array(
				'fname' => $result['first_name'],
				'lname' => $result['last_name'],
				'email' => $result['email'],
				'oauth_vendor' => 'facebook',
				'oauth_id' => $result['id']
			);

			return $this->oauthLogin($user_info);
		}
		// if not ask for permission first
		else{
			// get fb authorization
			$url = $fb->getAuthorizationUri();

			// return to facebook login url
			 return Redirect::to( (string)$url );
		}
	}

	/**
	 * Login user with Twitter
	 */
	public function getTwitterLogin(){

	    // get data from input
	    $token = Input::get( 'oauth_token' );
	    $verify = Input::get( 'oauth_verifier' );

	    // get twitter service
	    $tw = OAuth::consumer( 'Twitter' );

	    // check if code is valid

	    // if code is provided get user data and sign in
	    if ( !empty( $token ) && !empty( $verify ) ) {

	        // This was a callback request from twitter, get the token
	        $token = $tw->requestAccessToken( $token, $verify );

	        // Send a request with it
	        $result = json_decode( $tw->request( 'account/verify_credentials.json' ), true );

			$user_info = array(
				'fname' => $result['name'],
				'lname' => '',
				'oauth_vendor' => 'twitter',
				'oauth_id' => $result['id']
			);

			return $this->oauthLogin($user_info);
	    }
	    // if not ask for permission first
	    else {
	        // get request token
	        $reqToken = $tw->requestRequestToken();

	        // get Authorization Uri sending the request token
	        $url = $tw->getAuthorizationUri(array('oauth_token' => $reqToken->getRequestToken()));

	        // return to twitter login url
	        return Redirect::to( (string)$url );
	    }
	}

	/**
	 * Login user with Linkedin
	 */
	public function getLinkedinLogin(){

        // get data from input
        $code = Input::get( 'code' );

        $linkedinService = OAuth::consumer( 'Linkedin' );


        if ( !empty( $code ) ) {

		    // retrieve the CSRF state parameter
		    $state = isset($_GET['state']) ? $_GET['state'] : null;

		    // This was a callback request from linkedin, get the token
		    $token = $linkedinService->requestAccessToken($_GET['code'], $state);

            // Send a request with it. Please note that XML is the default format.
            $result = json_decode($linkedinService->request('/people/~:(id,first-name,last-name,email-address)?format=json'), true);

			// Remap the $result to something that matches our schema.
			$user_info = array(
				'fname' => $result['firstName'],
				'lname' => $result['lastName'],
				'email' => $result['emailAddress'],
				'oauth_vendor' => 'linkedin',
				'oauth_id' => $result['id']
			);

			return $this->oauthLogin($user_info);

        }// if not ask for permission first
        else {
            // get linkedinService authorization
            $url = $linkedinService->getAuthorizationUri();

            // return to linkedin login url
            return Redirect::to( (string)$url );
        }
    }

	/**
	 * Use OAuth data to login user.  Create account if necessary.
	 */
	public function oauthLogin($user_info){
		// See if we already have a matching user in the system
		$user = User::where('oauth_vendor', $user_info['oauth_vendor'])
			->where('oauth_id', $user_info['oauth_id'])->first();

		if(!isset($user)){

			// Make sure this user doesn't already exist in the system.
			if(isset($user_info['email'])){
				$existing_user = User::where('email', $user_info['email'])->first();

				if(isset($existing_user)){
					return Redirect::to('user/login')->with('error',
						'It appears that you already have an account with that email address. Please login below.');
				}
			}

			// Create a new user since we don't have one.
			$user = new User();
			$user->oauth_vendor = $user_info['oauth_vendor'];
			$user->oauth_id = $user_info['oauth_id'];
			$user->oauth_update = true;

			$new_user = true;
		}

		// Now that we have a user for sure, update the user and log them in.
		$user->fname = $user_info['fname'];
		$user->lname = $user_info['lname'];
		if(isset($user_info['email'])){
			$user->email = $user_info['email'];
		}

		// If the user is new, or if we are allowed to update the user via oauth.
		// Note: The oauth_update flag is turned to off the first time the user
		// edits their account within Madison, locking in their info.
		if(isset($new_user) || (isset($user->oauth_update) && $user->oauth_update == true)) {
			$user->save();
		}

		Auth::login($user);

		if(isset($new_user)){
			$message = 'Welcome ' . $user->fname;
		}
		else{
			$message = 'Welcome back, ' . $user->fname;
		}

		return Redirect::to('/')->with('success_message', $message);
	}
}
