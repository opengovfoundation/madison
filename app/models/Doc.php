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
		switch($format){
			case 'html' :
				$path = 'html';
				$ext = '.html';
				break;

			case 'markdown':
			default:
				$path = 'md';
				$ext = '.md';
		}


		$filename = $this->slug . $ext;
		$path = join(DIRECTORY_SEPARATOR, array(storage_path(), 'docs', $path, $filename));

		return $path;
	}

	public function store_content($doc, $doc_content){
		File::put($this->get_file_path('markdown'), $doc_content->content);

		File::put($this->get_file_path('html'),
			Markdown::render($doc_content->content)
		);
	}

	public function get_content($format = null){
		$path = $this->get_file_path($format);

		try {
			return File::get($path);
		}
		catch (Illuminate\Filesystem\FileNotFoundException $e){
			$content = DocContent::where('doc_id', '=', $this->attributes['id'])->where('parent_id')->first()->content;

			if($format == 'html'){
				$content = Markdown::render($content);
			}

			return $content;
		}


	}
}

