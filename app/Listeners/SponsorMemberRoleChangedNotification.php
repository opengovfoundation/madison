<?php

namespace App\Listeners;

use App\Events\SponsorMemberRoleChanged;
use App\Notifications\UserSponsorRoleChanged;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SponsorMemberRoleChangedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  SponsorMemberRemoved  $event
     * @return void
     */
    public function handle(SponsorMemberRoleChanged $event)
    {
        // notify the user
        $event->sponsorMember->user->notify(new UserSponsorRoleChanged(
            $event->oldValue,
            $event->newValue,
            $event->sponsorMember,
            $event->instigator
        ));
    }
}
