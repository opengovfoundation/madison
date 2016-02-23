<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\NoteMeta;

class Annotation extends Model implements ActivityInterface
{
    use SoftDeletes;

    const INDEX_TYPE = 'annotation';

    const ANNOTATION_CONSUMER = "Madison";

    const ACTION_LIKE = 'like';
    const ACTION_FLAG = 'flag';

    protected $table = "annotations";
    protected $fillable = array('quote', 'text', 'uri', 'seen');
    protected $dates = ['deleted_at'];

    protected static $_esInstance = null;
    protected static $_esIndex;

    public static function getEsClient()
    {
        static::connectToEs();

        return static::$_esInstance;
    }

    public static function setEsIndex($index)
    {
        static::$_esIndex = $index;
    }

    public static function getEsIndex()
    {
        return static::$_esIndex;
    }

    public static function connectToEs()
    {
        if (is_null(static::$_esInstance)) {
            $params = array(
                'hosts' => Config::get('elasticsearch.hosts'),
            );

            static::$_esInstance = new ElasticSearch\Client($params);
        }

        static::setEsIndex(Config::get('elasticsearch.annotationIndex'));

        return static::$_esInstance;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\AnnotationComment', 'annotation_id');
    }

    public function tags()
    {
        return $this->hasMany('App\Models\AnnotationTag', 'annotation_id');
    }

    public function permissions()
    {
        return $this->hasMany('App\Models\AnnotationPermission', 'annotation_id');
    }

    public function doc()
    {
        return $this->belongsTo('App\Models\Doc', 'doc_id');
    }

    public static function createFromAnnotatorArray(array $input)
    {
        if (isset($input['id'])) {
            $retval = static::firstOrNew(array('id' => $input['id']));
        } else {
            $retval = new static();
        }

        $retval->doc_id = (int) $input['doc_id'];

        if (isset($input['user']) && is_array($input['user'])) {
            $retval->user_id = (int) $input['user']['id'];
        }

        if (isset($input['quote'])) {
            $retval->quote = $input['quote'];
        }

        if (isset($input['text'])) {
            $retval->text = $input['text'];
        }

        if (isset($input['uri'])) {
            $retval->uri = $input['uri'];
        }

        DB::transaction(function () use ($retval, $input) {

            $retval->save();

            if (isset($input['ranges'])) {
                foreach ($input['ranges'] as $range) {
                    $rangeObj = AnnotationRange::firstByRangeOrNew(array(
                            'annotation_id' => $retval->id,
                            'start_offset' => $range['startOffset'],
                            'end_offset' => $range['endOffset'],
                    ));

                    $rangeObj->start = $range['start'];
                    $rangeObj->end = $range['end'];

                    $rangeObj->save();
                }
            }

            if (isset($input['comments']) && is_array($input['comments'])) {
                foreach ($input['comments'] as $comment) {
                    $commentObj = AnnotationComment::firstOrNew(array(
                            'id' => (int) $comment['id'],
                            'annotation_id' => $retval->id,
                            'user_id' => (int) $comment['user']['id'],
                    ));

                    $commentObj->text = $comment['text'];

                    $commentObj->save();
                }
            }

            $permissions = array();

            if (isset($input['permissions']) && is_array($input['permissions'])) {
                foreach ($input['permissions']['read'] as $userId) {
                    $userId = (int) $userId;

                    if (!isset($permissions[$userId])) {
                        $permissions[$userId] = array('read' => false, 'update' => false, 'delete' => false, 'admin' => false);
                    }

                    $permissions[$userId]['read'] = true;
                }

                foreach ($input['permissions']['update'] as $userId) {
                    $userId = (int) $userId;

                    if (!isset($permissions[$userId])) {
                        $permissions[$userId] = array('read' => false, 'update' => false, 'delete' => false, 'admin' => false);
                    }

                    $permissions[$userId]['update'] = true;
                }

                foreach ($input['permissions']['delete'] as $userId) {
                    $userId = (int) $userId;

                    if (!isset($permissions[$userId])) {
                        $permissions[$userId] = array('read' => false, 'update' => false, 'delete' => false, 'admin' => false);
                    }

                    $permissions[$userId]['delete'] = true;
                }

                foreach ($input['permissions']['admin'] as $userId) {
                    $userId = (int) $userId;

                    if (!isset($permissions[$userId])) {
                        $permissions[$userId] = array('read' => false, 'update' => false, 'delete' => false, 'admin' => false);
                    }

                    $permissions[$userId]['admin'] = true;
                }
            }

            foreach ($permissions as $userId => $perms) {
                $userId = (int) $userId;

                $permissionsObj = AnnotationPermission::firstOrNew(array(
                    'annotation_id' => $retval->id,
                    'user_id' => $userId,
                ));

                $permissionsObj->read = (int) $perms['read'];
                $permissionsObj->update = (int) $perms['update'];
                $permissionsObj->delete = (int) $perms['delete'];
                $permissionsObj->admin = (int) $perms['admin'];

                $permissionsObj->save();
            }

            if (isset($input['tags']) && is_array($input['tags'])) {
                foreach ($input['tags'] as $tag) {
                    AnnotationTag::where('annotation_id', '=', $retval->id)->delete();

                    $tag = AnnotationTag::firstOrNew(array(
                        'annotation_id' => $retval->id,
                        'tag' => strtolower($tag),
                    ));

                    $tag->save();
                }
            }

        });

        return $retval;
    }

