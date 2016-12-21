<?php

namespace App\Models;

use Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use GrahamCampbell\Markdown\Facades\Markdown;

class DocContent extends Model
{
    use SoftDeletes;

    protected $table = 'doc_contents';
    protected $dates = ['deleted_at'];

    public static function boot()
    {
        parent::boot();

        static::updated(function ($content) {
            // Invalidate the cache
            $format = 'html';
            $cacheKey = static::cacheKey($content, $format);
            Cache::forget($cacheKey);
        });
    }

    public function doc()
    {
        return $this->belongsTo('App\Models\Doc');
    }

    public function notes()
    {
        return $this->hasMany('App\Models\Note', 'section_id');
    }

    public function content_children()
    {
        return $this->hasMany('App\Models\DocContent', 'parent_id');
    }

    public function content_parent()
    {
        return $this->belongsTo('App\Models\DocContent', 'parent_id');
    }

    public function html()
    {
        return Markdown::convertToHtml($this->content);
    }

    public function rendered($format = 'html')
    {
        $cacheKey = static::cacheKey($this, $format);

        if ($format === 'html' && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $content = '';

        switch ($format) {
            case 'html':
                $content = $this->html();
                break;
            case 'raw':
                $content = $this->content;
                break;
        }

        if ($format === 'html') {
            Cache::forever($cacheKey, $content);
        }

        return $content;
    }

    protected static function cacheKey(DocContent $content, $format)
    {
        return 'doc-'.$content->doc->id.'-'.$content->page.'-'.$format;
    }
}
