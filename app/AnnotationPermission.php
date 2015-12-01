<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnnotationPermission extends Model
{
    use Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = "annotation_permissions";
    protected $fillable = array('annotation_id', 'user_id');
    protected $dates = ['deleted_at'];

    public function annotation()
    {
        return $this->belongsTo('DBAnnotation');
    }
}
