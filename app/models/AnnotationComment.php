<?php

class AnnotationComment extends Eloquent
{
	protected $table = "annotation_comments";
    protected $softDelete = true;
	public $incrementing = false;
	protected $fillable = array('id', 'user_id', 'annotation_id', 'text');
	
	public function annotation()
	{
		return $this->belongsTo('DBAnnotation');
	}

    public function user(){
        return $this->belongsTo('User');
    }
}