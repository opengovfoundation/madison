<?php
use Carbon\Carbon;

class OldAnnotation
{
    const TYPE = 'annotation';

    protected $body;
    protected $es;
    protected $index;

    //Annotation format described at https://github.com/okfn/annotator/wiki/Annotation-format
    public $id;
    public $annotator_schema_version = 'v1.0';
    public $created;
    public $updated;
    public $text;
    public $quote;
    public $uri;
    public $ranges;
    public $user;
    public $consumer = 'Madison';
    public $tags;
    public $permissions;
    public $likes = null;
    public $flags = null;
    public $comments = array();
    public $user_action = null;

    public function __construct($id = null, $source = null)
    {
        $this->id = $id;

        if (isset($source)) {
            foreach ($source as $key => $value) {
                $this->$key = $value;
            }
        }

        $this->index = Config::get('elasticsearch.annotationIndex');

        $this->es = self::connect();
    }

    public function addComment($comment)
    {
        $es = $this->es;

        $comment['created'] = Carbon::now('America/New_York')->toRFC2822String();
        $comment['updated'] = Carbon::now('America/New_York')->toRFC2822String();

        array_push($this->comments, $comment);

        foreach ($this->comments as $index => &$comment) {
            $comment['id'] = $index + 1;
        }

        $retval =  $this->update(false);

        $dbComment = new AnnotationComment();
        $dbComment->text = $comment['text'];
        $dbComment->user_id = $comment['user']['id'];
        $dbComment->id = $comment['id'];
        $dbComment->annotation_id = $this->id;
        $dbComment->save();

        return $retval;
    }

    public function setUserAction($user_id)
    {
        $meta = NoteMeta::where('user_id', $user_id)->where('note_id', '=', $this->id)->where('meta_key', '=', 'user_action');

        if ($meta->count() == 1) {
            $this->user_action = $meta->first()->meta_value;
        }
    }

    public function setActionCounts()
    {
        $this->likes = $this->likes();
        $this->flags = $this->flags();
    }

    public function update($updateTimestamp = true)
    {
        $es = $this->es;

        if ($updateTimestamp) {
            $this->updated = Carbon::now('America/New_York')->toRFC2822String();
        }

        if (isset($body)) {
            foreach ($body as $name => $value) {
                $this->$name = $value;
            }
        }

        if (isset($body['user_action'])) {
            unset($body['user_action']);
        }

        $params = array(
            'index' => $this->index,
            'type'    => self::TYPE,
            'id'    => $this->id,
        );

        $attributes = new ReflectionClass('Annotation');
        $attributes = $attributes->getProperties(ReflectionProperty::IS_PUBLIC);

        $body = array();

        foreach ($attributes as $attribute) {
            $name = $attribute->name;
            $body[$name] = $this->$name;
        }

        $params['body']['doc'] = $body;

        $dbValues = $params['body'];
        $dbValues['search_id'] = $params['id'];

        try {
            $results = $es->update($params);
        } catch (Elasticsearch\Common\Exceptions\Missing404Exception $e) {
            App::abort(404, 'Id not found');
        } catch (Exception $e) {
            App::abort(404, $e->getMessage());
        }

        static::saveAnnotationModel($dbValues);

        return $results;
    }

    public function save()
    {
        $es = $this->es;

        if (!isset($this->body)) {
            throw new Exception('Annotation body not found.  Cannot save.');
        }

        $dbValues = $this->body;

        $this->body['created'] = Carbon::now('America/New_York')->toRFC2822String();
        $this->body['updated'] = Carbon::now('America/New_York')->toRFC2822String();

        $params = array(
            'index'    => $this->index,
            'type'    => self::TYPE,
            'body'    => $this->body,
        );

        $results = $es->index($params);

        $dbValues['search_id'] = $results['_id'];

        static::saveAnnotationModel($dbValues);

        return $results['_id'];
    }

    public function delete()
    {
        $es = self::connect();

        $params = array(
            'index'    => $this->index,
            'type'    => self::TYPE,
            'id'    => $this->id,
        );

        $result = $es->delete($params);

        if ($result['ok'] == true) {
            $dbRow = DBAnnotation::where('search_id', '=', $this->id);
            $dbRow->delete();
        }

        return $result;
    }

    /**
     *	Accessor Functions.
     */
    public function id($id = null)
    {
        return $this->access('id', $id);
    }

    public function created($created = null)
    {
        return $this->access('created', $created);
    }

    public function updated($updated = null)
    {
        return $this->access('updated', $updated);
    }

    public function quote($quote = null)
    {
        return $this->access('quote', $quote);
    }

    public function uri($uri = null)
    {
        return $this->access('uri', $uri);
    }

    public function ranges($ranges = null)
    {
        return $this->access('ranges', $ranges);
    }

