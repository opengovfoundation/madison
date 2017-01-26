<?php

namespace App\Contracts;

interface Notification
{
    const TYPE_ADMIN = 'admin';
    const TYPE_USER = 'user';

    public static function getName();
    public static function getType();
}
