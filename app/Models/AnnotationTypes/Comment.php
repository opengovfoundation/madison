<?php

namespace App\Models\AnnotationTypes;

use App;
use Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $table = 'annotation_types_comment';
    protected $fillable = ['content'];
    protected $dates = ['deleted_at'];

    public static function boot()
    {
        parent::boot();

        static::updated(function ($comment) {
            $cacheKey = static::contentHtmlCacheKey($comment);
            Cache::forget($cacheKey);
        });
    }

    public function annotation()
    {
        return $this->morphOne('App\Models\Annotation', 'annotation_type');
    }

    public function getContentHtmlAttribute()
    {
        if ($this->content) {
            $cacheKey = static::contentHtmlCacheKey($this);

            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $html = App::make('comment_markdown')->convertToHtml($this->content);
            Cache::forever($cacheKey, $html);

            return $html;
        }

        return null;
    }

    protected static function contentHtmlCacheKey(Comment $comment)
    {
        return $comment->annotation->str_id.'-html';
    }
}
