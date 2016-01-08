<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnnotationTag extends Model
{
    use SoftDeletes;

    protected $table = "annotation_tags";
    protected $fillable = array('annotation_id', 'tag');
    protected $dates = ['deleted_at'];

    public function annotation()
    {
        return $this->belongsTo('App\Models\DBAnnotation');
    }
}
