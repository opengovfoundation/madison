<?php

namespace App\Contracts;

interface Notification
{
    const TYPE_ADMIN = 'admin';
    const TYPE_USER = 'user';

    public static function getName();
    public static function getType();

    /**
     * Returns the user that did the thing that caused this notification to be
     * sent. Used for filtering out notifications to users for their own
     * actions.
     *
     * @returns \App\Models\User
     */
    public function getInstigator();
}
