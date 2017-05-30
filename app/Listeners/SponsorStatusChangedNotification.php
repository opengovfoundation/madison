<?php

namespace App\Listeners;

use App\Events\SponsorStatusChanged;
use App\Mail\SponsorOnboarding;
use App\Models\Sponsor;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class SponsorStatusChangedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  SponsorMemberRemoved  $event
     * @return void
     */
    public function handle(SponsorStatusChanged $event)
    {
        if ($event->newValue === Sponsor::STATUS_ACTIVE) {
            foreach ($event->sponsor->members->pluck('user') as $member) {
                Mail::to($member)
                    ->send(new SponsorOnboarding\Publish($event->sponsor, $member));
            }
        }
    }
}
