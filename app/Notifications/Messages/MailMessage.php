<?php

namespace App\Notifications\Messages;

use App\Models\NotificationPreference;
use App\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage as BaseMailMessage;

class MailMessage extends BaseMailMessage
{
    public function __construct(Notification $notification, $notifiable)
    {
        $this->viewData['notification'] = $notification;
        $this->viewData['notifiable'] = $notifiable;
        $this->viewData['unsubscribeMarkdown'] = NotificationPreference::getUnsubscribeMarkdown($notification, $notifiable);
    }
}
