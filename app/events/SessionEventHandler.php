<?php


use Symfony\Component\HttpKernel\KernelEvents;

class SessionEventHandler
{
    public function onSessionWriteAfter($sessionHandler, $sessionId, $data)
    {
        // var_dump($sessionId, $data); die();

        $connection = $sessionHandler->getConnection();
        $user = Auth::user();

        if ($user && $sessionId) {
            $userId = $user->id;
            $updateStatement = $connection->prepare(
                "UPDATE $sessionHandler->table SET user_id = :user_id WHERE $sessionHandler->idCol = :id"
            );
            $updateStatement->bindParam(':id', $sessionId, \PDO::PARAM_STR);
            $updateStatement->bindParam(':user_id', $userId, \PDO::PARAM_INT);
            $updateStatement->execute();
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
