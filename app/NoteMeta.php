<?php

use Illuminate\Database\Eloquent\Model;

namespace App;

/**
 * 	Note meta model.
 */
class NoteMeta extends Model
{
    protected $table = 'note_meta';

    const TYPE_USER_ACTION = "user_action";

    public function user()
    {
        return $this->belongsTo('User');
    }
}
