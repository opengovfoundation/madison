<?php

namespace App\Notification;

use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class Message
{
    use SerializesModels;

    protected $subject;
    protected $recipients;
    protected $parts;
    protected $preferredContentType = 'text/html';

    public function __construct($subject = null, $body = null, $recipients = null)
    {
        $this->setSubject($subject);
        $this->setBody($body);
        $this->setRecipients($recipients);
    }

    /**
     * Set the subject for this Message.
     *
     * @param string $subject
     *
     * @return Message
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get the subject for this Message.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set the body for this Message.
     *
     * @param string $body
     * @param string $contentType
     * @param string $charset
     *
     * @return Message
     */
    public function setBody($body, $contentType = null, $charset = null)
    {
        return $this->addPart($body, $contentType, $charset);
    }

    /**
     * Get the body for this Message.
     *
     * @param string $contentType
     *
     * @return string
     */
    public function getBody($contentType = null)
    {
        if (empty($contentType)) {
            $contentType = $this->preferredContentType;
        }

        if (!empty($this->parts[$contentType])) {
            return $this->parts[$contentType];
        }

        return '';
    }

    /**
     * Set the recipient(s) for this Message.
     *
     * @param array|User $recipient
     *
     * @return Message
     */
    public function setRecipients($recipient = null)
    {
        if (is_array($recipient)) {
            $this->recipients = $recipient;
        } elseif (is_null($recipient)) {
            $this->recipients = [];
        } elseif ($recipient instanceof Collection) {
            $this->recipients = $recipient->all();
        } elseif ($recipient instanceof User) {
            $this->recipients = [$recipient];
        } else {
            throw new \InvalidArgumentException('Recipients must be users or a collection of users');
        }

        return $this;
    }

    /**
     * Get the recipients for this Message.
     *
     * @return User
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * Add a MimePart to this Message.
     *
     * @param string $body
     * @param string $contentType
     * @param string $charset
     *
     * @return Message
     */
    public function addPart($body, $contentType = null, $charset = null)
    {
        if (empty($contentType)) {
            $contentType = $this->preferredContentType;
        }

        $this->parts[$contentType] = $body;
        return $this;
    }
}
