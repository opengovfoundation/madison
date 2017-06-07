<?php

namespace App\Notifications;

use App\Models\Doc as Document;
use App\Models\User;
use App\Notifications\Messages\MailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SupportVoteChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public $oldValue;
    public $newValue;
    public $document;
    public $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($oldValue, $newValue, Document $document, User $user)
    {
        $this->oldValue = $oldValue;
        $this->newValue = $newValue;
        $this->document = $document;
        $this->user = $user;
        $this->actionUrl = $document->url;

        $subject = '';

        if ($this->oldValue === null) {
            // then this is a new vote
            if ($this->newValue === true) {
                $subject = static::baseMessageLocation().'.vote_support';
            } else {
                $subject = static::baseMessageLocation().'.vote_oppose';
            }
        } else {
            // they are changing their vote
            if ($this->oldValue === false && $this->newValue === true) {
                $subject = static::baseMessageLocation().'.vote_support_from_oppose';
            } else {
                $subject = static::baseMessageLocation().'.vote_oppose_from_support';
            }
        }

        $args = [
            'document' => $this->document->title,
        ];

        $this->subjectText = trans($subject, $args);
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
                    ->action(trans('messages.notifications.see_document'), $this->actionUrl)
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
            'old_value' => $this->oldValue,
            'new_value' => $this->newValue,
            'document_id' => $this->document->id,
            'user_id' => $this->user->id,
        ];
    }

    public static function getName()
    {
        return 'madison.document.support_vote_changed';
    }

    public static function getType()
    {
        return static::TYPE_SPONSOR;
    }

    public function getInstigator()
    {
        return $this->user;
    }
}
