<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use GrahamCampbell\Markdown\Facades\Markdown;

class DocContent extends Model
{
    use SoftDeletes;

    protected $table = 'doc_contents';
    protected $dates = ['deleted_at'];

    public function doc()
    {
        return $this->belongsTo('App\Doc');
    }

    public function notes()
    {
        return $this->hasMany('App\Note', 'section_id');
    }

    public function content_children()
    {
        return $this->hasMany('App\DocContent', 'parent_id');
    }

    public function content_parent()
    {
        return $this->belongsTo('App\DocContent', 'parent_id');
    }

    public function html()
    {
        return Markdown::convertToHtml($this->content);
    }
}
