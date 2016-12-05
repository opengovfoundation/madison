<?php

namespace App\Listeners;

use App\Events\GroupCreated;
use App\Models\Role;
use App\Notification\Notifier;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GroupCreatedNotification implements ShouldQueue
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
     * @param  GroupCreated  $event
     * @return void
     */
    public function handle(GroupCreated $event)
    {
        $adminRole = Role::where('name', Role::ROLE_ADMIN)->first();
        $admins = $adminRole->users;

        $data = [
            'group' => $event->group,
        ];

        if ($event->group->individual == 1) {
            $view = 'notification.group.created-independent-html';
            $subject = 'A user is requesting approval as an independent sponsor';
        } else {
            $view = 'notification.group.created-html';
            $subject = 'A new group has been created and needs approval';
        }

        $this->notifier->queue($view, $data, function ($message) use ($admins, $subject) {
            $message->setSubject($subject);
            $message->setRecipients($admins);
        }, $event);
    }
}
