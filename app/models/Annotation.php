<?php
class Annotation{
	const INDEX = 'annotator';
	const TYPE = 'annotation';

	protected $body;

	//Annotation format described at https://github.com/okfn/annotator/wiki/Annotation-format
	public $id;
	public $annotator_schema_version = 'v1.0';
	public $created;
	public $updated;
	public $text;
	public $quote;
	public $uri;
	public $ranges;
	public $user;
	public $consumer = 'Madison';
	public $tags;
	public $permissions;

	public function __construct($id = null, $source = null){
		$this->id = $id;

		if(isset($source)){
			foreach($source as $key => $value){
				$this->$key = $value;
			}
		}
	}

	public function save($es){
		if(!isset($this->body)){
			throw new Exception('Annotation body not found.  Cannot save.');
		}

		$params = array(
			'index'	=> self::INDEX,
			'type'	=> self::TYPE,
			'body'	=> $this->body
		);

		$results = $es->index($params);
		
		if(!isset($results['ok']) || !$results['ok']){
			throw new Exception("Annotation save error: " . $results['error'] . " | status: " . $results['status']);
		}

		return $results['_id'];
	}

	/**
	*	Accessor Functions
	*/

	public function id($id = null){
		return $this->access('id', $id);
	}

	public function created($created = null){
		return $this->access('created', $created);
	}
	
	public function updated($updated = null){
		return $this->access('updated', $updated);
	}
	
	public function quote($quote = null){
		return $this->access('quote', $quote);
	}

	public function uri($uri = null){
		return $this->access('uri', $uri);
	}
	
	public function ranges($ranges = null){
		return $this->access('ranges', $ranges);
	}

	public function tags($tags = null){
		return $this->access('tags', $tags);
	}
	
	public function permissions($permissions = null){
		return $this->access('permissions', $permissions);
	}

	public function body($body = null){
		return $this->access('body', $body);
	}

	public function text($text = null){
		return $this->access('text', $text);
	}

	public function user($user = null){
		return $this->access('user', $user);
	}

	/**
	*	Class Helper Functions
	**/
	protected function access($attribute, $value){
		if(isset($value)){
			$this->$attribute = $value;
		}else{
			return $this->$attribute;
		}
	}

	public function toString(){

	}

	/**
	*	Class Static Functions
	*/

	public static function find($es, $id){
		if($id === null){
			throw new Exception('Cannot retrieve annotation with null id');
		}

		$params = array(
			'index'	=> self::INDEX,
			'type'	=> self::TYPE,
			'id'	=> $id
		);


		$annotation = $es->get($params);

		return new Annotation($id, $annotation['_source']);
	}

	public static function all($es){
		
		$params = array(
			'index'	=> self::INDEX,
			'type'	=> self::TYPE,
			'body'	=> array(
				'query'	=> array(
					'match_all'	=> array()
				)
			)
		);

		$results = $es->search($params);
		$results = $results['hits']['hits'];
		$annotations = array();
		foreach($results as $annotation){
			$toPush = new Annotation($annotation['_id'], $annotation['_source']);

			array_push($annotations, $toPush);
		}

		return $annotations;
	}

	public static function delete($es, $id){
		$params = array(
			'index'	=> self::INDEX,
			'type'	=> self::TYPE,
			'id'	=> $id
		);

		return $es->delete($params);
	}
}

