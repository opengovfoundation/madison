<?php
class Comment extends Eloquent{
	protected $table = 'comments';

    const ACTION_LIKE = 'like';
    const ACTION_DISLIKE = 'dislike';
    const ACTION_FLAG = 'flag';

	public function doc(){
		return $this->belongsTo('Doc', 'doc_id');
	}

	public function user(){
		return $this->belongsTo('User', 'user_id');
	}

    public function comments(){
        return $this->hasMany('Comment', 'parent_id');
    }

    public function likes()
    {   
        $likes =  CommentMeta::where('comment_id', $this->id)
                    ->where('meta_key', '=', CommentMeta::TYPE_USER_ACTION)
                    ->where('meta_value', '=', static::ACTION_LIKE)
                    ->count();

        return $likes;
    }

    public function dislikes()
    {
        $dislikes = CommentMeta::where('comment_id', $this->id)
                             ->where('meta_key', '=', CommentMeta::TYPE_USER_ACTION)
                             ->where('meta_value', '=', static::ACTION_DISLIKE)
                             ->count();
    
        return $dislikes;
    }
    
    public function flags()
    {
        $flags = CommentMeta::where('comment_id', $this->id)
                         ->where('meta_key', '=', CommentMeta::TYPE_USER_ACTION)
                         ->where('meta_value', '=', static::ACTION_FLAG)
                         ->count();
    
        return $flags;
    }

    public function loadArray($userId = null){
        $item = $this->toArray();
        $item['created'] = $item['created_at'];
        $item['updated'] = $item['updated_at'];
        $item['likes'] = $this->likes();
        $item['dislikes'] = $this->dislikes();
        $item['flags'] = $this->flags();

        return $item;
    }

    public function saveUserAction($userId, $action) {
        switch($action) {
            case static::ACTION_LIKE:
            case static::ACTION_DISLIKE:
            case static::ACTION_FLAG:
                break;
            default:
                throw new \InvalidArgumentException("Invalid Action to Add");
        }

        $actionModel = CommentMeta::where('comment_id', '=', $this->id)
                                    ->where('user_id', '=', $userId)
                                    ->where('meta_key', '=', CommentMeta::TYPE_USER_ACTION)
                                    ->take(1)->first();

        if(is_null($actionModel)) {
            $actionModel = new CommentMeta();
            $actionModel->meta_key = CommentMeta::TYPE_USER_ACTION;
            $actionModel->user_id = $userId;
            $actionModel->comment_id = $this->id;
        }

        $actionModel->meta_value = $action;

        return $actionModel->save();
    }

    public function addOrUpdateComment(array $comment) {
        $obj = new Comment();
        $obj->text = $comment['text'];
        $obj->user_id = $comment['user']['id'];
        $obj->doc_id = $this->doc_id;

        if(isset($comment['id'])) {
            $obj->id = $comment['id'];
        }
        
        $obj->parent_id = $this->id;
        
        $obj->save();
        $obj->load('user');

        return $obj;
    }    

    static public function loadComments($docId, $commentId, $userId){
        $comments = static::where('doc_id', '=', $docId)->whereNull('parent_id')->with('comments');

        foreach($comments->comments as $subcomment){
            $subcomment->load('user');
        }

        if(!is_null($commentId)){
            $comments->where('id', '=', $commentId);
        }

        $comments = $comments->get();

        $retval = array();
        foreach($comments as $comment){
            $retval[] = $comment->loadArray();
        }

        return $retval;
    }
}

