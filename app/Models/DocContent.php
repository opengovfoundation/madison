<?php

namespace App\Models;

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
}
