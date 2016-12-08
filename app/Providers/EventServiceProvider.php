<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\CommentCreated' => [
            'App\Listeners\CommentCreatedNotification',
        ],
        'App\Events\FeedbackSeen' => [
            'App\Listeners\FeedbackSeenNotification',
        ],
        'App\Events\GroupCreated' => [
            'App\Listeners\GroupCreatedNotification',
        ],
        'App\Events\GroupMemberAdded' => [
            'App\Listeners\GroupMemberAddedNotification',
        ],
        'App\Events\UserVerificationStatusChange' => [
            'App\Listeners\UserVerificationStatusChangeNotification',
        ],
        'App\Events\UserVerificationRequest' => [
            'App\Listeners\UserVerificationRequestNotification',
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
