<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 	Document meta model.
 */
class DocMeta extends Model
{
    protected $table = 'doc_meta';

    public static $timestamp = true;
    protected $dates = ['deleted_at'];

    //Document this meta is describing
    public function doc()
    {
        return $this->belongsTo('Doc');
    }

    public function user()
    {
        return $this->belongsTo('User');
    }
}
