<?php

namespace App\Listeners;

use App\Events\SponsorCreated;
use App\Models\Role;
use App\Models\User;
use App\Notifications\SponsorNeedsApproval;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Notification;

class SponsorCreatedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  SponsorCreated  $event
     * @return void
     */
    public function handle(SponsorCreated $event)
    {
        $admins = User::findByRoleName(Role::ROLE_ADMIN);

        Notification::send($admins, new SponsorNeedsApproval($event->sponsor));
    }
}
