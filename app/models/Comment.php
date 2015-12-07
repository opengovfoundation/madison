<?php
class Comment extends Eloquent implements ActivityInterface
{
    protected $table = 'comments';
    protected $softDelete = true;

    const ACTION_LIKE = 'like';
    const ACTION_FLAG = 'flag';

    public function doc()
    {
        return $this->belongsTo('Doc', 'doc_id');
    }

    public function user()
    {
        return $this->belongsTo('User', 'user_id');
    }

    public function comments()
    {
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

    public function flags()
    {
        $flags = CommentMeta::where('comment_id', $this->id)
                         ->where('meta_key', '=', CommentMeta::TYPE_USER_ACTION)
                         ->where('meta_value', '=', static::ACTION_FLAG)
                         ->count();

        return $flags;
    }

    public function replyCount()
    {
        return (int) static::where('parent_id', $this->id)->count();
    }

    public function loadArray($userId = null)
    {
        $item = $this->toArray();
        $item['created'] = $item['created_at'];
        $item['updated'] = $item['updated_at'];
        $item['likes'] = $this->likes();
        $item['flags'] = $this->flags();
        $item['replyCount'] = $this->replyCount();

        return $item;
    }

    public function saveUserAction($userId, $action)
    {
        switch ($action) {
            case static::ACTION_LIKE:
            case static::ACTION_FLAG:
                break;
            default:
                throw new \InvalidArgumentException("Invalid Action to Add");
        }

        $actionModel = CommentMeta::where('comment_id', '=', $this->id)
                                    ->where('user_id', '=', $userId)
                                    ->where('meta_key', '=', CommentMeta::TYPE_USER_ACTION)
                                    ->take(1)->first();

        if (is_null($actionModel)) {
            $actionModel = new CommentMeta();
            $actionModel->meta_key = CommentMeta::TYPE_USER_ACTION;
            $actionModel->user_id = $userId;
            $actionModel->comment_id = $this->id;
        }

        $actionModel->meta_value = $action;

        return $actionModel->save();
    }

    /**
     *   addOrUpdateComment.
     *
     *   Updates or creates a Comment
     *
     *   @param array $comment
     *
     *   @return Comment $obj with User relationship loaded
     */
    public function addOrUpdateComment(array $comment)
    {
        $obj = new Comment();
        $obj->text = $comment['text'];
        $obj->user_id = $comment['user']['id'];
        $obj->doc_id = $this->doc_id;

        if (isset($comment['id'])) {
            $obj->id = $comment['id'];
        }

        $obj->parent_id = $this->id;

        $obj->save();
        $obj->load('user');

        return $obj;
    }

    /**
     *   Construct link for Comment.
     *
     *   @param null
     *
     *   @return url
     */
    public function getLink()
    {
        $slug = DB::table('docs')->where('id', $this->doc_id)->pluck('slug');

        return URL::to('docs/'.$slug.'#comment_'.$this->id);
    }

    /**
     *   Create RSS item for Comment.
     *
     *   @param null
     *
     *   @return array $item
     */
    public function getFeedItem()
    {
        $user = $this->user()->get()->first();

        $item['title'] = $user->fname.' '.$user->lname."'s Comment";
        $item['author'] = $user->fname.' '.$user->lname;
        $item['link'] = $this->getLink();
        $item['pubdate'] = $this->updated_at;
        $item['description'] = $this->text;

        return $item;
    }

    /**
     * Load multiple Comments.
     *
     * @param int $docId the document id.
     * @param int $parentId the comment's parent.
     * @param int $userId the owner's user id.
     */
    public static function loadComments($docId, $parentId = null, $userId = null)
    {
        $comments = static::where('doc_id', '=', $docId)->with('user');

        if (!is_null($parentId)) {
            $comments->where('parent_id', '=', $parentId);
        }
        // If we don't have a $parentId, only return top-level comments.
        else {
            $comments->whereNull('parent_id');
        }

        $comments = $comments->get();

        $retval = array();
        foreach ($comments as $comment) {
            $retval[] = $comment->loadArray();
        }

        usort($retval, function ($a, $b) {
            if ($a['likes'] == $b['likes']) return 0;
            return ($a['likes'] > $b['likes']) ? -1 : 1;
        });

        return $retval;
    }

    /**
     * Load a single Comment.
     *
     * @param int $docId the document id.
     * @param int $commentId the comment id.
     * @param int $userId the owner's user id.
     */
    public static function loadComment($docId, $commentId, $userId = null)
    {
        $comments = static::where('doc_id', '=', $docId)->with('user');

        $comments->where('id', '=', $commentId);

        $comments = $comments->get();

        $retval = array();
        foreach ($comments as $comment) {
            $retval[] = $comment->loadArray();
        }

        return $retval;
    }


    /**
     *   Include link to annotation when converted to array.
     *
     *   @param null
     *
     * @return parent::toArray()
     */
    public function toArray()
    {
        $this->link = $this->getLink();

        return parent::toArray();
    }
}
