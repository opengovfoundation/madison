<?php

class DBAnnotation extends Eloquent
{
	protected $table = "annotations";
	protected $fillable = array('quote', 'text', 'uri', 'flags', 'likes', 'dislikes');
	public $incrementing = false;
	
	public function comments()
	{
		return $this->hasMany('AnnotationComment', 'annotation_id');
	}
	
	public function tags()
	{
		return $this->hasMany('AnnotationTag', 'annotation_id');
	}
	
	public function permissions()
	{
		return $this->hasMany('AnnotationPermission', 'annotation_id');
	}
	
	static public function loadAnnotationsForAnnotator($docId, $annotationId = null, $userId = null)
	{
		if(is_null($userId)) {
			if(is_null($annotationId)) {
				$annotations = static::where('doc', '=', $docId)->get();
			} else {
				$annotations = static::where('annotation_id', '=', $annotationId)->get();
			}
		} else {
			if(!is_null($annotationId)) {
				$annotations = array(static::firstWithActions($annotationId, $userId));
			} else {
				$annotations = static::where('doc', '=', $docId)->get();
			}
		}
		
		$retval = array();
		foreach($annotations as $annotation) {
			
			$item = $annotation->toArray();
			$item['created'] = $item['created_at'];
			$item['updated'] = $item['updated_at'];
			$item['annotator_schema_version'] = 'v1.0';
			$item['ranges'] = array();
			$item['tags'] = array();
			$item['comments'] = array();
			$item['permissions'] = array(
				'read' => array(),
				'update' => array(),
				'delete' => array(),
				'admin' => array()
			);
			
			$ranges = AnnotationRange::where('annotation_id', '=', $item['id'])->get();
			
			foreach($ranges as $range) {
				$item['ranges'][] = array(
					'start' => $range['start'],
					'end' => $range['end'],
					'startOffset' => $range['start_offset'],
					'endOffset' => $range['end_offset']
				);
			}
			
			if(!is_null($userId)) {
				$user = User::where('id', '=', $userId)->first();
				$item['user'] = array_intersect_key($user->toArray(), array_flip(array('id', 'email', 'user_level')));
				$item['user']['name'] = $user->fname . ' ' . $user->lname{0};
			}
			
			$item['consumer'] = "Madison";
			
			$tags = AnnotationTag::where('annotation_id', '=', $item['id'])->get();
		
			foreach($tags as $tag) {
				$items['tags'][] = $tag->tag;
			}
			
			$permissions = AnnotationPermission::where('annotation_id', '=', $item['id'])->get();
			
			foreach($permissions as $perm) {
				if($perm->read) {
					$item['permissions']['read'][] = $perm['user_id'];
				}
				
				if($perm->update) {
					$item['permissions']['update'][] = $perm['user_id'];
				}
				
				if($perm->delete) {
					$item['permissions']['delete'][] = $perm['user_id'];
				}
				
				if($perm->admin) {
					$item['permissions']['admin'][] = $perm['user_id'];
				}
			}
			
			$item = array_intersect_key($item, array_flip(array(
				'id', 'annotator_schema_version', 'created', 'updated',
				'text', 'quote', 'uri', 'ranges', 'user', 'consumer', 'tags',
				'permissions', 'likes', 'dislikes', 'flags', 'comments', 'user_action',
				'doc'
			)));
			
			$retval[] = $item;
		}
		
		return $retval;
	}
	
	static public function firstWithActions($annotationId, $userId)
	{
		$retval = static::where('id', '=', $annotationId)->first();
		$retval->setUserAction($userId);
		$retval->setActionCounts();
		
		return $retval;
	}
	
	public function addComment(array $comment) {
		$obj = new AnnotationComment();
		$obj->text = $comment['text'];
		$obj->user_id = $comment['user']['id'];
		$obj->id = $comment['id'];
		$obj->annotation_id = $this->id;
		return $obj->save();
	}
	
	static public function allByDocId($docId)
	{
		$retval = static::where('doc_id', '=', $docId);
		
		foreach($retval as $annotation) {
			$annotation->setActionCounts();
		}
		
		return $retval;
	}
	
	static public function allWithActions($docId, $userId) {
		
		$retval = static::where('doc_id', '=', $docId);
		
		foreach($retval as $annotation) {
			$annotation->setUserAction($userId);
			$annotation->setActionCounts();
		}
		
		return $retval;
	} 
	
	static public function getMetaCount($id, $action) 
	{
		$annotation = static::where('annotation_id', '=', $id);
		
		$actionCount = $annotation->$action();
		
		return $actionCount;
	}
	
	static public function addUserAction($annotationId, $userId, $action) 
	{
		$retval = array(
			'action' => null,
			'likes' => -1,
			'dislikes' => -1,
			'flags' => -1
		);
		
		$annotation = static::where('annotation_id', '=', $annotationId);
		
		$meta = NoteMeta::where('user_id', $userId)
		                ->where('note_id', '=', $annotationId)
		                ->where('meta_key', '=', 'user_action');
		
		if($meta->count() > 1) {
			throw new Exception("Multiple user actions were found");
		}
		
		if($meta->count() == 0) {
			$meta = new NoteMeta();
			
			$meta->user_id = Auth::user()->id;
			$meta->note_id = $annotationId;
			$meta->meta_key = 'user_action';
			$meta->meta_value = $action;
			
			$meta->save();
			
			$retval['action'] = true;
		} else if($meta->count() == 1) {
			$emeta = $meta->first();
			
			if($meta->meta_value == $action) {
				$meta->delete();
				$retval['action'] = false;
			} else {
				$meta->meta_value = $action;
				$meta->save();
				
				$retval['action'] = true;
			}
		}
		
		$retval['likes'] = $annotation->likes();
		$retval['dislikes'] = $annotation->dislikes();
		$retval['flags'] = $annotation->flags();
		
		return $retval;
	}
	
	public function setUserAction($userId)
	{
		$meta = NoteMeta::where('user_id', $userId)
		                 ->where('note_id', '=', $this->id)
		                 ->where('meta_key', '=', 'user_action');
		
		if($meta->count() == 1) {
			$this->user_action = $meta->first()->meta_value;
		}
	}
	
	public function setActionCounts()
	{
		$this->likes = $this->likes();
		$this->dislikes = $this->dislikes();
		$this->flags = $this->flags();
	}
	
	public function likes()
	{
		$likes = NoteMeta::where('note_id', $this->id)
						 ->where('meta_key', '=', 'user_action')
						 ->where('meta_value', '=', 'like')
						 ->count();
		
		return $likes;
	}
	
	public function dislikes()
	{
		$likes = NoteMeta::where('note_id', $this->id)
		->where('meta_key', '=', 'user_action')
		->where('meta_value', '=', 'dislike')
		->count();
	
		return $likes;
	}
	
	public function flags()
	{
		$likes = NoteMeta::where('note_id', $this->id)
		->where('meta_key', '=', 'user_action')
		->where('meta_value', '=', 'flag')
		->count();
	
		return $likes;
	}
}