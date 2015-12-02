<?php

use Illuminate\Database\Eloquent\Model;

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 	Organization Model.
 */
class Organization extends Model
{
    public static $timestamp = true;
    protected $dates = ['deleted_at'];

    //Users belonging to this organization
    public function users()
    {
        return $this->has_many('User');
    }
}
