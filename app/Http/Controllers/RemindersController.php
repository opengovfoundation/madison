<?php

namespace App\Http\Controllers;

use Input;
use Response;
use View;
use Mail;
use Lang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RemindersController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Handle a POST request to remind a user of their password.
     *
     * @return Response
     */
    public function postRemind(Request $request)
    {

        $this->validate($request, ['email' => 'required|email']);

        $response = Password::sendResetLink($request->only('email'), function (Message $message) {
            $message->subject('Password Reset');
        });

        switch ($response) {
            case Password::RESET_LINK_SENT:
                return Response::json($this->growlMessage('Password change instructions have been sent to your email address.', 'warning'));

            case Password::INVALID_USER:
                return Response::json($this->growlMessage(Lang::get($response)), 'error');
        }
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

        $response = Password::reset($credentials, function ($user, $password) {
            $user->password = $password;

            $user->save();
        });

        switch ($response) {
            case Password::INVALID_PASSWORD:
            case Password::INVALID_TOKEN:
            case Password::INVALID_USER:
                return Response::json($this->growlMessage(Lang::get($response)), 'error');

            case Password::PASSWORD_RESET:
                return Response::json($this->growlMessage('Password changed successfully.', 'success'));
        }
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

        if (!isset($user)) {
            return Response::json($this->growlMessage('That email does not exist.', 'error'), 400);
        }

        if (empty($user->token)) {
            return Response::json($this->growlMessage('That user was already confirmed.', 'error'), 400);
        }

        if (!Hash::check($password, $user->password)) {
            return Response::json($this->growlMessage('The password for that email is incorrect.', 'error'), 400);
        }

        $token = $user->token;
        $email = $user->email;
        $fname = $user->fname;

        //Send email to user for email account verification
        Mail::queue('email.signup', array('token' => $token), function ($message) use ($email, $fname) {
            $message->subject('Welcome to the Madison Community');
            $message->from('sayhello@opengovfoundation.org', 'Madison');
            $message->to($email);
        });

        return Response::json($this->growlMessage('An email has been sent to your email address.  Please follow the instructions in the email to confirm your email address before logging in.', 'warning'));
    }
}
