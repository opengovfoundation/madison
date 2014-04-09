<?php

class AnnotationTag extends Eloquent
{
	protected $table = "annotation_tags";
    protected $softDelete = true;
	protected $fillable = array('annotation_id', 'tag');
	
	public function annotation()
	{
		return $this->belongsTo('DBAnnotation');
	}
}