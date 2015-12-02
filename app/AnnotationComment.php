<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnnotationComment extends Model
{

    protected $table = "annotation_comments";
    public $incrementing = false;
    protected $fillable = array('id', 'user_id', 'annotation_id', 'text');

    protected $dates = ['deleted_at'];

    public function annotation()
    {
        return $this->belongsTo('App\DBAnnotation');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
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
}