    public function toAnnotatorArray($userId = null)
    {
        $item = $this->toArray();
        $item['annotator_schema_version'] = 'v1.0';
        $item['ranges'] = array();
        $item['tags'] = array();
        $item['comments'] = array();
        $item['permissions'] = array(
            'read' => array(),
            'update' => array(),
            'delete' => array(),
            'admin' => array(),
        );

        $comments = AnnotationComment::where('annotation_id', '=', $item['id'])->get();

        foreach ($comments as $comment) {
            $user = User::find($comment['user_id']);

            $item['comments'][] = array(
                'id' => $comment->id,
                'text' => $comment->text,
                'created_at' => $comment->created_at,
                'updated_at' => $comment->updated_at,
                'user' => array(
                    'id' => $user->id,
                    'email' => $user->email,
                    'display_name' => $user->display_name,
                ),
            );
        }

        $ranges = AnnotationRange::where('annotation_id', '=', $item['id'])->get();

        foreach ($ranges as $range) {
            $item['ranges'][] = array(
                'start' => $range['start'],
                'end' => $range['end'],
                'startOffset' => $range['start_offset'],
                'endOffset' => $range['end_offset'],
            );
        }

        $user = User::where('id', '=', $item['user_id'])->first();
        $item['user'] = array_intersect_key($user->toArray(), array_flip(array('id', 'email')));
        $item['user']['display_name'] = $user->display_name;

        $item['consumer'] = static::ANNOTATION_CONSUMER;

        $tags = AnnotationTag::where('annotation_id', '=', $item['id'])->get();

        foreach ($tags as $tag) {
            $item['tags'][] = $tag->tag;
        }

        $permissions = AnnotationPermission::where('annotation_id', '=', $item['id'])->get();

        foreach ($permissions as $perm) {
            if ($perm->read) {
                $item['permissions']['read'][] = $perm['user_id'];
            }

            if ($perm->update) {
                $item['permissions']['update'][] = $perm['user_id'];
            } else {
                $item['permissions']['update'][] = '0';
            }

            if ($perm->delete) {
                $item['permissions']['delete'][] = $perm['user_id'];
            } else {
                $item['permissions']['delete'][] = '0';
            }

            if ($perm->admin) {
                $item['permissions']['admin'][] = $perm['user_id'];
            } else {
                $item['permissions']['admin'][] = '0';
            }
        }

        if (!is_null($userId)) {
            $noteModel = NoteMeta::where('user_id', '=', $userId)
                                 ->where('meta_key', '=', NoteMeta::TYPE_USER_ACTION)
                                 ->take(1)->first();

            if (!is_null($noteModel)) {
                $item['user_action'] = $noteModel->meta_value;
            }
        }

        $item['likes'] = $this->likes();
        $item['flags'] = $this->flags();
        $item['seen'] = $this->seen;

        //fix for #804: Time stamps for annotations are off by several hours
        //convert date to RFC2822 string
        $item['created_at'] = date_format(date_create($item['created_at']), "r");
        $item['updated_at'] = date_format(date_create($item['updated_at']), "r");

        $item = array_intersect_key($item, array_flip(array(
            'id', 'annotator_schema_version', 'created_at', 'updated_at',
            'text', 'quote', 'uri', 'ranges', 'user', 'consumer', 'tags',
            'permissions', 'likes', 'flags', 'seen', 'comments', 'doc_id',
            'user_action',
        )));

        return $item;
    }

