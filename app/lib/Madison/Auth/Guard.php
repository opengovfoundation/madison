<?php namespace Madison\Auth;

class Guard extends \Illuminate\Auth\Guard {
    /**
     * Get a unique identifier for the auth session value.
     *
     * @return string
     */
    public function getName()
    {
        return 'user.id';
    }

    /**
     * Get the name of the cookie used to store the "recaller".
     *
     * @return string
     */
    public function getRecallerName()
    {
        return 'remember';
    }
}
