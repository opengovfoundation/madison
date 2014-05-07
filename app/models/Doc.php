<?php
use Illuminate\Database\Eloquent\Collection;
class Doc extends Eloquent{
	public static $timestamp = true;
	
	protected $index;
	protected $softDelete = true;

	const TYPE = 'doc';

	const SPONSOR_TYPE_INDIVIDUAL = "individual";
	const SPONSOR_TYPE_GROUP = "group";
	
	public function __construct()
	{
		parent::__construct();

		$this->index = Config::get('elasticsearch.annotationIndex');
	}

	public function dates()
	{
		return $this->hasMany('Date');
	}

	public function userSponsor()
	{
		return $this->belongsToMany('User');
	}
	
	public function groupSponsor()
	{
		return $this->belongsToMany('Group');
	}

	public function statuses()
	{
		return $this->belongsToMany('Status');
	}

	public function categories()
	{
		return $this->belongsToMany('Category');
	}

	public function comments()
	{
		return $this->hasMany('Comment');
	}

	public function getLink()
	{
		return URL::to('doc/' . $this->slug);
	}

	public function content()
	{
		return $this->hasOne('DocContent');
	}

	public function doc_meta()
	{
		return $this->hasMany('DocMeta');
	}

	static public function createEmptyDocument(array $params)
	{
		$defaults = array(
			'content' => "New Document Content",
			'sponsor' => null,
			'sponsorType' => null
		);
		
		$params = array_replace_recursive($defaults, $params);
		
		if(is_null($params['sponsor'])) {
			throw new \Exception("Sponsor Param Required");
		}
		
		$document = new Doc();
		
		DB::transaction(function() use ($document, $params) {
			$document->title = $params['title'];
			$document->save();
				
			switch($params['sponsorType']) {
				case static::SPONSOR_TYPE_INDIVIDUAL:
					$document->userSponsor()->sync(array($params['sponsor']));
					break;
				case static::SPONSOR_TYPE_GROUP:
					$document->groupSponsor()->sync(array($params['sponsor']));
					break;
				default:
					throw new \Exception("Invalid Sponsor Type");
			}
			
			$template = new DocContent();
			$template->doc_id = $document->id;
			$template->content = "New Document Content";
			$template->save();
				
			$document->init_section = $template->id;
			$document->save();
		});
		
		return $document;
	}
	
	public function save(array $options = array())
	{
		if(empty($this->slug)) {
			$this->slug = $this->getSlug();
		}
		
		return parent::save($options);
	}
	
	public function getSlug()
	{
		if(empty($this->title)) {
			throw new Exception("Can't get a slug - empty title");
		}
		
		return str_replace(
					array(' ', '.'),
					array('-', ''),
					strtolower($this->title));
	}
	
	static public function allOwnedBy($userId)
	{
		$rawDocs = DB::select(
			DB::raw(
				"SELECT docs.* FROM
					(SELECT doc_id 
					   FROM doc_group, group_members
					  WHERE doc_group.group_id = group_members.group_id
					    AND group_members.user_id = ?
					UNION ALL
					 SELECT doc_id 
					   FROM doc_user
					  WHERE doc_user.user_id = ?
				    ) DocUnion, docs
				  WHERE docs.id = DocUnion.doc_id
			   GROUP BY docs.id"
			),
			array($userId, $userId)
		);
		
		$results = new Collection();
		
		foreach($rawDocs as $row) {
			$obj = new static();
			
			foreach($row as $key => $val) {
				$obj->$key = $val;
			}
			
			$results->add($obj);
		}
		
		return $results;
	}
	
	public function setActionCount(){
		$es = self::esConnect();

		$params['index'] = $this->index;
		$params['type'] = 'annotation';
		$params['body']['term']['doc'] = (string)$this->id;

		$count = $es->count($params);

		$this->annotationCount = $count['count'];
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
		$es = self::esConnect();

		File::put($this->get_file_path('markdown'), $doc_content->content);

		File::put($this->get_file_path('html'),
			Markdown::render($doc_content->content)
		);

		$body = array(
			'id' => $this->id,
			'content' => $doc_content->content
		);

		$params = array(
			'index'	=> $this->index,
			'type'	=> self::TYPE,
			'id'	=> $this->id,
			'body'	=> $body
		);

		$results = $es->index($params);
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

	public static function search($query){
		$es = self::esConnect();

		$params['index'] = Config::get('elasticsearch.annotationIndex');
		$params['type'] = self::TYPE;
		$params['body']['query']['filtered']['query']['query_string']['query'] = $query;

		return $es->search($params);
	}

	public static function esConnect(){
		$esParams['hosts'] = Config::get('elasticsearch.hosts');
		$es = new Elasticsearch\Client($esParams);

		return $es;
	}
}

