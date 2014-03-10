<?php

class AnnotationTag extends Eloquent
{
	protected $table = "annotation_tags";
	protected $fillable = array('annotation_id', 'tag');
	
	public function annotation()
	{
		return $this->belongsTo('DBAnnotation');
	}
}