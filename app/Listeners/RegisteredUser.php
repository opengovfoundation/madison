<?php

namespace App\Listeners;

use App\Mail\EmailVerification;
use App\Models\Role;
use App\Models\User;
use App\Notifications\UserCreated;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Mail;
use Notification;

class RegisteredUser
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        // send email to user for email verification
        Mail
            ::to($event->user)
            ->send(new EmailVerification($event->user));

        // notify admins of user creation
        $admins = User::findByRoleName(Role::ROLE_ADMIN);
        Notification::send($admins, new UserCreated($event->user));
    }
}
