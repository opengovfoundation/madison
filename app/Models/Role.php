<?php

namespace App\Models;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
    const ROLE_ADMIN = "Admin";
    const ROLE_INDEPENDENT_SPONSOR = "Independent Sponsor";

    protected $fillable = ['name'];
}
