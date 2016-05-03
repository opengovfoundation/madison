<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnnotationPermission extends Model
{
    use SoftDeletes;

    protected $table = "annotation_permissions";
    protected $fillable = array('annotation_id', 'user_id');
    protected $dates = ['deleted_at'];

    public function annotation()
    {
        return $this->belongsTo('App\Models\Annotation');
    }
}
