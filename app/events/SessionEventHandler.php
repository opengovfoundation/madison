<?php


use Symfony\Component\HttpKernel\KernelEvents;

class SessionEventHandler
{
    public function onSessionWriteAfter($sessionHandler, $sessionId, $data)
    {
        $connection = $sessionHandler->getConnection();
        $user = Auth::user();

        if ($user && $sessionId) {
            $session = Sessions::find($sessionId);
            $session->user_id = $user->id;
            $payload = $session->payload;
            $payload['user']['email'] = $user->email;
            $session->payload = $payload;

            $session->save();
        }

        // $request = $event->getRequest();
        // $session = $this->getSession();
        // if (null === $session || $request->hasSession()) {
        //     return;
        // }

        // $request->setSession($session);
    }


    public function subscribe($eventManager)
    {
        $eventManager->listen('session.write.after', 'SessionEventHandler@onSessionWriteAfter');
    }
}
