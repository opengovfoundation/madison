<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 	User meta model.
 */
class UserMeta extends Model
{
    protected $table = 'user_meta';

    protected $fillable = array('user_id', 'meta_key');

    const TYPE_SEEN_ANNOTATION_THANKS = "seen_annotation_thanks";
    const TYPE_INDEPENDENT_SPONSOR = "independent_sponsor";

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
