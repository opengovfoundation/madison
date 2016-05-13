<?php

namespace App\Notification;

use App\Models\User;
use Illuminate\Queue\SerializesModels;

class Message
{
    use SerializesModels;

    protected $subject;
    protected $recipient;
    protected $parts;
    protected $preferredContentType = 'text/html';

    public function __construct($subject = null, $body = null, User $recipient = null)
    {
        $this->setSubject($subject);
        $this->setBody($body);
        $this->setRecipient($recipient);
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
     * Set the recipient for this Message.
     *
     * @param User $recipient
     *
     * @return Message
     */
    public function setRecipient(User $recipient = null)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Get the recipient for this Message.
     *
     * @return User
     */
    public function getRecipient()
    {
        return $this->recipient;
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
