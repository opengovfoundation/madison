<?php
use Illuminate\Database\Eloquent\Collection;

class Doc extends Eloquent
{
    public static $timestamp = true;

    protected $index;
    protected $softDelete = true;

    protected $appends = ['featured', 'url'];

    const TYPE = 'doc';

    const SPONSOR_TYPE_INDIVIDUAL = "individual";
    const SPONSOR_TYPE_GROUP = "group";

    public function __construct()
    {
        parent::__construct();

        $this->index = Config::get('elasticsearch.annotationIndex');
    }

    public function getEmbedCode()
    {
        $dom = new \DOMDocument();

        $docSrc = URL::to('docs/embed', $this->slug);

        $insertElement = $dom->createElement('div');

        $containerElement = $dom->createElement('iframe');
        $containerElement->setAttribute('id', '__ogFrame');
        $containerElement->setAttribute('width', 300);
        $containerElement->setAttribute('height', 500);
        $containerElement->setAttribute('src', $docSrc);
        $containerElement->setAttribute('frameBorder', 0);

        $insertElement->appendChild($containerElement);

        return $dom->saveHtml($insertElement);
    }

    public function introtext()
    {
        return $this->hasMany('DocMeta')->where('meta_key', '=', 'intro-text');
    }

    public function dates()
    {
        return $this->hasMany('Date');
    }

    public function getFeaturedAttribute()
    {
        $featuredSetting = Setting::where('meta_key', '=', 'featured-doc')->first();

        if ($featuredSetting) {
            return $featuredSetting->meta_value == $this->id;
        }

        return false;
    }

    public function canUserEdit($user)
    {
        $sponsor = $this->sponsor()->first();

        if ($user->hasRole('Admin')) {
            return true;
        }

        switch (true) {
            case $sponsor instanceof User:
                return $sponsor->hasRole('Independent Sponsor');
                break;
            case $sponsor instanceof Group:
                return $sponsor->userHasRole($user, Group::ROLE_EDITOR) || $sponsor->userHasRole($user, Group::ROLE_OWNER);
                break;
            default:
                throw new \Exception("Unknown Sponsor Type");
        }

        return false;
    }

    public function sponsor()
    {
        $sponsor = $this->belongsToMany('Group')->first();

        if (!$sponsor) {
            return $this->belongsToMany('User');
        }

        return $this->belongsToMany('Group');
    }

    public function userSponsor()
    {
        return $this->belongsToMany('User');
    }

    public function groupSponsor()
    {
        return $this->belongsToMany('Group');
    }

    public function statuses()
    {
        return $this->belongsToMany('Status');
    }

    public function categories()
    {
        return $this->belongsToMany('Category');
    }

    public function comments()
    {
        return $this->hasMany('Comment');
    }

    public function getCommentCount()
    {
        return $this->comments()->count();
    }

    public function getCommentCountAttribute()
    {
        return $this->getCommentCount();
    }

    public function getAnnotationCount()
    {
        return $this->annotations()->count();
    }

    public function getAnnotationCountAttribute()
    {
        return $this->getAnnotationCount();
    }

    public function getAnnotationCommentCount()
    {
        return count($this->getAnnotationComments());
    }

    public function getAnnotationCommentCountAttribute()
    {
        return $this->getAnnotationCommentCount();
    }

    public function getUserCount()
    {

        //Return user objects with only user_id property
        $annotationUsers = DB::table('annotations')
            ->where('doc_id', '=', $this->id)
            ->get(['user_id']);

        //Returns user objects with only user_id property
        $commentUsers = DB::table('comments')
            ->where('doc_id', '=', $this->id)
            ->get(['user_id']);

        //Returns user objects with only user_id property
        $annotationCommentUsers = DB::table('annotation_comments')
            ->join('annotations', function ($join) {
                $join->on('annotation_comments.annotation_id', '=', 'annotations.id')
                    ->where('annotations.doc_id', '=', $this->id);
            })
            ->get(['annotation_comments.user_id']);

        //Merge object arrays
        $users = array_merge($annotationUsers, $commentUsers, $annotationCommentUsers);

        //Grab only the user_id attributes
        $userArray = array_map(function ($user) {
            return $user->user_id;
        }, $users);

        //Return the count of the array with uniques filtered
        return count(array_unique($userArray));
    }

