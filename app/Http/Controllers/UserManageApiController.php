<?php

namespace App\Http\Controllers;

use Input;
use Hash;
use Mail;
use Response;
use Validator;
use Auth;
use App\Models\User;

/**
 * 	Controller for User Login/Signup API actions.
 */

class UserManageApiController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function postLogin()
    {
        //Retrieve POST values
        $email = Input::get('email');
        $password = Input::get('password');
        $remember = Input::get('remember');
        $previous_page = Input::get('previous_page');
        $user_details = Input::all();

        //Rules for login form submission
        $rules = array('email' => 'required', 'password' => 'required');
        $validation = Validator::make($user_details, $rules);

        //Validate input against rules
        if ($validation->fails()) {
            $errors = $validation->messages()->getMessages();
            $messages = array();

            foreach ($errors as $error) {
                array_push($messages, $error[0]);
            }

            return Response::json($this->growlMessage($messages, 'error'), 401);
        }

        //Check that the user account exists
        $user = User::where('email', $email)->first();

        if (!isset($user)) {
            return Response::json($this->growlMessage('Email does not exist!', 'error'), 401);
        }

        //If the user's token field isn't blank, he/she hasn't confirmed their account via email
        if ($user->token != '') {
            return Response::json($this->growlMessage('Please click the link sent to your email to verify your account.', 'error'), 401);
        }

        //Attempt to log user in
        $credentials = array('email' => $email, 'password' => $password);

        if (Auth::attempt($credentials, ($remember === 'true') ? true : false)) {
            return Response::json(array( 'status' => 'ok', 'errors' => array() ));
        } else {
            return Response::json($this->growlMessage('The email address or password is incorrect.', 'error'), 401);
        }
    }

    /**
     * 	POST to create user account.
     */
    public function postSignup()
    {
        //Retrieve POST values
        $email = Input::get('email');
        $password = Input::get('password');
        $fname = Input::get('fname');
        $lname = Input::get('lname');
        $user_details = Input::all();

        //Rules for signup form submission
        $rules = array('email'        =>    'required|unique:users',
                        'password'    =>    'required',
                        'fname'        =>    'required',
                        'lname'        =>    'required',
                        );
        $validation = Validator::make($user_details, $rules);
        if ($validation->fails()) {
            $errors = $validation->messages()->getMessages();
            $messages = array();

            $replacements = array(
                'fname' => 'first name',
                'lname' => 'last name'
            );

            foreach ($errors as $error) {
                $error_message = str_replace(array_keys($replacements), array_values($replacements), $error[0]);

                array_push($messages, $error_message);
            }

            return Response::json($this->growlMessage($messages, 'error'), 500);
        } else {
            //Create user token for email verification
            $token = str_random();

            //Create new user
            $user = new User();
            $user->email = $email;
            $user->password = $password;
            $user->fname = $fname;
            $user->lname = $lname;
            $user->token = $token;
            $user->save();

            //Send email to user for email account verification
            Mail::queue('email.signup', array('token' => $token), function ($message) use ($email, $fname) {
                $message->subject('Welcome to the Madison Community');
                $message->from('sayhello@opengovfoundation.org', 'Madison');
                $message->to($email); // Recipient address
            });

            return Response::json(array( 'status' => 'ok', 'errors' => array(), 'message' => 'An email has been sent to your email address.  Please follow the instructions in the email to confirm your email address before logging in.'));
        }
    }
}
