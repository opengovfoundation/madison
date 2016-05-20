<?php

namespace App\Notification;

use App\Events\Event;
use App\Notification\Message;
use App\Models\NotificationPreference;
use App\Models\Role;
use App\Models\User;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use SuperClosure\Serializer;
use InvalidArgumentException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Queue\Queue as QueueContract;

class Notifier
{
    /**
     * The view factory instance.
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $views;

    /**
     * The Mailer instance.
     *
     * @var \Illuminate\Mail\Mailer
     */
    protected $mailer;

    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher|null
     */
    protected $events;

    /**
     * The IoC container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The queue implementation.
     *
     * @var \Illuminate\Contracts\Queue\Queue
     */
    protected $queue;

    /**
     * Create a new Mailer instance.
     *
     * @param  \Illuminate\Contracts\View\Factory  $views
     * @param  \Illuminate\Mail\Mailer  $mailer
     * @param  \Illuminate\Contracts\Events\Dispatcher|null  $events
     * @return void
     */
    public function __construct(Factory $views, \Illuminate\Mail\Mailer $mailer, Dispatcher $events = null)
    {
        $this->views = $views;
        $this->mailer = $mailer;
        $this->events = $events;
    }

    /**
     * Send a new message when only a raw text part.
     *
     * @param  string  $text
     * @param  mixed  $callback
     * @param  Event  $event
     * @return int
     */
    public function raw($text, $callback, Event $event)
    {
        return $this->send(['raw' => $text], [], $callback, $event);
    }

    /**
     * Send a new message when only a plain part.
     *
     * @param  string  $view
     * @param  array  $data
     * @param  mixed  $callback
     * @param  Event  $event
     * @return int
     */
    public function plain($view, array $data, $callback, Event $event)
    {
        return $this->send(['text' => $view], $data, $callback, $event);
    }

    /**
     * Send a new message using a view.
     *
     * @param  string|array  $view
     * @param  array  $data
     * @param  \Closure|string  $callback
     * @param  Event  $event
     * @return void
     */
    public function send($view, array $data, $callback, Event $event)
    {
        // First we need to parse the view, which could either be a string or
        // an array containing both an HTML and plain text versions of the
        // view which should be used when sending an message. We will extract
        // both of them out here.
        list($view, $plain, $raw) = $this->parseView($view);

        if ($callback instanceof Message) {
            $data['message'] = $message = $callback;
        } else {
            $data['message'] = $message = $this->createMessage();
        }

        // Once we have retrieved the view content for the message we will set
        // the body of this message using the HTML type, which will provide a
        // simple wrapper to creating view based emails that are able to
        // receive arrays of data.
        $this->addContent($message, $view, $plain, $raw, $data);

        $this->callMessageBuilder($callback, $message);

        // determine if the recipient wants notifications from this kind of event
        $recipientNotificationPreference = [];
        foreach ($message->getRecipients() as $recipient) {
            // if the event is not in the set of valid notifications for the
            // recipient, then skip it
            if (empty(NotificationPreference::getValidNotificationsForUser($recipient)[$event::getName()])) {
                continue;
            }

            $recipientNotificationPreference[$recipient->id] = NotificationPreference
                ::where('user_id', $recipient->id)
                ->where('event', $event::getName())
                ->get();
        }

        if (empty($recipientNotificationPreference)) {
            // they don't want notifications for this event type
            return;
        }

        return $this->sendMessage($message, $recipientNotificationPreference);
    }

    /**
     * Queue a new message for sending.
     *
     * @param  string|array  $view
     * @param  array  $data
     * @param  \Closure|string  $callback
     * @param  Event  $event
     * @param  string|null  $queue
     * @return mixed
     */
    public function queue($view, array $data, $callback, Event $event, $queue = null)
    {
        $callback = $this->buildQueueCallable($callback);
        $event = serialize($event);

        return $this->queue->push('notifier@handleQueuedMessage', compact('view', 'data', 'callback', 'event'), $queue);
    }

    /**
     * Queue a new message for sending on the given queue.
     *
     * @param  string  $queue
     * @param  string|array  $view
     * @param  array  $data
     * @param  \Closure|string  $callback
     * @param  Event  $event
     * @return mixed
     */
    public function onQueue($queue, $view, array $data, $callback, Event $event)
    {
        return $this->queue($view, $data, $callback, $event, $queue);
    }

    /**
     * Queue a new message for sending on the given queue.
     *
     * This method didn't match rest of framework's "onQueue" phrasing. Added "onQueue".
     *
     * @param  string  $queue
     * @param  string|array  $view
     * @param  array  $data
     * @param  \Closure|string  $callback
     * @param  Event  $event
     * @return mixed
     */
    public function queueOn($queue, $view, array $data, $callback, Event $event)
    {
        return $this->onQueue($queue, $view, $data, $callback, $event);
    }

    /**
     * Queue a new message for sending after (n) seconds.
     *
     * @param  int  $delay
     * @param  string|array  $view
     * @param  array  $data
     * @param  \Closure|string  $callback
     * @param  Event  $event
     * @param  string|null  $queue
     * @return mixed
     */
    public function later($delay, $view, array $data, $callback, Event $event, $queue = null)
    {
        $callback = $this->buildQueueCallable($callback);
        $event = serialize($event);

        return $this->queue->later($delay, 'notifier@handleQueuedMessage', compact('view', 'data', 'callback', 'event'), $queue);
    }

    /**
     * Queue a new message for sending after (n) seconds on the given queue.
     *
     * @param  string  $queue
     * @param  int  $delay
     * @param  string|array  $view
     * @param  array  $data
     * @param  \Closure|string  $callback
     * @param  Event  $event
     * @return mixed
     */
    public function laterOn($queue, $delay, $view, array $data, $callback, Event $event)
    {
        return $this->later($delay, $view, $data, $callback, $queue, $event);
    }

