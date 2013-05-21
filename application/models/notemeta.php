<?php
/**
 * 	Note meta model
 */
class NoteMeta extends Eloquent{
	public static $table = 'note_meta';
	public static $timestamp = true;
	
	//Note this meta is describing
	public function note(){
		return $this->belongs_to('Note');
	}
	
	public function user(){
		return $this->belongs_to('User');
	}
	
}