    public function getUserCountAttribute()
    {
        return $this->getUserCount();
    }

    /*
     * Add the "count" fields before serializing.
     */
    public function enableCounts()
    {
        $this->appends[] = 'comment_count';
        $this->appends[] = 'annotation_count';
        $this->appends[] = 'annotation_comment_count';
        $this->appends[] = 'user_count';
    }

    public function annotations()
    {
        return $this->hasMany('Annotation');
    }

    public function getAnnotationComments()
    {
        $annotationComments = DB::table('annotation_comments')
            ->join('annotations', function ($join) {
                $join->on('annotation_comments.annotation_id', '=', 'annotations.id')
                    ->where('annotations.doc_id', '=', $this->id);
            })->get();

        return $annotationComments;
    }

    public function getLink()
    {
        return URL::to('docs/'.$this->slug);
    }

    /*
     * Wrapper for automatically getting the url value.
     */
    public function getUrlAttribute()
    {
        return $this->getLink();
    }

    public function content()
    {
        return $this->hasOne('DocContent');
    }

    public function doc_meta()
    {
        return $this->hasMany('DocMeta');
    }

    public static function createEmptyDocument(array $params)
    {
        $defaults = array(
            'content' => "New Document Content",
            'sponsor' => null,
            'sponsorType' => null,
        );

        $params = array_replace_recursive($defaults, $params);

        if (is_null($params['sponsor'])) {
            throw new \Exception("Sponsor Param Required");
        }

        $document = new Doc();

        DB::transaction(function () use ($document, $params) {
            $document->title = $params['title'];
            $document->save();

            switch ($params['sponsorType']) {
                case static::SPONSOR_TYPE_INDIVIDUAL:
                    $document->userSponsor()->sync(array($params['sponsor']));
                    break;
                case static::SPONSOR_TYPE_GROUP:
                    $document->groupSponsor()->sync(array($params['sponsor']));
                    break;
                default:
                    throw new \Exception("Invalid Sponsor Type");
            }

            $template = new DocContent();
            $template->doc_id = $document->id;
            $template->content = "New Document Content";
            $template->save();

            $document->init_section = $template->id;
            $document->save();
        });

        Event::fire(MadisonEvent::NEW_DOCUMENT, $document);

        return $document;
    }

    public function save(array $options = array())
    {
        if (empty($this->slug)) {
            $this->slug = $this->getSlug();
        }

        return parent::save($options);
    }

    public function getSlug()
    {
        if (empty($this->title)) {
            throw new Exception("Can't get a slug - empty title");
        }

        return str_replace(
                    array(' ', '.', ',', '#'),
                    array('-', '', '', ''),
                    strtolower($this->title));
    }

    /*
     * Simple wrapper for our most commonly used joins.
     */
    public static function getEager()
    {
        return Doc::with('categories')->with('sponsor')->with('statuses')->with('dates');
    }

    /*
     * Active documents are much harder to query.  We do this with its own
     * custom query.
     */
    public static function getActive($num, $offset)
    {
        // Defaults to limit 10 because of the expense here.
        if (!$num) {
            $num = 10;
        }

        if (!$offset) {
            $offset = 0;
        }

        $docIds = DB::select(
            DB::raw(
                "SELECT doc_id, SUM(num) AS total FROM (
                    SELECT doc_id, COUNT(*) AS num
                        FROM annotations
                        GROUP BY doc_id
                    UNION ALL
                    SELECT doc_id, COUNT(*) AS num
                        FROM comments
                        GROUP BY doc_id
                    UNION ALL
                    SELECT annotations.doc_id, COUNT(*) AS num
                        FROM annotation_comments
                        INNER JOIN annotations
                        ON annotation_comments.annotation_id = annotations.id
                        GROUP BY doc_id

                ) total_count
                GROUP BY doc_id
                ORDER BY total DESC
                LIMIT :offset, :limit"
            ),
            array(
                ':offset' => $offset,
                ':limit' => $num
            )
        );

        $docArray = [];

        //Create array of [id => total] for each document
        foreach ($docIds as $docId) {
            $docArray[$docId->doc_id] = $docId->total;
        }
        $docs = false;

