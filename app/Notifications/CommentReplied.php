<?php

namespace App\Notifications;

use App\Models\Annotation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CommentReplied extends Notification implements ShouldQueue
{
    use Queueable;

    public $comment;
    public $parent;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Annotation $comment, Annotation $parent)
    {
        $this->comment = $comment;
        $this->parent = $parent;
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
        if ($this->parent->isNote()) {
            $parentType = trans('messages.notifications.comment_type_note');
        } else {
            $parentType = trans('messages.notifications.comment_type_comment');
        }

        $url = $this->comment->getLink();

        return (new MailMessage)
                    ->subject(trans(static::baseMessageLocation().'.subject', [
                        'name' => $this->comment->user->getDisplayName(),
                        'comment_type' => $parentType,
                        'document' => $this->comment->rootAnnotatable->title,
                    ]))
                    ->action(trans('messages.notifications.see_comment'), $url)
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
            'parent_id' => $this->parent->id,
            'comment_id' => $this->comment->id,
        ];
    }

    public static function getName()
    {
        return 'madison.comment.replied';
    }

    public static function getType()
    {
        return static::TYPE_USER;
    }

    public function getInstigator()
    {
        return $this->comment->user;
    }
}
