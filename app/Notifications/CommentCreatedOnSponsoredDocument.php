<?php

namespace App\Notifications;

use App\Models\Annotation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CommentCreatedOnSponsoredDocument extends Notification implements ShouldQueue
{
    use Queueable;

    public $comment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Annotation $comment)
    {
        $this->comment = $comment;
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
        if ($this->comment->isNote()) {
            $commentType = trans('messages.notifications.comment_type_note');
        } else {
            $commentType = trans('messages.notifications.comment_type_comment');
        }

        $url = $this->comment->getLink();

        return (new MailMessage)
                    ->subject(trans(static::baseMessageLocation().'.subject', [
                        'name' => $this->comment->user->getDisplayName(),
                        'comment_type' => $commentType,
                        'document' => $this->comment->rootAnnotatable->title,
                    ]))
                    ->action(trans('messages.notifications.see_comment', ['comment_type' => $commentType]), $url)
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
            'comment_id' => $this->comment->id,
        ];
    }

    public static function getName()
    {
        return 'madison.comment.created_on_sponsored';
    }

    public static function getType()
    {
        return static::TYPE_SPONSOR;
    }

    public function getInstigator()
    {
        return $this->comment->user;
    }
}
