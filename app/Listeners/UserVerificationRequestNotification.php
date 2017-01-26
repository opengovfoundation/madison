<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\Role;
use App\Events\UserVerificationRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use URL;


class UserVerificationRequestNotification implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  UserVerificationRequest $event
     * @return void
     */
    public function handle(UserVerificationRequest $event)
    {
        // $data = [
        //     'user' => $event->user,
        // ];

        // $admins = User::findByRoleName(Role::ROLE_ADMIN);

        // $this->notifier->queue('email.notification.verify_request_user', $data, function($message) use ($admins) {
        //     $message->setSubject('New user requesting account verification');
        //     $message->setRecipients($admins);
        // }, $event);
    }
}
