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

	public function get_file_path($format = 'markdown'){
		// Format is not used yet.

		$markdown_filename = $this->slug . '.md';
		$markdown_path = join(DIRECTORY_SEPARATOR, array(storage_path(), 'docs', 'md', $markdown_filename));

		return $markdown_path;
	}

	public function store_content($doc, $doc_content){
		return File::put($this->get_file_path(), $doc_content->content);
	}

	public function get_content($format = null){
		$path = $this->get_file_path($format);

		try {
			return File::get($path);
		}
		catch (FileNotFoundException $e) {
			return DocContent::where('doc_id', '=', $this->attributes['id'])->where('parent_id')->first()->content;
		}


	}
}

