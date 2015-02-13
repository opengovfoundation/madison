<?php

class RemindersController extends BaseController {


	public function __construct(){
		parent::__construct();
	}

	/**
	 * Display the password reminder view.
	 *
	 * @return Response
	 */
	public function getRemind()
	{
		$data = array(
			'page_id'		=> 'dashboard',
			'page_title'	=> 'Dashboard'
		);

		return View::make('password.remind', $data);
	}

	/**
	 * Handle a POST request to remind a user of their password.
	 *
	 * @return Response
	 */
	public function postRemind()
	{
		switch ($response = Password::remind(Input::only('email'), function($message){
		    $message->subject(trans('messages.resetemailtitle'));
		})) {

			case Password::INVALID_USER:
				return Redirect::back()->with('error', Lang::get($response));

			case Password::REMINDER_SENT:
				return Redirect::back()->with('message', trans('messages.remindersent'));
		}
	}

	/**
	 * Display the password reset view for the given token.
	 *
	 * @param  string  $token
	 * @return Response
	 */
	public function getReset($token = null)
	{
		if (is_null($token)) App::abort(404);

		$data = array(
			'page_id'		=> 'reset',
			'page_title'	=> 'Reset Password'
		);

		return View::make('password.reset', $data)->with('token', $token);
	}

	/**
	 * Handle a POST request to reset a user's password.
	 *
	 * @return Response
	 */
	public function postReset()
	{
		$credentials = Input::only(
			'email', 'password', 'password_confirmation', 'token'
		);

		$response = Password::reset($credentials, function($user, $password)
		{
			$user->password = Hash::make($password);

			$user->save();
		});

		switch ($response)
		{
			case Password::INVALID_PASSWORD:
			case Password::INVALID_TOKEN:
			case Password::INVALID_USER:
				return Redirect::back()->with('error', Lang::get($response));

			case Password::PASSWORD_RESET:
				return Redirect::to('/')->with('message', 'Password successfully changed.');;
		}
	}


	public function getConfirmation()
	{
		$data = array(
			'page_id'		=> 'dashboard',
			'page_title'	=> 'Resend confirmation email'
		);

		return View::make('password.resend', $data);
	}

	/**
	 * Handle a POST request to remind a user of their password.
	 *
	 * @return Response
	 */
	public function postConfirmation()
	{
		// 3 error cases - user already confirmed, email does not exist, password not correct
		// (prevents people from brute-forcing email addresses to see who is registered)

		$email = Input::get('email');
		$password = Input::get('password');
		$user = User::where('email', $email)->first();
		
		if(!isset($user)){
			return Redirect::to('verification/remind')->with('error', 'That email was not registered.');
		}

		if(empty($user->token)) {
			return Redirect::to('user/login')->with('error', 'That user was already confirmed.');
		}

		if (!Hash::check($password, $user->password)) {
			return Redirect::to('verification/remind')->with('error', 'The password for that email is incorrect.');
		} 
		
		$token = $user->token;
		$email = $user->email;
		$fname = $user->fname;

		//Send email to user for email account verification
		Mail::queue('email.signup', array('token'=>$token), function ($message) use ($email, $fname) {
    		$message->subject(trans('messages.confirmationtitle'));
    		$message->from(trans('messages.emailfrom'), trans('messages.emailfromname'));
    		$message->to($email);
		});
			
		return Redirect::to('user/login')->with('message', trans('messages.confirmationresent'));

	}

}
