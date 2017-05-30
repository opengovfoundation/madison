<?php

namespace App\Listeners;

use App\Events\DocumentPublished;
use App\Mail\SponsorOnboarding;
use App\Models\User;
use App\Notifications;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Mail;
use Notification;

class DocumentPublishedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  SponsorCreated  $event
     * @return void
     */
    public function handle(DocumentPublished $event)
    {
        $users = User::whereHas('notificationPreferences', function ($query) {
            $query->where('event', Notifications\DocumentPublished::getName());
        })->get();

        Notification::send($users, new Notifications\DocumentPublished($event->document, $event->instigator));

        $members = $event->document->sponsors
            ->flatMap(function ($sponsor) {
                return $sponsor->members->pluck('user');
            })
            ->unique('id');
        foreach ($members as $member) {
            Mail::to($member)
                ->send(new SponsorOnboarding\Engage($member));
        }
    }
}
