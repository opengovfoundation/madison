<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 	Comment meta model.
 */
class CommentMeta extends Model
{
    protected $table = 'comment_meta';

    const TYPE_USER_ACTION = "user_action";

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\Comment');
    }
}