    /**
     * Build the callable for a queued e-mail job.
     *
     * @param  mixed  $callback
     * @return mixed
     */
    protected function buildQueueCallable($callback)
    {
        if ($callback instanceof Message) {
            return serialize($callback);
        }

        if ($callback instanceof Closure) {
            return (new Serializer)->serialize($callback);
        }

        return $callback;
    }

    /**
     * Handle a queued message job.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  array  $data
     * @return void
     */
    public function handleQueuedMessage($job, $data)
    {
        $this->send($data['view'], $data['data'], $this->getQueuedCallable($data), unserialize($data['event']));

        $job->delete();
    }

    /**
     * Get the true callable for a queued message.
     *
     * @param  array  $data
     * @return mixed
     */
    protected function getQueuedCallable(array $data)
    {
        if (Str::contains($data['callback'], 'Notification\\Message')) {
            return unserialize($data['callback']);
        }

        if (Str::contains($data['callback'], 'SerializableClosure')) {
            return unserialize($data['callback'])->getClosure();
        }

        return $data['callback'];
    }

    /**
     * Add the content to a given message.
     *
     * @param  \Illuminate\Mail\Message  $message
     * @param  string  $view
     * @param  string  $plain
     * @param  string  $raw
     * @param  array  $data
     * @return void
     */
    protected function addContent($message, $view, $plain, $raw, $data)
    {
        if (isset($view)) {
            $message->setBody($this->getView($view, $data), 'text/html');
        }

        if (isset($plain)) {
            $method = isset($view) ? 'addPart' : 'setBody';

            $message->$method($this->getView($plain, $data), 'text/plain');
        }

        if (isset($raw)) {
            $method = (isset($view) || isset($plain)) ? 'addPart' : 'setBody';

            $message->$method($raw, 'text/plain');
        }
    }

    /**
     * Parse the given view name or array.
     *
     * @param  string|array  $view
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function parseView($view)
    {
        if (is_string($view)) {
            return [$view, null, null];
        }

        // If the given view is an array with numeric keys, we will just assume that
        // both a "pretty" and "plain" view were provided, so we will return this
        // array as is, since must should contain both views with numeric keys.
        if (is_array($view) && isset($view[0])) {
            return [$view[0], $view[1], null];
        }

        // If the view is an array, but doesn't contain numeric keys, we will assume
        // the the views are being explicitly specified and will extract them via
        // named keys instead, allowing the developers to use one or the other.
        if (is_array($view)) {
            return [
                Arr::get($view, 'html'),
                Arr::get($view, 'text'),
                Arr::get($view, 'raw'),
            ];
        }

        throw new InvalidArgumentException('Invalid view.');
    }

    /**
     * Send a Message instance.
     *
     * @param  \App\Notification\Message  $message
     * @param  Collection                 $recipientNotificationPreference
     * @return void
     */
    protected function sendMessage($message, $recipientNotificationPreference)
    {
        if ($this->events) {
            $this->events->fire('notifier.sending', [$message]);
        }

        foreach ($recipientNotificationPreference as $userId => $prefs) {
            foreach ($prefs as $pref) {
                switch ($pref->type) {
                    case NotificationPreference::TYPE_EMAIL:
                        $recipient = User::find($userId);

                        // if the email is not verified, don't send a message to
                        // it regardless of if the user has it selected
                        if (!empty($recipient->token) || empty($recipient->email)) {
                            continue;
                        }

                        $this->mailer->raw($message->getBody(), function ($swiftMessage) use ($message, $recipient) {
                            $swiftMessage->setContentType('text/html');
                            $swiftMessage->subject($message->getSubject());
                            $swiftMessage->from('sayhello@opengovfoundation.org', 'Madison');
                            $swiftMessage->to($recipient->email);
                        });
                        break;
                    case NotificationPreference::TYPE_TEXT:
                        // unsupported
                    default:
                        // do nothing
                }
            }
        }
    }

    /**
     * Call the provided message builder.
     *
     * @param  \Closure|string  $callback
     * @param  \App\Notification\Message  $message
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function callMessageBuilder($callback, $message)
    {
        if ($callback instanceof Message) {
            return $callback;
        }

        if ($callback instanceof Closure) {
            return call_user_func($callback, $message);
        }

        throw new InvalidArgumentException('Callback is not valid.');
    }

    /**
     * Create a new message instance.
     *
     * @return \App\Notification\Message
     */
    protected function createMessage()
    {
        $message = new Message();

        return $message;
    }

    /**
     * Render the given view.
     *
     * @param  string  $view
     * @param  array  $data
     * @return \Illuminate\View\View
     */
    protected function getView($view, $data)
    {
        return $this->views->make($view, $data)->render();
    }

    /**
     * Get the view factory instance.
     *
     * @return \Illuminate\Contracts\View\Factory
     */
    public function getViewFactory()
    {
        return $this->views;
    }

    /**
     * Get the Mailer instance.
     *
     * @return \Illuminate\Mail\Mailer
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * Get the array of failed recipients.
     *
     * @return array
     */
    public function failures()
    {
        return $this->failedRecipients;
    }

    /**
     * Set the Mailer instance.
     *
     * @param  \Illuminate\Mail\Mailer  $mailer
     * @return void
     */
    public function setMailer($mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Set the queue manager instance.
     *
     * @param  \Illuminate\Contracts\Queue\Queue  $queue
     * @return $this
     */
    public function setQueue(QueueContract $queue)
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * Set the IoC container instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return void
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }
}
