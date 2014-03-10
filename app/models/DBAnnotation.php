<?php

class DBAnnotation extends Eloquent
{
	protected $table = "annotations";
	protected $fillable = array('quote', 'text', 'uri', 'flags', 'likes', 'dislikes');
	public $incrementing = false;
	
	public function comments()
	{
		return $this->hasMany('AnnotationComment', 'annotation_id');
	}
	
	public function tags()
	{
		return $this->hasMany('AnnotationTag', 'annotation_id');
	}
	
	public function permissions()
	{
		return $this->hasMany('AnnotationPermission', 'annotation_id');
	}
}