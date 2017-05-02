<?php

namespace App\Models\AnnotationTypes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hidden extends Model
{
    use SoftDeletes;

    protected $table = 'annotation_types_hidden';
    protected $fillable = [];
    protected $dates = ['deleted_at'];

    public function annotation()
    {
        return $this->morphOne('App\Models\Annotation', 'annotation_type');
    }
}
