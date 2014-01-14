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
	public $user_action = null;

	public function __construct($id = null, $source = null){
		$this->id = $id;

		if(isset($source)){
			foreach($source as $key => $value){
				$this->$key = $value;
			}
		}
	}

	public function setUserAction($user_id){
		$meta = NoteMeta::where('user_id', $user_id)->where('note_id', '=', $this->id)->where('meta_key', '=', 'user_action');

		if($meta->count() == 1){
			$this->user_action = $meta->first()->meta_value;
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

	public function likes($likes = null){
		$likes = NoteMeta::where('note_id', $this->id)->where('meta_key', '=', 'user_action')->where('meta_value', '=', 'like')->count();

		return $likes;
	}

	public function dislikes($disliked = null){
		$dislikes = NoteMeta::where('note_id', $this->id)->where('meta_key', '=', 'user_action')->where('meta_value', '=', 'dislike')->count();

		return $dislikes;
	}

	public function flags($flags = null){
		$flags = NoteMeta::where('note_id', $this->id)->where('meta_key', '=', 'user_action')->where('meta_value', '=', 'flag')->count();

		return $flags;
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

	public static function findWithActions($es, $id, $userid){
		if($id === null){
			throw new Exception('Cannot retrieve annotation with null id');
		}

		$params = array(
			'index'	=> self::INDEX,
			'type'	=> self::TYPE,
			'id'	=> $id
		);


		$annotation = $es->get($params);

		$annotation = new Annotation($id, $annotation['_source']);
		$annotation->setUserAction($userid);
		
		return $annotation;
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

	public static function allWithActions($es, $userid){
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
			$toPush->setUserAction($userid);

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

		$result = $es->delete($params);
		
		if($result['ok'] == true){
			$metas = NoteMeta::where('note_id', $id);
			$metas->delete();
		}
		
		return $result;
	}

	public static function getMetaCount($es, $id, $action){
		if($id === null){
			App::abort(404, 'No note id passed');
		}

		$annotation = Annotation::find($es, $id);

		$action_count = $annotation->$action();

		return $action_count;
	}

	public static function addUserAction($es, $note_id, $user_id, $action){
		if($note_id == null || $user_id == null || $action == null){
			throw new Exception('Unable to add user action.');
		}

		$toReturn = array(
		                  'action' 		=> null,
		                  'likes'		=> -1,
		                  'dislikes'	=> -1,
		                  'flags'		=> -1
		            );

		$annotation = Annotation::find($es, $note_id);

		$meta = NoteMeta::where('user_id', $user_id)->where('note_id', '=', $note_id)->where('meta_key', '=', 'user_action');

		//This user has no actions on this annotation
		if($meta->count() == 0){
			$meta = new NoteMeta();
			$meta->user_id = Auth::user()->id;
			$meta->note_id = $note_id;
			$meta->meta_key = 'user_action';
			$meta->meta_value = $action;

			$meta->save();

			$toReturn['action'] = true;
		}elseif($meta->count() == 1){
			$meta = $meta->first();

			//This user has already done this action.  Removing the action
			if($meta->meta_value == $action){
				$meta->delete();

				$toReturn['action'] = false;
			}else{
				$meta->meta_value = $action;	
				$meta->save();

				$toReturn['action'] = true;
			}
		}else{
			throw new Exception('Multiple user actions were found');
		}

		$toReturn['likes'] = $annotation->likes();
		$toReturn['dislikes'] = $annotation->dislikes();
		$toReturn['flags'] = $annotation->flags();

		return $toReturn;
	}
}

