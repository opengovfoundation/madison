<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 	Document date model.
 */
class Date extends Model
{
    //Document this meta is describing
    public function docs()
    {
        return $this->belongsTo('App\Models\Doc');
    }
}
