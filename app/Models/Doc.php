<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Category;
use App\Models\DocContent as DocumentContent;
use App\Traits\RootAnnotatableHelpers;
use Event;
use Exception;
use URL;
use Cache;
use DB;

class Doc extends Model
{
    use SoftDeletes;
    use RootAnnotatableHelpers;

    public static $timestamp = true;

    protected $dates = ['deleted_at'];
    protected $appends = ['featured', 'url'];

    protected $fillable = ['discussion_state', 'publish_state', 'title', 'slug'];

    const TYPE = 'doc';
    const ANNOTATABLE_TYPE = 'doc';

    const PUBLISH_STATE_PUBLISHED = 'published';
    const PUBLISH_STATE_UNPUBLISHED = 'unpublished';
    const PUBLISH_STATE_PRIVATE = 'private';
    const PUBLISH_STATE_DELETED_ADMIN = 'deleted-admin';
    const PUBLISH_STATE_DELETED_USER = 'deleted-user';

    const DISCUSSION_STATE_OPEN = 'open';
    const DISCUSSION_STATE_CLOSED = 'closed';
    const DISCUSSION_STATE_HIDDEN = 'hidden';

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }

    public static function boot()
    {
        parent::boot();

        /**
         * Set default value for slug
         */
        static::saving(function($doc) {
            if (empty($doc->slug)) $doc->slug = static::makeSlug($doc->title);
        });

        static::created(function ($document) {
            $starter = new DocumentContent();
            $starter->doc_id = $document->id;
            $starter->content = "New Document Content";
            $starter->save();

            $document->init_section = $starter->id;
            $document->save();
        });

        static::deleted(function ($document) {
            $document->removeAsFeatured();
        });
    }

    public function annotations()
    {
        return $this->morphMany('App\Models\Annotation', 'annotatable');
    }

    public function allAnnotations()
    {
        return $this->morphMany('App\Models\Annotation', 'root_annotatable');
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
        return $this->hasMany('App\Models\DocMeta')->where('meta_key', '=', 'intro-text');
    }

    public function setIntroText($value)
    {
        $introtext = DocMeta
            ::where('meta_key', '=', 'intro-text')
            ->where('doc_id', $this->id)
            ->first();

        if ($introtext) {
            $introtext->meta_value = $value;
        } else {
            $introtext = new DocMeta();
            $introtext->doc_id = $this->id;
            $introtext->meta_key = 'intro-text';
            $introtext->meta_value = $value;
        }

        $introtext->save();
    }

    public function dates()
    {
        return $this->hasMany('App\Models\Date');
    }

    public function getFeaturedAttribute()
    {
        $featuredSetting = Setting::where('meta_key', '=', 'featured-doc')->first();

        if ($featuredSetting) {
            $docIds = explode(',', $featuredSetting->meta_value);
            return in_array($this->id, $docIds);
        }

        return false;
    }

    public function canUserEdit($user)
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        foreach ($this->sponsors as $sponsor) {
            switch (true) {
                case $sponsor instanceof Sponsor:
                    return $sponsor->userHasRole($user, Sponsor::ROLE_EDITOR) || $sponsor->userHasRole($user, Sponsor::ROLE_OWNER);
                    break;
                default:
                    throw new \Exception("Unknown Sponsor Type");
            }
        }

        return false;
    }

    public function canUserView($user)
    {
        if (in_array(
            $this->publish_state,
            [static::PUBLISH_STATE_PUBLISHED, static::PUBLISH_STATE_PRIVATE]
        )) {
            return true;
        }

        if ($user) {
            if ($user->hasRole('Admin')) {
                return true;
            }

            if (
                $this->publish_state == static::PUBLISH_STATE_UNPUBLISHED
                && $this->canUserEdit($user)
            ) {
                return true;
            }
        }

        return false;
    }

    public function sponsors()
    {
        return $this->belongsToMany('App\Models\Sponsor');
    }

    // We need to declare this in order to dynamically add 'sponsors' to
    // $appends in enableSponsors()
    public function getSponsorsAttribute()
    {
        return $this->sponsors()->get();
    }

    public function statuses()
    {
        return $this->belongsToMany('App\Models\Status');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category');
    }

    public function syncCategories($categoriesArray)
    {
        if (empty($categoriesArray)) {
            $this->categories()->sync([]);
            return;
        }

        $categoriesToSync = [];

        foreach ($categoriesArray as $category) {
            // check if category has an id property
            if (!isset($category['id'])) {
                // Make sure category with same name doesn't already exist
                $existingCategory = Category::where('name', $category['name'])->first();

                if ($existingCategory) {
                    $categoriesToSync[] = $existingCategory->id;
                } else {
                    $category = new Category(['name' => $category['name']]);
                    $category->save();
                    $categoriesToSync[] = $category->id;
                }
            } else {
                $categoriesToSync[] = $category['id'];
            }
        }

        $this->categories()->sync($categoriesToSync);
    }

    public function getPages()
    {
        return $this->content()->count();
    }

    public function getPagesAttribute()
    {
        return $this->getPages();
    }

    public function getCommentCountAttribute()
    {
        return $this
            ->allComments()
            ->notNotes()
            ->count()
            ;
    }

    public function getNoteCountAttribute()
    {
        return $this
            ->allComments()
            ->onlyNotes()
            ->count()
            ;
    }

    public function getUserCount()
    {
        return $this->allComments()->count(DB::raw('DISTINCT user_id'));
    }

    public function getUserCountAttribute()
    {
        return $this->getUserCount();
    }

    public function getSupportAttribute()
    {
        return DocMeta::where('meta_key', 'support')->where('meta_value', '1')->where('doc_id', $this->id)->count();
    }

    public function getOpposeAttribute()
    {
        return DocMeta::where('meta_key', 'support')->where('meta_value', '')->where('doc_id', $this->id)->count();
    }

    /*
     * Add the "count" fields before serializing.
     */
    public function enableCounts()
    {
        $this->appends[] = 'pages';
        $this->appends[] = 'comment_count';
        $this->appends[] = 'note_count';
        $this->appends[] = 'user_count';
        $this->appends[] = 'support';
        $this->appends[] = 'oppose';
    }

    public function enableSponsors()
    {
        $this->appends[] = 'sponsors';
    }

    public function enableIntrotext()
    {
        $this->appends[] = 'introtext';
    }

    public function getIntrotextAttribute()
    {
        if ($this->introtext()->count()) {
            return $this->introtext()->first()->meta_value;
        } else {
            return null;
        }
    }

    public function getSponsorIdsAttribute()
    {
        $ids = [];
        foreach ($this->sponsors as $sponsor)
        {
            foreach ($sponsor->members as $member)
            {
                $ids[] = $member->user_id;
            }
        }

        return $ids;
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
        return $this->hasMany('App\Models\DocContent');
    }

    public function fullContentHtml()
    {
        return $this->content()
            ->orderBy('page')
            ->get()
            ->reduce(function ($fullContent, $content) {
                return $fullContent . $content->html();
            }, '')
            ;
    }

    public function doc_meta()
    {
        return $this->hasMany('App\Models\DocMeta');
    }

    /**
     * TODO: Sponsor handling here is off. Is this method needed even?
     * -- Only used in database seeding currently.
     */
    public static function createEmptyDocument(array $params)
    {
        $defaults = array(
            'content' => "New Document Content",
            'sponsor' => null,
            'publish_state' => 'unpublished'
        );

        $params = array_replace_recursive($defaults, $params);

        if (is_null($params['sponsor'])) {
            throw new \Exception("Sponsor Param Required");
        }

        $document = new Doc();

        \DB::transaction(function () use ($document, $params) {
            $document->title = $params['title'];
            $document->publish_state = $params['publish_state'];
            $document->save();

            $document->sponsors()->sync([$params['sponsor']]);

            $template = new DocContent();
            $template->doc_id = $document->id;
            $template->content = $params['content'];
            $template->save();

            $document->init_section = $template->id;
            $document->save();
        });

        return $document;
    }

    public static function prepareCountsAndDates($docs = [])
    {
        $return_docs = [];

        if ($docs) {
            foreach ($docs as $doc) {
                $doc->enableCounts();
                $doc->enableSponsors();

                $return_doc = $doc->toArray();

                $return_doc['updated_at'] = $doc->updated_at->toRfc3339String();
                $return_doc['created_at'] = $doc->created_at->toRfc3339String();

                if (!empty($doc->deleted_at)) {
                    $return_doc['deleted_at'] = $doc->deleted_at->toRfc3339String();
                }

                $return_docs[] = $return_doc;
            }
        }

        return $return_docs;
    }

    /**
     * Class method for converting a title into a valid slug
     */
    public static function makeSlug($title)
    {
        if (empty($title)) {
            throw new Exception("Can't get a slug - empty title");
        }

        return str_slug($title, '-');
    }

    /*
     * Simple wrapper for our most commonly used joins.
     */
    public static function getEager()
    {
        return static::with('categories')
            ->with('sponsors')
            ->with('statuses')
            ->with('dates');
    }

    /*
     * Active documents are much harder to query.  We do this with its own
     * custom query.
     */
    public static function getActive($num = 10, $offset = 0)
    {
        $docsInfo = static::getActiveInfo();

        $docs = false;

        if (count($docsInfo) > 0) {
            //Grab out most active documents
            $docs = static::getEager()->whereIn('id', static::getActiveIds())->get();

            //Sort by the sort value descending
            $docs = static::sortByActive($docs)
                ->slice($offset, $num)
                ;
        }

        return $docs;
    }

    public static function getActiveInfo()
    {
        $docsInfo = Cache::get('active-docs');

        if (empty($docsInfo)) {
            $docs = static
                ::where('publish_state', static::PUBLISH_STATE_PUBLISHED)
                ->where('discussion_state', static::DISCUSSION_STATE_OPEN)
                ->where('is_template', false)
                ->get()
                ;

            $docsInfo = [];

            //Create array of [id => total] for each document
            foreach ($docs as $doc) {
                $docsInfo[$doc->id] = $doc->all_comments_count;
            }

            Cache::put('active-docs', $docsInfo, 1440);
        }

        return $docsInfo;
    }


    public static function getActiveIds()
    {
        return array_keys(static::getActiveInfo());
    }

    public static function sortByActive(Collection $docs)
    {
        $activeInfo = static::getActiveInfo();

        return $docs->sortByDesc(function ($doc) use ($activeInfo) {
            return !empty($activeInfo[$doc->id]) ? $activeInfo[$doc->id] : 0;
        });
    }

    public static function getFeatured()
    {
        $featuredSetting = Setting::where('meta_key', '=', 'featured-doc')->first();

        if ($featuredSetting) {
            // Make sure our featured document can be viewed by the public.
            $featuredIds = explode(',', $featuredSetting->meta_value);
            $docQuery = static::with('categories')
                ->with('sponsors')
                ->with('statuses')
                ->with('dates')
                ->whereIn('id', $featuredIds)
                ->where('is_template', '!=', '1')
                ->where('publish_state', '=', static::PUBLISH_STATE_PUBLISHED);

            $docs = $docQuery->get();

            if ($docs) {
                // Reorder based on our previous list.
                $tempDocs = [];
                $orderList = array_flip($featuredIds);
                foreach ($docs as $key=>$doc) {
                    $tempDocs[(int) $orderList[$doc->id]] = $doc;
                }

                // If you set the key of an array value as we do above,
                // PHP will internally store the object as an associative
                // array (hash), not as a list, and will return the elements
                // in the order assigned, not by the key order.
                // This means our attempt to re-order the object will fail.
                // The line below will restore the order. Ugh.
                ksort($tempDocs);
                $docs = $tempDocs;
            }
        }

        // If we don't have a document, just find anything recent.
        if (empty($docs)) {
            $docs = [
                static::with('categories')
                ->with('sponsors')
                ->with('statuses')
                ->with('dates')
                ->where('publish_state', '=', static::PUBLISH_STATE_PUBLISHED)
                ->where('is_template', '!=', '1')
                ->orderBy('created_at', 'desc')
                ->first()
            ];
        }

        $return_docs = [];
        foreach ($docs as $key => $doc) {
            $doc->enableCounts();
            $doc->enableSponsors();
            $return_doc = $doc->toArray();

            $return_doc['introtext'] = $doc->introtext()->first()['meta_value'];
            $return_doc['updated_at'] = date('c', strtotime($return_doc['updated_at']));
            $return_doc['created_at'] = date('c', strtotime($return_doc['created_at']));
            $return_doc['featuredImageUrl'] = $doc->getFeaturedImageUrl();

            $return_docs[] = $return_doc;
        }

        return $return_docs;
    }

    public static function allOwnedBy($userId)
    {
        $rawDocs = \DB::select(
            \DB::raw(
                "SELECT docs.* FROM
                    (SELECT doc_id
                       FROM doc_sponsor, sponsor_members
                      WHERE doc_sponsor.sponsor_id = sponsor_members.sponsor_id
                        AND sponsor_members.user_id = ?
                    ) DocUnion, docs
                  WHERE docs.id = DocUnion.doc_id
               GROUP BY docs.id"
            ),
            [$userId]
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
        return Sponsor
            ::select('id', 'display_name')
            ->where('status', Sponsor::STATUS_ACTIVE)
            ->get()
            ;
    }

    public static function validPublishStates()
    {
         return [
            self::PUBLISH_STATE_PUBLISHED,
            self::PUBLISH_STATE_UNPUBLISHED,
            self::PUBLISH_STATE_PRIVATE,
            self::PUBLISH_STATE_DELETED_ADMIN,
            self::PUBLISH_STATE_DELETED_USER
        ];
    }

    public static function validDiscussionStates()
    {
        return [
            self::DISCUSSION_STATE_OPEN,
            self::DISCUSSION_STATE_CLOSED,
            self::DISCUSSION_STATE_HIDDEN
        ];
    }

    /**
     * Scopes!
     * -----------------------------------------------
     */

    /**
     * Scope to return documents that the user has edit access to
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBelongsToUser($query, $userId)
    {
        return $query->whereHas('sponsors', function($q) use ($userId) {
            // user belongs to sponsor as EDITOR or OWNER
            $q->whereHas('members', function($q) use ($userId) {
                $q->where('user_id', '=', $userId);
                $q->whereIn('role', [Sponsor::ROLE_EDITOR, Sponsor::ROLE_OWNER]);
            });
        });
    }

    /**
     * Scope to get most recently active public documents with open discussion.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMostRecentPublicWithOpenDiscussion($query, $howMany = 6)
    {
        return $query
            ->orderBy('updated_at', 'DESC')
            ->where('discussion_state', static::DISCUSSION_STATE_OPEN)
            ->where('publish_state', static::PUBLISH_STATE_PUBLISHED)
            ->where('is_template', '!=', '1')
            ->take($howMany);
    }


    public function setAsFeatured()
    {
        $featuredSetting = static::getFeaturedDocumentsSetting();
        $docIds = explode(',', $featuredSetting->meta_value);

        if (!in_array($this->id, $docIds)) {
            array_unshift($docIds, $this->id);
        }

        $featuredSetting->meta_value = join(',', $docIds);
        $featuredSetting->save();
    }

    public function removeAsFeatured()
    {
        $featuredSetting = static::getFeaturedDocumentsSetting();
        $docIds = explode(',', $featuredSetting->meta_value);

        if (in_array($this->id, $docIds)) {
            $docIds = array_diff($docIds, [$this->id]);
        }

        $featuredSetting->meta_value = join(',', $docIds);
        $featuredSetting->save();
    }

    public static function getFeaturedDocumentsSetting()
    {
        // firstOrNew() is not working for some reason, so we do it manually.
        $featuredSetting = Setting::where(['meta_key' => 'featured-doc'])->first();
        if (!$featuredSetting) {
            $featuredSetting = new Setting;
            $featuredSetting->meta_key = 'featured-doc';
        }

        return $featuredSetting;
    }

    public function getFeaturedImageUrl()
    {
        if ($this->featuredImage) {
            return route('documents.images.show', [
                'document' => $this->slug,
                'image' => $this->featuredImage,
                'size' => 'featured',
            ]);
        }

        return '/img/default-featured.jpg';
    }
}
