<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *  Document meta model.
 */
class DocMeta extends Model
{
    use SoftDeletes;

    protected $table = 'doc_meta';

    public static $timestamp = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['meta_value'];

    //Document this meta is describing
    public function doc()
    {
        return $this->belongsTo('App\Models\Doc');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
