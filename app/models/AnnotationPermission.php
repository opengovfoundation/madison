<?php

class AnnotationPermission extends Eloquent
{
	protected $table = "annotation_permissions";
	protected $fillable = array('annotation_id', 'user_id');
	
	public function annotation()
	{
		return $this->belongsTo('DBAnnotation');
	}
}