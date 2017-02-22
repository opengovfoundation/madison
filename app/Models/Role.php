<?php

namespace App\Models;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
    const ROLE_ADMIN = "Admin";
    const ROLE_INDEPENDENT_SPONSOR = "Independent Sponsor";

    protected $fillable = ['name'];

    public static function adminRole()
    {
        return static::where('name', static::ROLE_ADMIN)->first() ?: static::create(['name' => static::ROLE_ADMIN]);
    }
}
