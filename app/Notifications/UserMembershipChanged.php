<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

abstract class UserMembershipChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public $instigator;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $instigator)
    {
        $this->instigator = $instigator;
    }


    public static function getName()
    {
        return 'madison.user.sponsor_membership_changed';
    }

    public static function getType()
    {
        return static::TYPE_USER;
    }

    public function getInstigator()
    {
        return $this->instigator;
    }
}
