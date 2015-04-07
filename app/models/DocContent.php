<?php
class DocContent extends Eloquent
{
    protected $table = 'doc_contents';
    protected $softDelete = true;

    public function doc()
    {
        return $this->belongsTo('Doc');
    }

    public function notes()
    {
        return $this->hasMany('Note', 'section_id');
    }

    public function content_children()
    {
        return $this->hasMany('DocContent', 'parent_id');
    }

    public function content_parent()
    {
        return $this->belongsTo('DocContent', 'parent_id');
    }

    public function html()
    {
        return Markdown::render($this->content);
    }
}
