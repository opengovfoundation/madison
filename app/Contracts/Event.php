<?php

namespace App\Contracts;

interface Event
{
    const TYPE_ADMIN = 'admin';
    const TYPE_USER = 'user';

    public static function getName();
    public static function getDescription();
    public static function getType();
}
