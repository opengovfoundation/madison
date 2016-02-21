<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnnotationComment extends Model
{
    use SoftDeletes;

    protected $table = "annotation_comments";
    public $incrementing = false;
    protected $fillable = array('id', 'user_id', 'annotation_id', 'text');

    protected $dates = ['deleted_at'];

    public function annotation()
    {
        return $this->belongsTo('App\Models\DBAnnotation');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
    * getLink
    *   Created direct link for this AnnotationComment.
    *
    * @param int $doc_id
    *
    * @return URL::to()
    */
    public function getLink($doc_id)
    {
        $slug = \DB::table('docs')->where('id', $doc_id)->pluck('slug');
        return \URL::to('docs/'.$slug.'#annsubcomment_'.$this->id);
    }

    /**
    * getCreatedAtAttribute
    *   reformat the date structure for the datastore #804.
    *
    * @param string $date
    *
    * @return Carbon date
    */
    public function getCreatedAtAttribute($date)
    {
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('r');
    }
}
