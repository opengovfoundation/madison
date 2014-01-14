<?php
/**
 * 	Note meta model
 */
class NoteMeta extends Eloquent{
	protected $table = 'note_meta';
	
	public function user(){
		return $this->belongs_to('User');
	}
	
}

