<?php

use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 	Organization Model.
 */
class Organization extends Model
{
    use SoftDeletes;

    public static $timestamp = true;
    protected $dates = ['deleted_at'];

    //Users belonging to this organization
    public function users()
    {
        return $this->has_many('User');
    }
}
