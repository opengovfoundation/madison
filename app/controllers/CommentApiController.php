<?php
/**
 * 	Controller for Document actions
 */
class CommentApiController extends ApiController{

	public function __construct(){
		parent::__construct();

		$this->beforeFilter('auth', array('on' => array('post','put', 'delete')));
	}	
	
	public function getIndex($doc, $comment = null){
		if($comment === null){
			$comments = Comment::where('doc_id', $doc)->get();

			if($comments->isEmpty()){
				$comments = array();
			}

			return Response::json($comments);
		}

	}

	public function postIndex($doc){
		$comment = Input::get('comment');

		$newComment = new Comment();
		$newComment->user_id = Auth::user()->id;
		$newComment->doc_id = $comment['doc']['id'];
		$newComment->content = $comment['content'];

		$return = $newComment->save();

		return Response::json($return);
	}
}

