<?php
class Doc extends Eloquent{
	public static $timestamp = true;
	
	public function getLink(){
		return URL::to('doc/' . $this->slug);
	}
	
	public function content(){
		return $this->has_many('DocContent');
	}
	
	public function doc_meta(){
		return $this->has_many('Doc_Meta');
	}
	
	public function get_root_content(){
		return DocContent::where('doc_id', '=', $this->id)->where('parent_id', 'IS', DB::raw('NULL'))->get();
	}
	
	//This should be set as a secondary relationship for eager loading
	public function get_all_comments(){
		$comments = DB::table('notes')
			->left_join('doc_contents', 'notes.section_id', '=', 'doc_contents.id')
			->where_type('comment')
			->order_by('likes')
			->get(array('notes.id', 'section_id', 'notes.content', 'likes', 'dislikes'));
		return $comments;
	}
	
	//This should be set as a secondary relationship for eager loading
	public function get_all_suggestions(){
		$suggestions = DB::table('notes')
			->left_join('doc_contents', 'notes.section_id', '=', 'doc_contents.id')
			->where_type('suggestion')
			->order_by('likes')
			->get(array('notes.id', 'section_id', 'notes.content', 'likes', 'dislikes'));
		return $suggestions;
	}
}

