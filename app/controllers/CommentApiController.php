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
		try{
			$userId = null;
			if(Auth::check()){
				$userId = Auth::user()->id;
			}

			$results = Comment::loadComments($doc, $comment, $userId);
		}catch(Exception $e){
			throw $e;
			App::abort(500, $e->getMessage());
		}

		return Response::json($results);
	}

	public function postIndex($doc){
		$comment = Input::get('comment');

		$newComment = new Comment();
		$newComment->user_id = Auth::user()->id;
		$newComment->doc_id = $comment['doc']['id'];
		$newComment->text = $comment['text'];

		$return = $newComment->save();

		return Response::json($return);
	}

	public function postSeen($docId, $commentId) {
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

		$comment = Comment::find($commentId);
		$comment->seen = 1;
		$comment->save();
		
		$doc = Doc::find($docId);
		$vars = array('sponsor' => $user->fname . ' ' . $user->lname, 'label' => 'comment', 'slug' => $doc->slug, 'title' => $doc->title, 'text' => $comment->text);
		$email = $comment->user->email;

		Mail::queue('email.read', $vars, function ($message) use ($email)
		{
    		$message->subject('Your feedback on Madison was viewed by a sponsor!');
    		$message->from('sayhello@opengovfoundation.org', 'Madison');
    		$message->to($email); // Recipient address
		});

		return Response::json($comment);
	
	}
	
	public function postLikes($docId, $commentId) {
		$comment = Comment::find($commentId);
		$comment->saveUserAction(Auth::user()->id, Comment::ACTION_LIKE);

		return Response::json($comment->loadArray());
	}

	public function postDislikes($docId, $commentId) {
		$comment = Comment::find($commentId);
		$comment->saveUserAction(Auth::user()->id, Comment::ACTION_DISLIKE);

		return Response::json($comment->loadArray());
	}

	public function postFlags($docId, $commentId) {
		$comment = Comment::find($commentId);
		$comment->saveUserAction(Auth::user()->id, Comment::ACTION_FLAG);

		return Response::json($comment->loadArray());
	}

	public function postComments($docId, $commentId) {
		$comment = Input::get('comment');

		$parent = Comment::where('doc_id', '=', $docId)
								->where('id', '=', $commentId)
							    ->first();

		$result = $parent->addOrUpdateComment($comment);
		
		return Response::json($result);
	}
}

