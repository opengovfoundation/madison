<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnnotationRange extends Model
{
    use SoftDeletes;

    protected $table = "annotation_ranges";
    public $incrementing = false;
    protected $fillable = array('start', 'end', 'start_offset', 'end_offset');
    protected $dates = ['deleted_at'];

    public function annotation()
    {
        return $this->belongsTo('App\Models\DBAnnotation');
    }

    public static function firstByRangeOrNew(array $input)
    {
        $retval = static::where('annotation_id', '=', $input['annotation_id'])
                        ->where('start_offset', '=', $input['start_offset'])
                        ->where('end_offset', '=', $input['end_offset'])
                        ->first();

        if (!$retval) {
            $retval = new static();

            foreach ($input as $key => $val) {
                $retval->$key = $val;
            }
        }

        return $retval;
    }
}
