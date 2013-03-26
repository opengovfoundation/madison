<?php
class Note extends Eloquent{
	public static $timestamp = true;
	
	public function doc_content(){
		return $this->belongs_to('Doc_Content', 'section_id');
	}
	
	public function note_children(){
		return $this->has_many('Note', 'parent_id');
	}
	
	public function note_parent(){
		return $this->belongs_to('Note', 'parent_id');
	}
	
	public function user(){
		return $this->belongs_to('User');
	}
}
