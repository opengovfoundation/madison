<?php

namespace App\Notifications;

use App\Models\Annotation;
use App\Notifications\Messages\MailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class CommentFlagged extends Notification implements ShouldQueue
{
    use Queueable;

    public $flag;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Annotation $flag)
    {
        $this->flag = $flag;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if ($this->flag->annotatable->isNote()) {
            $parentType = trans('messages.notifications.comment_type_note');
        } else {
            $parentType = trans('messages.notifications.comment_type_comment');
        }

        $url = $this->flag->annotatable->getLink();

        return (new MailMessage($this, $notifiable))
                    ->subject(trans(static::baseMessageLocation().'.subject', [
                        'comment_type' => $parentType,
                        'document' => $this->flag->rootAnnotatable->title,
                    ]))
                    ->action(trans('messages.notifications.see_comment', ['comment_type' => $parentType]), $url)
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
            'name' => static::getName(),
            'flag_id' => $this->flag->id,
        ];
    }

    public static function getName()
    {
        return 'madison.comment.flagged';
    }

    public static function getType()
    {
        return static::TYPE_SPONSOR;
    }

    public function getInstigator()
    {
        return $this->flag->user;
    }
}
