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
	public $consumer;
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
		
		return $results['_id'];
	}

	public function setBody($body){
		$this->body = $body;
	}

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

