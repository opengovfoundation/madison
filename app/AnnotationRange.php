<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnnotationRange extends Model
{
    use Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = "annotation_ranges";
    public $incrementing = false;
    protected $fillable = array('start', 'end', 'start_offset', 'end_offset');
    protected $dates = ['deleted_at'];

    public function annotation()
    {
        return $this->belongsTo('DBAnnotation');
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
