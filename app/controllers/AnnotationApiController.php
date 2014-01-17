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
	public function getIndex($doc, $annotation = null){
		try{
			if(Auth::check()){
				$userid = Auth::user()->id;

				if($annotation !== null){
					$results = Annotation::findWithActions($annotation, $userid);
				}else{
					$results = Annotation::allWithActions($doc, $userid);
				}
			}else{
				if($annotation !== null){
					$results = Annotation::find($annotation);
				}else{
					$results = Annotation::all($doc);
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

		$id = $annotation->save();

		return Redirect::to('/api/docs/' . $doc . '/annotations/' . $id, 303);
	}

	public function putIndex($id = null){
		
		//If no id requested, return 404
		if($id === null){
			App::abort(404, 'No annotation id passed.');
		}

		$body = Input::all();

		$id = Input::get('id');

		$annotation = Annotation::find($id);

		$annotation->body($body);

		$results = $annotation->update();

		return Response::json($results);
	}

	public function deleteIndex($doc, $annotation){
		//If no id requested, return 404
		if($annotation === null){
			App::abort(404, 'No annotation id passed.');
		}

		$annotation = Annotation::find($annotation);
		
		$ret = $annotation->delete();
		
		return Response::make(null, 204);
	}

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

		$postAction = Annotation::addUserAction($annotation, Auth::user()->id, 'like');

		return Response::json($postAction);
	}

	public function postDislikes($doc, $annotation = null){
		if($annotation === null){
			App::abort(404, 'No note id passed');
		}

		$postAction = Annotation::addUserAction($annotation, Auth::user()->id, 'dislike');

		return Response::json($postAction);
	}	

	public function postFlags($doc, $annotation = null){
		if($annotation === null){
			App::abort(404, 'No note id passed');
		}

		$postAction = Annotation::addUserAction($annotation, Auth::user()->id, 'flag');

		return Response::json($postAction);
	}

	public function postComments($doc, $annotation = null){
		if($annotation === null){
			throw new Exception("Unable to post comment without annotation id.");
		}

		$comment = Input::get('comment');

		$annotation = Annotation::find($annotation);

		$results = $annotation->addComment($comment);

		return Response::json($results);
	}
}

