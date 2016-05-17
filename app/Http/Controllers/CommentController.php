<?php

namespace App\Http\Controllers;

use Auth;
use Input;
use Response;
use Event;
use App\Events\CommentCreated;
use App\Events\FeedbackSeen;
use App\Models\Comment;

/**
 * 	Controller for Document actions.
 */
class CommentController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->beforeFilter('auth', array('on' => array('post', 'put', 'delete')));
    }

    public function getIndex($doc)
    {
        try {
            $userId = null;
            if (Auth::check()) {
                $userId = Auth::user()->id;
            }

            $parentId = Input::get('parent_id');

            $results = Comment::loadComments($doc, $parentId, $userId);
        } catch (Exception $e) {
            throw $e;
            App::abort(500, $e->getMessage());
        }

        return Response::json($results);
    }

    public function getComment($doc, $commentId)
    {
        try {
            $userId = null;
            if (Auth::check()) {
                $userId = Auth::user()->id;
            }

            $result = Comment::loadComment($doc, $commentId, $userId);
        } catch (Exception $e) {
            throw $e;
            App::abort(500, $e->getMessage());
        }

        return Response::json($result);
    }

    public function postIndex($doc)
    {
        $comment = Input::get('comment');

        $newComment = new Comment();
        $newComment->user_id = Auth::user()->id;
        $newComment->doc_id = $comment['doc']['id'];
        $newComment->text = $comment['text'];
        $newComment->save();

        // Late load the user.
        $newComment->user;

        return Response::json($newComment->toArray());
    }

    public function postSeen($docId, $commentId)
    {
        $allowed = false;

        $user = Auth::user();
        $user->load('docs');

        // Check user documents against current document
        foreach ($user->docs as $doc) {
            if ($doc->id == $docId) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            throw new Exception("You are not authorized to mark this annotation as seen.");
        }

        $comment = Comment::find($commentId);
        $comment->seen = 1;
        $comment->save();

        Event::fire(new FeedbackSeen($comment, $user));

        return Response::json($comment);
    }

    public function postLikes($docId, $commentId)
    {
        $comment = Comment::find($commentId);
        $comment->saveUserAction(Auth::user()->id, Comment::ACTION_LIKE);

        //Load fields for notification
        $comment->load('user');
        $comment->type = 'comment';

        return Response::json($comment->loadArray());
    }

    public function postFlags($docId, $commentId)
    {
        $comment = Comment::find($commentId);
        $comment->saveUserAction(Auth::user()->id, Comment::ACTION_FLAG);

        return Response::json($comment->loadArray());
    }

    public function postComments($docId, $commentId)
    {
        $comment = Input::get('comment');

        $parent = Comment::where('doc_id', '=', $docId)
                                ->where('id', '=', $commentId)
                                ->first();

        $parent->load('user');
        $parent->type = 'comment';

        //Returns the new saved Comment with the User relationship loaded
        $result = $parent->addOrUpdateComment($comment);

        Event::fire(new CommentCreated($result, $parent));

        return Response::json($result);
    }
}
