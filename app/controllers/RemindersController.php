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
		    $message->subject('Madison Password Reset');
		})) {

			case Password::INVALID_USER:
				return Redirect::back()->with('error', Lang::get($response));

			case Password::REMINDER_SENT:
				return Redirect::back()->with('message', 'Password change message sent.');
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

}
