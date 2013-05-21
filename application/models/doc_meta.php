<?php
/**
 * 	Document meta model
 */
class Doc_Meta extends Eloquent{
	public static $table = 'doc_meta';
	public static $timestamp = true;
	
	//Document this meta is describing
	public function doc(){
		return $this->belongs_to('Doc');
	}
	
	
}

