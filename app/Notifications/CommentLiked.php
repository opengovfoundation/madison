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
    public $parentType;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Annotation $like)
    {
        $this->like = $like;
        $this->actionUrl = $like->annotatable->getLink();

        if ($this->like->annotatable->isNote()) {
            $this->parentType = trans('messages.notifications.comment_type_note');
        } else {
            $this->parentType = trans('messages.notifications.comment_type_comment');
        }

        $this->subjectText = trans(static::baseMessageLocation().'.subject', [
            'name' => $this->like->user->getDisplayName(),
            'comment_type' => $this->parentType,
            'document' => $this->like->rootAnnotatable->title,
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
                    ->action(trans('messages.notifications.see_comment', ['comment_type' => $this->parentType]), $this->actionUrl)
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
            'like_id' => $this->like->id,
            'comment_type' => $this->parentType,
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
