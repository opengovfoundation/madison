<?php

namespace App\Policies;

class Policy
{
    public function before($user, $ability)
    {
       if ($user->isAdmin()) {
           return true;
       }
    }
}
