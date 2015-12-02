<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * 	Note meta model.
 */
class NoteMeta extends Model
{
    protected $table = 'note_meta';

    const TYPE_USER_ACTION = "user_action";

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
