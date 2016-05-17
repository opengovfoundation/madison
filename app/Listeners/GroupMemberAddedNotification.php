<?php

namespace App\Listeners;

use App\Events\GroupMemberAdded;
use App\Notification\Notifier;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GroupMemberAddedNotification implements ShouldQueue
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
     * @param  GroupMemberAdded  $event
     * @return void
     */
    public function handle(GroupMemberAdded $event)
    {
        $text = "You've been added to the group ".$event->groupMember->group->getDisplayName()." with the role of ".$event->groupMember->role.".";
        $data = [
            'text' => $text,
        ];
        $recipient = $event->groupMember->user;

        $this->notifier->queue('notification.simple-html', $data, function ($message) use ($recipient) {
            $message->setSubject("You've been added to a Madison group");
            $message->setRecipients($recipient);
        }, $event);
    }
}
