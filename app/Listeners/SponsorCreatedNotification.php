<?php

namespace App\Listeners;

use App\Events\SponsorCreated;
use App\Models\Role;
use App\Notification\Notifier;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SponsorCreatedNotification implements ShouldQueue
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
     * @param  SponsorCreated  $event
     * @return void
     */
    public function handle(SponsorCreated $event)
    {
        $adminRole = Role::where('name', Role::ROLE_ADMIN)->first();
        $admins = $adminRole->users;

        $data = [
            'sponsor' => $event->sponsor,
        ];

        $view = 'notification.sponsor.created-html';
        $subject = 'A new sponsor has been created and needs approval';

        $this->notifier->queue($view, $data, function ($message) use ($admins, $subject) {
            $message->setSubject($subject);
            $message->setRecipients($admins);
        }, $event);
    }
}
