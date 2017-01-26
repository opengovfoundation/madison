<?php

namespace App\Listeners;

use App\Events\SponsorMemberAdded;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SponsorMemberAddedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  SponsorMemberAdded  $event
     * @return void
     */
    public function handle(SponsorMemberAdded $event)
    {
        // $text = "You've been added to the sponsor ".$event->sponsorMember->sponsor->display_name." with the role of ".$event->sponsorMember->role.".";
        // $data = [
        //     'text' => $text,
        // ];
        // $recipient = $event->sponsorMember->user;

        // $this->notifier->queue('notification.simple-html', $data, function ($message) use ($recipient) {
        //     $message->setSubject("You've been added to a Madison sponsor");
        //     $message->setRecipients($recipient);
        // }, $event);
    }
}
