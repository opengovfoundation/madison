<?php

namespace App\Listeners;

use App\Events\SponsorMemberAdded;
use App\Notifications\AddedToSponsor;
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
        // notify the user
        $event->sponsorMember->user->notify(new AddedToSponsor($event->sponsorMember, $event->instigator));
    }
}
