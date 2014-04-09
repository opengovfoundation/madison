<?php
/**
 * 	Document meta model
 */
class DocMeta extends Eloquent{
	protected $table = 'doc_meta';

	protected $softDelete = true;
	public static $timestamp = true;
	
	//Document this meta is describing
	public function doc(){
		return $this->belongsTo('Doc');
	}

	public function user(){
		return $this->belongsTo('User');
	}
	
	
}

