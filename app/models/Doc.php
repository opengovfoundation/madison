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
		return $this->has_many('DocMeta');
	}
	
	public function get_root_content(){
		return DocContent::where('doc_id', '=', $this->id)->where('parent_id', 'IS', DB::raw('NULL'))->get();
	}
}

