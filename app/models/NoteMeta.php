<?php
/**
 * 	Note meta model
 */
class NoteMeta extends Eloquent{
	protected $table = 'note_meta';
	
	//Note this meta is describing
	public function note(){
		return $this->belongs_to('Note');
	}
	
	public function user(){
		return $this->belongs_to('User');
	}
	
}

