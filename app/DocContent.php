<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocContent extends Model
{
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
        return Markdown::render($this->content);
    }
}
