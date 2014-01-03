<?php
class Annotation{
	const INDEX = 'annotator';
	const TYPE = 'annotation';

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

		foreach($source as $key => $value){
			$this->$key = $value;
		}
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
}

