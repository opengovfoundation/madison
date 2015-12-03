<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * 	Document meta model.
 */
class Status extends Model
{
    //Document this meta is describing
    public function docs()
    {
        return $this->belongsToMany('App\Doc');
    }
}
