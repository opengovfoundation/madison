<?php
/**
 * 	Controller for Document actions
 */
class AnnotationApiController extends ApiController{

	public function __construct(){
		parent::__construct();

		$this->beforeFilter('auth', array('on' => array('post','put', 'delete')));
	}	
	
	//Route for /api/docs{doc}/annotation/{annotation}
	//	Returns json annotation if id found,
	//		404 with error message if id not found,
	//		404 if no id passed
	
	/**
	 * Get annotations by document ID and annotation ID
	 * @param interger $docId
	 * @param string $annotationId optional, if not provided get all
	 * @throws Exception
	 */
	public function getIndex($docId, $annotationId = null){
		try{
			$userId = null;
			if(Auth::check()){
				$userId = Auth::user()->id;
			}
			
			$results = Annotation::loadAnnotationsForAnnotator($docId, $annotationId, $userId);
		}catch(Exception $e){
			throw $e;
			App::abort(500, $e->getMessage());
		} 
		
		if(isset($annotationId)){
			return Response::json($results[0]);
		}

		return Response::json($results);
	}

	/**
	 * Create a new annotation
	 * @param document ID $doc
	 * @throws Exception
	 * @return 303 redirect to annotation link
	 */
	public function postIndex($doc){
		$body = Input::all();
		$body['doc_id'] = $doc;
		$is_edit = false;

		//Check for edit tag
		if(in_array('edit', $body['tags'])){
			$is_edit = true;

			//If no explanation present, throw error
			if(!isset($body['explanation'])){
				throw new Exception('Explanation required for edits');
			}
		}

		$id = DB::transaction(function() use ($body, $doc, $is_edit){
			$annotation = new Annotation();
			$annotation->doc_id = $doc;
			$annotation->user_id = Auth::user()->id;
			$annotation->quote = $body['quote'];
			$annotation->text = $body['text'];
			$annotation->uri = $body['uri'];

			$annotation->save();

			foreach($body['ranges'] as $range){
				$rangeObj = new AnnotationRange();
				$rangeObj->annotation_id = $annotation->id;
				$rangeObj->start_offset = $range['startOffset'];
				$rangeObj->end_offset = $range['endOffset'];
				$rangeObj->start = $range['start'];
				$rangeObj->end = $range['end'];

				$rangeObj->save();
			}
			
			$permissions = new AnnotationPermission();
			$permissions->annotation_id = $annotation->id;
			$permissions->user_id = Auth::user()->id;
			$permissions->read = 1;
			$permissions->update = 0;
			$permissions->delete = 0;
			$permissions->admin = 0;
			$permissions->save();

			foreach($body['tags'] as $tag){
				$tagObj = new AnnotationTag();
				$tagObj->annotation_id = $annotation->id;
				$tagObj->tag = $tag;
				$tagObj->save();
			}

			if($is_edit){
				$comment = new AnnotationComment();
				$comment->text = $body['explanation'];
				$comment->user_id = $annotation->user_id;
				$comment->annotation_id = $annotation->id;

				$comment->save();
			}

			$annotation->updateSearchIndex();

			return $annotation->id;
		});
		
		return Redirect::to('/api/docs/' . $doc . '/annotations/' . $id, 303);
	}

	public function postSeen($docId, $annotationId) {
		$allowed = false;

		$user = Auth::user();
		$user->load('docs');

		// Check user documents against current document
		foreach($user->docs as $doc){
			if($doc->id == $docId){
				$allowed = true;
				break;
			}
		}

		if(!$allowed){
			throw new Exception("You are not authorized to mark this annotation as seen.");
		}

		//The user is allowed to make this action		
		$annotation = Annotation::find($annotationId);
		$annotation->seen = 1;
		$annotation->save();

		$doc = Doc::find($docId);
		$vars = array('sponsor' => $user->fname . ' ' . $user->lname, 'label' => 'annotation', 'slug' => $doc->slug, 'title' => $doc->title, 'text' => $annotation->text);
		$email = $annotation->user->email;

		Mail::queue('email.read', $vars, function ($message) use ($email)
		{
    		$message->subject('Your feedback on Madison was viewed by a sponsor!');
    		$message->from('sayhello@opengovfoundation.org', 'Madison');
    		$message->to($email); // Recipient address
		});

		return Response::json($annotation);		
	}

	/**
	 * Update an existing annotation
	 * @param string $id
	 */
	public function putIndex($id = null){
		
		//If no id requested, return 404
		if($id === null){
			App::abort(404, 'No annotation id passed.');
		}

		$body = Input::all();
		
		$annotation = Annotation::createFromAnnotatorArray($body);
		$annotation->updateSearchIndex();
		
		return Response::json($annotation);
	}

	/**
	 * Delete an annotation by doc ID and annotation ID
	 * 
	 * @param int $doc
	 * @param int $annotation
	 */
	public function deleteIndex($doc, $annotation){
		//If no id requested, return 404
		if($annotation === null){
			App::abort(404, 'No annotation id passed.');
		}

		$annotation = Annotation::find($annotation);
		
		$ret = $annotation->delete();
		
		return Response::make(null, 204);
	}

	/**
	 * Return search results for annotations
	 */
	public function getSearch(){
		return false;
	}

	public function getLikes($doc, $annotation = null){
		if($annotation === null){
			App::abort(404, 'No annotation id passed.');
		}

		$likes = Annotation::getMetaCount($annotation, 'likes');

		return Response::json(array('likes' => $likes));
	}

	public function getDislikes($doc, $annotation = null){
		if($annotation === null){
			App::abort(404, 'No annotation id passed.');
		}

		$dislikes = Annotation::getMetaCount($annotation, 'dislikes');

		return Response::json(array('dislikes' => $dislikes));
	}

	public function getFlags($doc, $annotation = null){
		if($annotation === null){
			App::abort(404, 'No annotation id passed.');
		}

		$flags = Annotation::getMetaCount($annotation, 'flags');

		return Response::json(array('flags' => $flags));
	}

	public function postLikes($doc, $annotation = null){
		if($annotation === null){
			App::abort(404, 'No note id passed');
		}

		$annotation = Annotation::find($annotation);
		$annotation->saveUserAction(Auth::user()->id, Annotation::ACTION_LIKE);

		return Response::json($annotation->toAnnotatorArray());
	}

	public function postDislikes($doc, $annotation = null){
		if($annotation === null){
			App::abort(404, 'No note id passed');
		}

		$annotation = Annotation::find($annotation);
		$annotation->saveUserAction(Auth::user()->id, Annotation::ACTION_DISLIKE);

		return Response::json($annotation->toAnnotatorArray());
	}	

	public function postFlags($doc, $annotation = null){
		if($annotation === null){
			App::abort(404, 'No note id passed');
		}

		$annotation = Annotation::find($annotation);
		$annotation->saveUserAction(Auth::user()->id, Annotation::ACTION_FLAG);

		return Response::json($annotation->toAnnotatorArray());
	}

	public function postComments($docId, $annotationId){

		$comment = Input::get('comment');

		$annotation = Annotation::where('doc_id', '=', $docId)
								->where('id', '=', $annotationId)
							    ->first();

		$result = $annotation->addOrUpdateComment($comment);
		
		Event::fire(OpenGovEvent::DOC_COMMENTED, array('annotation' => $annotation, 'comment' => $comment));
		
		return Response::json($result);
	}
}

