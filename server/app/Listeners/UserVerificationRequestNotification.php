<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\Role;
use App\Events\UserVerificationRequest;
use App\Notification\Notifier;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use URL;


class UserVerificationRequestNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Notifier $notifier)
    {
        $this->notifier = $notifier;
    }

    /**
     * Handle the event.
     *
     * @param  UserVerificationRequest $event
     * @return void
     */
    public function handle(UserVerificationRequest $event)
    {
        $fname = $event->user->fname;
        $lname = $event->user->lname;
        $email = $event->user->email;
        $url = URL::to('administrative-dashboard/verify-account');

        $text = "$fname $lname ($email) is requesting account verification.";
        $text .= " <a href='$url'>View the request.</a>.";

        $data = [
            'text' => $text,
        ];
        $admins = User::findByRoleName(Role::ROLE_ADMIN);

        $this->notifier->queue('notification.simple-html', $data, function($message) use ($admins) {
            $message->setSubject('New user requesting account verification');
            $message->setRecipients($admins);
        }, $event);
    }
}