        if (count($docArray) > 0) {
            //Grab out most active documents
            $docs = Doc::getEager()->whereIn('id', array_keys($docArray))->get();

            //Set the sort value to the total count
            foreach ($docs as $doc) {
                $doc->participationTotal = $docArray[$doc->id];
            }

            //Sort by the sort value descending
            $docs = $docs->sortByDesc('participationTotal');
        }

        return $docs;
    }

    public static function allOwnedBy($userId)
    {
        $rawDocs = DB::select(
            DB::raw(
                "SELECT docs.* FROM
					(SELECT doc_id
					   FROM doc_group, group_members
					  WHERE doc_group.group_id = group_members.group_id
					    AND group_members.user_id = ?
					UNION ALL
					 SELECT doc_id
					   FROM doc_user
					  WHERE doc_user.user_id = ?
				    ) DocUnion, docs
				  WHERE docs.id = DocUnion.doc_id
			   GROUP BY docs.id"
            ),
            array($userId, $userId)
        );

        $results = new Collection();

        foreach ($rawDocs as $row) {
            $obj = new static();

            foreach ($row as $key => $val) {
                $obj->$key = $val;
            }

            $results->add($obj);
        }

        return $results;
    }

    public static function getAllValidSponsors()
    {
        $userMeta = UserMeta::where('meta_key', '=', UserMeta::TYPE_INDEPENDENT_SPONSOR)
                            ->where('meta_value', '=', 1)
                            ->get();

        $groups = Group::where('status', '=', Group::STATUS_ACTIVE)
                        ->get();

        $results = new Collection();

        $userIds = array();

        foreach ($userMeta as $m) {
            $userIds[] = $m->user_id;
        }

        if (!empty($userIds)) {
            $users = User::whereIn('id', $userIds)->get();

            foreach ($users as $user) {
                $row = array(
                        'display_name' => "{$user->fname} {$user->lname}",
                        'sponsor_type' => 'individual',
                        'id' => $user->id,
                );

                $results->add($row);
            }
        }

        foreach ($groups as $group) {
            $row = array(
                    'display_name' => $group->display_name,
                    'sponsor_type' => 'group',
                    'id' => $group->id,
            );

            $results->add($row);
        }

        return $results;
    }

    public function get_file_path($format = 'markdown')
    {
        switch ($format) {
            case 'html' :
                $path = 'html';
                $ext = '.html';
                break;

            case 'markdown':
            default:
                $path = 'md';
                $ext = '.md';
        }

        $filename = $this->slug.$ext;
        $path = implode(DIRECTORY_SEPARATOR, array(storage_path(), 'docs', $path, $filename));

        return $path;
    }

    public function indexContent($doc_content)
    {
        $es = self::esConnect();

        File::put($this->get_file_path('markdown'), $doc_content->content);

        File::put($this->get_file_path('html'),
            Markdown::render($doc_content->content)
        );

        $body = array(
            'id' => $this->id,
            'content' => $doc_content->content,
        );

        $params = array(
            'index'    => $this->index,
            'type'    => self::TYPE,
            'id'    => $this->id,
            'body'    => $body,
        );

        $results = $es->index($params);
    }

    public function get_content($format = null)
    {
        $path = $this->get_file_path($format);

        try {
            return File::get($path);
        } catch (Illuminate\Filesystem\FileNotFoundException $e) {
            $content = DocContent::where('doc_id', '=', $this->attributes['id'])->where('parent_id')->first()->content;

            if ($format == 'html') {
                $content = Markdown::render($content);
            }

            return $content;
        }
    }

    public static function search($query)
    {
        $es = self::esConnect();

        $params['index'] = Config::get('elasticsearch.annotationIndex');
        $params['type'] = self::TYPE;
        $params['body']['query']['filtered']['query']['query_string']['query'] = $query;

        return $es->search($params);
    }

    public static function esConnect()
    {
        $esParams['hosts'] = Config::get('elasticsearch.hosts');
        $es = new Elasticsearch\Client($esParams);

        return $es;
    }

    public static function findDocBySlug($slug = null)
    {
        //Retrieve requested document
        $doc = static::where('slug', $slug)
                     ->with('statuses')
                     ->with('userSponsor')
                     ->with('groupSponsor')
                     ->with('categories')
                     ->with('dates')
                     ->first();

        if (!isset($doc)) {
            return;
        }

        return $doc;
    }
}
