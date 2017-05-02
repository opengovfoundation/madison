<?php

namespace App\Listeners;

use App\Events\UserVerificationStatusChange;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserVerificationStatusChangeNotification implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  UserVerificationStatusChange  $event
     * @return void
     */
    public function handle(UserVerificationStatusChange $event)
    {
        // $text = "Your account verification status has changed from {$event->oldValue} to {$event->newValue}.";
        // $data = [
        //     'text' => $text,
        // ];
        // $recipient = $event->user;

        // $this->notifier->queue('notification.simple-html', $data, function ($message) use ($recipient) {
        //     $message->setSubject('Your account verification status has changed');
        //     $message->setRecipients($recipient);
        // }, $event);
    }
}
