<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification as BaseNotification;

abstract class Notification
    extends BaseNotification
    implements \App\Contracts\Notification
{
    public static function baseMessageLocation()
        {
            return 'messages.notifications.'.static::getName();
        }
}
