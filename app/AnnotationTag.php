<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnnotationTag extends Model
{
    use Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = "annotation_tags";
    protected $fillable = array('annotation_id', 'tag');
    protected $dates = ['deleted_at'];

    public function annotation()
    {
        return $this->belongsTo('DBAnnotation');
    }
}
