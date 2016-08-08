<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\Role;
use App\Events\IndependentSponsorRequest;
use App\Notification\Notifier;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use URL;


class IndependentSponsorRequestNotification implements ShouldQueue
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
     * @param  IndependentSponsorRequest $event
     * @return void
     */
    public function handle(IndependentSponsorRequest $event)
    {
        $data = [
            'user' => $event->user,
        ];
        $admins = User::findByRoleName(Role::ROLE_ADMIN);

        $this->notifier->queue('email.notification.independent_sponsor_request', $data, function($message) use ($admins) {
            $message->setSubject('User requesting independent sponsor status');
            $message->setRecipients($admins);
        }, $event);
    }
}
