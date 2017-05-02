<?php

namespace App\Listeners;

use App\Events\SponsorMemberRemoved;
use App\Notifications\RemovedFromSponsor;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SponsorMemberRemovedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  SponsorMemberRemoved  $event
     * @return void
     */
    public function handle(SponsorMemberRemoved $event)
    {
        // notify the user
        $event->member->notify(new RemovedFromSponsor($event->sponsor, $event->member, $event->instigator));
    }
}
