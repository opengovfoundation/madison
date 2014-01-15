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
	
	//Route for /api/annotation/{id}
	//	Returns json annotation if id found,
	//		404 with error message if id not found,
	//		404 if no id passed
	public function getIndex($id = null){

		try{
			if(Auth::check()){
				$userid = Auth::user()->id;

				if($id !== null){
					//TODO
						// This call should be Annotation::find($this->es)->with('actions');
					$results = Annotation::findWithActions($this->es, $id, $userid);
				}else{
					//TODO:
						// This call should be Annotation::all($this->es)->with('actions');
					$results = Annotation::allWithActions($this->es, $userid);
				}
			}else{
				if($id !== null){
					$results = Annotation::find($this->es, $id);
				}else{
					$results = Annotation::all($this->es);
				}
			}
		}catch(Exception $e){
			App::abort(404, $e->getMessage());
		}

		return Response::json($results);
	}

	public function postIndex(){
		$body = Input::all();

		$annotation = new Annotation();
		$annotation->body($body);

		$id = $annotation->save($this->es);

		return Redirect::to('/api/annotations/' . $id, 303);
	}

	public function putIndex($id = null){
		
		//If no id requested, return 404
		if($id === null){
			App::abort(404, 'No annotation id passed.');
		}

		$body = Input::all();

		$id = Input::get('id');

		$annotation = Annotation::find($this->es, $id);

		$results = $annotation->update($this->es, $body);

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

	public function getLikes($id = null){
		$es = $this->es;

		if($id === null){
			App::abort(404, 'No annotation id passed.');
		}

		$likes = Annotation::getMetaCount($es, $id, 'likes');

		return Response::json(array('likes' => $likes));
	}

	public function getDislikes($id = null){
		$es = $this->es;

		if($id === null){
			App::abort(404, 'No annotation id passed.');
		}

		$dislikes = Annotation::getMetaCount($es, $id, 'dislikes');

		return Response::json(array('dislikes' => $dislikes));
	}

	public function getFlags($id = null){
		$es = $this->es;

		if($id === null){
			App::abort(404, 'No annotation id passed.');
		}

		$flags = Annotation::getMetaCount($es, $id, 'flags');

		return Response::json(array('flags' => $flags));
	}

	public function postLikes($id = null){
		$es = $this->es;

		if($id === null){
			App::abort(404, 'No note id passed');
		}

		$postAction = Annotation::addUserAction($es, $id, Auth::user()->id, 'like');

		return Response::json($postAction);
	}

	public function postDislikes($id = null){
		$es = $this->es;

		if($id === null){
			App::abort(404, 'No note id passed');
		}

		$postAction = Annotation::addUserAction($es, $id, Auth::user()->id, 'dislike');

		return Response::json($postAction);
	}	

	public function postFlags($id = null){
		$es = $this->es;

		if($id === null){
			App::abort(404, 'No note id passed');
		}

		$postAction = Annotation::addUserAction($es, $id, Auth::user()->id, 'flag');

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

	public function postComments($id = null){
		if($id === null){
			throw new Exception("Unable to post comment without annotation id.");
		}

		$comment = Input::get('comment');

		$annotation = Annotation::find($this->es, $id);

		$results = $annotation->addComment($this->es, $comment);

		return Response::json($results);
	}
}

