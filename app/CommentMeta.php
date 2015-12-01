<?php

namespace App;

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
        return $this->belongsTo('User');
    }

    public function parent()
    {
        return $this->belongsTo('Comment');
    }
}
