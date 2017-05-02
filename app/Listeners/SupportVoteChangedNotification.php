<?php

namespace App\Listeners;

use App\Events\SupportVoteChanged;
use App\Notifications;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Notification;

class SupportVoteChangedNotification implements ShouldQueue
{
    public function handle(SupportVoteChanged $event)
    {
        // if they aren't just removing their vote, then notify the sponsors
        if ($event->newValue !== null) {
            // notify the sponsors of the document
            $sponsors = $event->document->sponsors;
            $members = $sponsors
                ->flatMap(function ($sponsor) {
                    return $sponsor->members->pluck('user');
                })
                ->unique('id');

            Notification::send($members, new Notifications\SupportVoteChanged(
                $event->oldValue,
                $event->newValue,
                $event->document,
                $event->user
            ));
        }
    }
}