    public static function loadAnnotationsForAnnotator($docId, $annotationId = null, $userId = null)
    {
        $annotations = static::where('doc_id', '=', $docId);

        if (!is_null($annotationId)) {
            $annotations->where('id', '=', $annotationId);
        }

        $annotations = $annotations->get();

        $retval = array();
        foreach ($annotations as $annotation) {
            $retval[] = $annotation->toAnnotatorArray();
        }

        return $retval;
    }

    public function updateSearchIndex()
    {
        $indexData = $this->toAnnotatorArray();
        $client = static::getEsClient();

        $esParams = array(
            'index' => static::getEsIndex(),
            'type' => static::INDEX_TYPE,
        );

        if (!empty($this->search_id)) {
            $esParams['body']['doc'] = $indexData;
            $esParams['id'] = $this->search_id;

            $result = $client->update($esParams);

            return $this->save();
        }

        $esParams['body'] = $indexData;

        $result = $client->index($esParams);
        $this->search_id = $result['_id'];

        return $this->save();
    }

    public function delete()
    {
        $client = static::getEsClient();

        $esParams = array(
            'index' => static::getEsIndex(),
            'type' => static::INDEX_TYPE,
            'id' => $this->search_id,
        );
        try {
            $client->delete($esParams);
        } catch (Exception $e) {
            $message = json_decode($e->getMessage());

            if ($message->ok) {
                Log::warning("The annotation with id: ".$this->search_id." was not found in ElasticSearch.  Deleting annotation from the DB...");
            } else {
                throw new Exception("Unable to delete annotation from ElasticSearch: ".$e->getMessage());
            }
        }

        DB::transaction(function () {
            $deletedMetas = NoteMeta::where('annotation_id', '=', $this->id)->delete();
            $deletedComments = AnnotationComment::where('annotation_id', '=', $this->id)->delete();
            $deletedPermissions = AnnotationPermission::where('annotation_id', '=', $this->id)->delete();
            $deletedRanges = AnnotationRange::where('annotation_id', '=', $this->id)->delete();
            $deletedTags = AnnotationTag::where('annotation_id', '=', $this->id)->delete();

            return parent::delete();
        });
    }

    public function addOrUpdateComment(array $comment)
    {
        $obj = new AnnotationComment();
        $obj->text = $comment['text'];
        $obj->user_id = $comment['user']['id'];

        if (isset($comment['id'])) {
            $obj->id = $comment['id'];
        }

        $obj->annotation_id = $this->id;

        $obj->save();
        $obj->load('user');

        return $obj;
    }

    public static function getMetaCount($id, $action)
    {
        $annotation = static::where('annotation_id', '=', $id);

        $actionCount = $annotation->$action();

        return $actionCount;
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

        $actionModel = NoteMeta::where('annotation_id', '=', $this->id)
                                ->where('user_id', '=', $userId)
                                ->where('meta_key', '=', NoteMeta::TYPE_USER_ACTION)
                                ->take(1)->first();

        if (is_null($actionModel)) {
            $actionModel = new NoteMeta();
            $actionModel->meta_key = NoteMeta::TYPE_USER_ACTION;
            $actionModel->user_id = $userId;
            $actionModel->annotation_id = $this->id;
        }

        $actionModel->meta_value = $action;

        return $actionModel->save();
    }

    public function likes()
    {
        $likes = NoteMeta::where('annotation_id', $this->id)
                         ->where('meta_key', '=', NoteMeta::TYPE_USER_ACTION)
                         ->where('meta_value', '=', static::ACTION_LIKE)
                         ->count();

        return $likes;
    }

    public function flags()
    {
        $flags = NoteMeta::where('annotation_id', $this->id)
                         ->where('meta_key', '=', NoteMeta::TYPE_USER_ACTION)
                         ->where('meta_value', '=', static::ACTION_FLAG)
                         ->count();

        return $flags;
    }

    /**
     *   Construct link for Annotation.
     *
     *   @param null
     *
     *   @return url
     */
    public function getLink()
    {
        $slug = \DB::table('docs')->where('id', $this->doc_id)->pluck('slug');

        return \URL::to('docs/'.$slug.'#annotation_'.$this->id);
    }

    /**
     *   Create RSS item for Annotation.
     *
     *   @param null
     *
     *   @return array $item
     */
    public function getFeedItem()
    {
        $user = $this->user()->get()->first();

        $item['title'] = $user->display_name."'s Annotation";
        $item['author'] = $user->display_name;
        $item['link'] = $this->getLink();
        $item['pubdate'] = $this->updated_at;
        $item['description'] = $this->text;

        return $item;
    }
}
