<?php
class Doc_Meta extends Eloquent{
	public static $table = 'doc_meta';
	public static $timestamp = true;
	
	public function doc(){
		return $this->belongs_to('Doc');
	}
	
	
}

