<?php
/**
 * 	Controller for Document actions
 */
class AnnotationApiController extends ApiController{
	
	protected $es; // ElasticSearch client

	public function __construct(){
		parent::__construct();

		$this->beforeFilter('auth', array('on' => array('post','put', 'delete')));
	}	
	
	//Route for /api/docs{doc}/annotation/{annotation}
	//	Returns json annotation if id found,
	//		404 with error message if id not found,
	//		404 if no id passed
	public function getIndex($doc, $annotation = null){
		try{
			if(Auth::check()){
				$userid = Auth::user()->id;

				if($annotation !== null){
					//TODO
						// This call should be Annotation::find($this->es)->with('actions');
					$results = Annotation::findWithActions($this->es, $annotation, $userid);
				}else{
					//TODO:
						// This call should be Annotation::all($this->es)->with('actions');
					$results = Annotation::allWithActions($this->es, $doc, $userid);
				}
			}else{
				if($annotation !== null){
					$results = Annotation::find($this->es, $annotation);
				}else{
					$results = Annotation::all($this->es, $doc);
				}
			}
		}catch(Exception $e){
			App::abort(404, $e->getMessage());
		}

		return Response::json($results);
	}

	public function postIndex($doc){
		$body = Input::all();
		$body['doc'] = $doc;

		$annotation = new Annotation();
		$annotation->body($body);

		$id = $annotation->save($this->es);

		return Redirect::to('/api/docs/' . $doc . '/annotations/' . $id, 303);
	}

	public function putIndex($id = null){
		
		//If no id requested, return 404
		if($id === null){
			App::abort(404, 'No annotation id passed.');
		}

		$body = Input::all();

		$id = Input::get('id');

		$annotation = Annotation::find($this->es, $id);

		$annotation->body($body);

		$results = $annotation->update($this->es);

		return Response::json($results);
	}

	public function deleteIndex($id = null){
		//If no id requested, return 404
		if($id === null){
			App::abort(404, 'No annotation id passed.');
		}

		try{
			$ret = Annotation::delete($this->es, $id);
		}catch(Exception $e){
			App::abort(404, $e->getMessage());
		}
		
		return Response::make(null, 204);
	}

	public function getSearch(){
		return false;
	}

	public function getLikes($doc, $annotation = null){
		$es = $this->es;

		if($annotation === null){
			App::abort(404, 'No annotation id passed.');
		}

		$likes = Annotation::getMetaCount($es, $annotation, 'likes');

		return Response::json(array('likes' => $likes));
	}

	public function getDislikes($doc, $annotation = null){
		$es = $this->es;

		if($annotation === null){
			App::abort(404, 'No annotation id passed.');
		}

		$dislikes = Annotation::getMetaCount($es, $annotation, 'dislikes');

		return Response::json(array('dislikes' => $dislikes));
	}

	public function getFlags($doc, $annotation = null){
		$es = $this->es;

		if($annotation === null){
			App::abort(404, 'No annotation id passed.');
		}

		$flags = Annotation::getMetaCount($es, $annotation, 'flags');

		return Response::json(array('flags' => $flags));
	}

	public function postLikes($doc, $annotation = null){
		$es = $this->es;

		if($annotation === null){
			App::abort(404, 'No note id passed');
		}

		$postAction = Annotation::addUserAction($es, $annotation, Auth::user()->id, 'like');

		return Response::json($postAction);
	}

	public function postDislikes($doc, $annotation = null){
		$es = $this->es;

		if($annotation === null){
			App::abort(404, 'No note id passed');
		}

		$postAction = Annotation::addUserAction($es, $annotation, Auth::user()->id, 'dislike');

		return Response::json($postAction);
	}	

	public function postFlags($doc, $annotation = null){
		$es = $this->es;

		if($annotation === null){
			App::abort(404, 'No note id passed');
		}

		$postAction = Annotation::addUserAction($es, $annotation, Auth::user()->id, 'flag');

		return Response::json($postAction);
	}

	public function getComments($id = null, $commentId = null){
		$es = $this->es;

		if($id !== null){
			$results = Comment::find($this->es, $id);
		}else{
			$results = Comment::all($this->es);
		}

		return Response::json($results);
	}

	public function postComments($doc, $annotation = null){
		if($annotation === null){
			throw new Exception("Unable to post comment without annotation id.");
		}

		$comment = Input::get('comment');

		$annotation = Annotation::find($this->es, $annotation);

		$results = $annotation->addComment($this->es, $comment);

		return Response::json($results);
	}
}

