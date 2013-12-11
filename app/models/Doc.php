<?php
class Doc extends Eloquent{
	public static $timestamp = true;

	public function getLink(){
		return URL::to('doc/' . $this->slug);
	}

	public function content(){
		return $this->hasOne('DocContent');
	}

	public function doc_meta(){
		return $this->hasMany('DocMeta');
	}

	public function get_root_content(){
		$root_content = DocContent::where('doc_id', '=', $this->attributes['id'])->where('parent_id')->get();

		return $root_content;
	}
}

