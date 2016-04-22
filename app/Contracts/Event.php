<?php

namespace App\Contracts;

interface Event
{
    public static function getName();
    public static function getDescription();
}