    public function tags($tags = null)
    {
        return $this->access('tags', $tags);
    }

    public function permissions($permissions = null)
    {
        return $this->access('permissions', $permissions);
    }

    public function body($body = null)
    {
        return $this->access('body', $body);
    }

    public function text($text = null)
    {
        return $this->access('text', $text);
    }

    public function user($user = null)
    {
        return $this->access('user', $user);
    }

    public function likes($likes = null)
    {
        $likes = NoteMeta::where('note_id', $this->id)->where('meta_key', '=', 'user_action')->where('meta_value', '=', 'like')->count();

        return $likes;
    }

    public function flags($flags = null)
    {
        $flags = NoteMeta::where('note_id', $this->id)->where('meta_key', '=', 'user_action')->where('meta_value', '=', 'flag')->count();

        return $flags;
    }

    public function comments()
    {
        return $this->comments;
    }

    /**
     *	Class Helper Functions.
     **/
    protected function access($attribute, $value)
    {
        if (isset($value)) {
            $this->$attribute = $value;
        } else {
            return $this->$attribute;
        }
    }

    /**
     *	Class Static Functions.
     */
    public static function find($id)
    {
        if (ctype_digit($id)) {
            return DBAnnotation::where('id', '=', $id);
        }

        return DBAnnotation::where('search_id', '=', $id);
    }

    public static function findWithActions($id, $userid)
    {
        $retval = static::find($id);

        $retval->setUserAction($userid);
        $retval->setActionCounts();

        return $retval;
    }

    public static function all($docId)
    {
        $results = DBAnnotation::allByDocId($docId);

        foreach ($results as $annotation) {
            $annotation->setActionCounts();
        }

        return $results;
    }

    public static function allWithActions($docId, $userId)
    {
        $results = DBAnnotation::allByDocId($docId);

        foreach ($results as $annotation) {
            $annotation->setUserAction($userId);
            $annotation->setActionCounts();
        }

        return $results;
    }

    public static function getMetaCount($id, $action)
    {
        $es = self::connect();

        if ($id === null) {
            App::abort(404, 'No note id passed');
        }

        $annotation = static::find($id);
        $action_count = $annotation->$action();

        return $action_count;
    }

    public static function addUserAction($note_id, $user_id, $action)
    {
        if ($note_id == null || $user_id == null || $action == null) {
            throw new Exception('Unable to add user action.');
        }

        $toReturn = array(
                          'action'        => null,
                          'likes'        => -1,
                          'flags'        => -1,
                    );

        $annotation = OldAnnotation::find($note_id);

        $meta = NoteMeta::where('user_id', $user_id)->where('annotation_id', '=', $note_id)->where('meta_key', '=', 'user_action');

        //This user has no actions on this annotation
        if ($meta->count() == 0) {
            $meta = new NoteMeta();
            $meta->user_id = Auth::user()->id;
            $meta->note_id = $note_id;
            $meta->meta_key = 'user_action';
            $meta->meta_value = $action;

            $meta->save();

            $toReturn['action'] = true;
        } elseif ($meta->count() == 1) {
            $meta = $meta->first();

            //This user has already done this action.  Removing the action
            if ($meta->meta_value == $action) {
                $meta->delete();

                $toReturn['action'] = false;
            } else {
                $meta->meta_value = $action;
                $meta->save();

                $toReturn['action'] = true;
            }
        } else {
            throw new Exception('Multiple user actions were found');
        }

        $toReturn['likes'] = $annotation->likes();
        $toReturn['flags'] = $annotation->flags();

        return $toReturn;
    }

    protected static function connect()
    {
        $params['hosts'] = Config::get('elasticsearch.hosts');
        $es = new Elasticsearch\Client($params);

        return $es;
    }

    protected static function saveAnnotationModel(array $input)
    {
        $retval = DBAnnotation::firstOrNew(array(
            'id' => $input['id'],
        ));

        if (isset($input['user'])) {
            $retval->user_id = (int) $input['user']['id'];
        }

        $retval->doc = (int) $input['doc'];
        $retval->id = $input['id'];

        if (isset($input['quote'])) {
            $retval->quote = $input['quote'];
        }

        if (isset($input['text'])) {
            $retval->text = $input['text'];
        }

        if (isset($input['uri'])) {
            $retval->uri = $input['uri'];
        }

        $retval->likes = isset($input['likes']) ? (int) $input['likes'] : 0;
        $retval->flags = isset($input['flags']) ? (int) $input['flags'] : 0;

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
                        'annotation_id' => $input['id'],
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
                    $tag = AnnotationTag::firstOrNew(array(
                            'annotation_id' => $input['id'],
                            'tag' => strtolower($tag),
                    ));

                    $tag->save();
                }
            }

        });

        return $retval;
    }
}
