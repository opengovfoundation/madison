<?php

namespace App\Notifications;

use App\Models\Sponsor;
use App\Models\User;
use App\Notifications\Messages\MailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class RemovedFromSponsor extends UserMembershipChanged
{
    public $sponsor;
    public $member;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Sponsor $sponsor, User $member, User $instigator)
    {
        parent::__construct($instigator);
        $this->sponsor = $sponsor;
        $this->member = $member;
        $this->actionUrl = route('sponsors.show', $sponsor);
        $this->subjectText = trans(static::baseMessageLocation().'.removed_from_sponsor', [
            'name' => $this->instigator->getDisplayName(),
            'sponsor' => $this->sponsor->display_name,
        ]);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage($this, $notifiable))
                    ->subject($this->subjectText)
                    ->action(trans('messages.notifications.see_sponsor'), $this->actionUrl)
                    ;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'line' => $this->toLine(),
            'name' => static::getName(),
            'sponsor_id' => $this->sponsor->id,
            'member_id' => $this->member->id,
            'instigator_id' => $this->instigator->id,
        ];
    }
}
