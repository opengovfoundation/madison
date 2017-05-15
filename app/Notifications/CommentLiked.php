<?php

namespace App\Notifications;

use App\Models\Annotation;
use App\Notifications\Messages\MailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class CommentLiked extends Notification implements ShouldQueue
{
    use Queueable;

    public $like;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Annotation $like)
    {
        $this->like = $like;
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
        if ($this->like->annotatable->isNote()) {
            $parentType = trans('messages.notifications.comment_type_note');
        } else {
            $parentType = trans('messages.notifications.comment_type_comment');
        }

        $url = $this->like->annotatable->getLink();

        return (new MailMessage($this, $notifiable))
                    ->subject(trans(static::baseMessageLocation().'.subject', [
                        'name' => $this->like->user->getDisplayName(),
                        'comment_type' => $parentType,
                        'document' => $this->like->rootAnnotatable->title,
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
            'like_id' => $this->like->id,
        ];
    }

    public static function getName()
    {
        return 'madison.comment.liked';
    }

    public static function getType()
    {
        return static::TYPE_USER;
    }

    public function getInstigator()
    {
        return $this->like->user;
    }
}
