<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Category;
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
        Doc::saving(function($doc) {
            if (!isset($doc->slug)) $doc->slug = Doc::makeSlug($doc->title);
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
                case $sponsor instanceof Group:
                    return $sponsor->userHasRole($user, Group::ROLE_EDITOR) || $sponsor->userHasRole($user, Group::ROLE_OWNER);
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
            [Doc::PUBLISH_STATE_PUBLISHED, Doc::PUBLISH_STATE_PRIVATE]
        )) {
            return true;
        }

        if ($user) {
            if ($user->hasRole('Admin')) {
                return true;
            }

            if (
                $this->publish_state == Doc::PUBLISH_STATE_UNPUBLISHED
                && $this->canUserEdit($user)
            ) {
                return true;
            }
        }

        return false;
    }

    public function sponsors()
    {
        return $this->belongsToMany('App\Models\Group');
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
        return Doc::with('categories')
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

        $docs = false;

        if (count($docsInfo) > 0) {
            //Grab out most active documents
            $docs = Doc::getEager()->whereIn('id', array_keys($docsInfo))->get();

            //Set the sort value to the total count
            foreach ($docs as $doc) {
                $doc->participationTotal = $docsInfo[$doc->id];
            }

            //Sort by the sort value descending
            $docs = $docs
                ->sortByDesc('participationTotal')
                ->slice($offset, $num)
                ;
        }

        return $docs;
    }

    public static function allOwnedBy($userId)
    {
        $rawDocs = \DB::select(
            \DB::raw(
                "SELECT docs.* FROM
					(SELECT doc_id
					   FROM doc_group, group_members
					  WHERE doc_group.group_id = group_members.group_id
					    AND group_members.user_id = ?
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
        return Group
            ::select('id', 'display_name')
            ->where('status', Group::STATUS_ACTIVE)
            ->get()
            ;
    }

    public static function validPublishStates()
    {
         return [
            'all',
            self::PUBLISH_STATE_PUBLISHED,
            self::PUBLISH_STATE_UNPUBLISHED,
            self::PUBLISH_STATE_PRIVATE,
            self::PUBLISH_STATE_DELETED_ADMIN,
            self::PUBLISH_STATE_DELETED_USER
        ];
    }

    public static function validPublishStatesRoutePattern()
    {
        $valid_states = self::validPublishStates();

        foreach ($valid_states as $idx => $state) {
            $valid_states[$idx] = str_replace('-', '\-', $state);
        }

        return '(' . implode('|', $valid_states) . ')';
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
            // user belongs to group as EDITOR or OWNER
            $q->whereHas('members', function($q) use ($userId) {
                $q->where('user_id', '=', $userId);
                $q->whereIn('role', [Group::ROLE_EDITOR, Group::ROLE_OWNER]);
            });
        });
    }

    public function getImagePath($image = '', $size = null)
    {
        return 'doc-' . $this->id . '/' . $this->addSizeToImage($image, $size);
    }

    public function getImageUrl($image = '', $size = null)
    {
        return '/api/docs/' . $this->id . '/images/' . $this->addSizeToImage($image, $size);
    }

    public function getImagePathFromUrl($image, $unsized = false)
    {
        $image_url = str_replace('/api/docs/' . $this->id . '/images/',
            'doc-' . $this->id . '/',
            $image);

        // Remove any sizing.
        if($unsized)
        {
            $image_url = $this->removeSizeFromImage($image_url);
        }

        return $image_url;
    }

    // This should work for any image - url, full path, or just the base name.
    public function addSizeToImage($image, $size = null) {
        $size = $this->parseSizeName($size);

        // Just get the name part of the image.  We don't want to accidentally
        // break up any paths that happen to have a dot in them.
        $imageName = basename($image);

        if($size && preg_match('/^[0-9]{1,4}x[0-9]{1,4}$/', $size)) {
            // Insert the size string before the extension.
            // Only split this into two parts, in case of multiple extensions.
            $imageParts = explode('.', $imageName, 2);
            $newImageName = $imageParts[0] . '-' . $size . '.' . $imageParts[1];

            // Replace the old image name with the new image name.
            // This is more reliable than splitting the path up.
            $image = str_replace($imageName, $newImageName, $image);
        }
        return $image;
    }

    public function removeSizeFromImage($image, $sizes = null)
    {
        if(!$sizes)
        {
            $sizes = config('madison.image_sizes');
        }

        // Just get the name part of the image.  We don't want to accidentally
        // break up any paths that happen to have a dot in them.
        $imageName = basename($image);

        // Split on the first period, the beginning of the extension.
        // Only split this into two parts, in case of multiple extensions.
        $imageParts = explode('.', $imageName, 2);

        // Remove all possible image sizes.
        foreach ($sizes as $name=>$size)
        {
            $sizeName = $size['width'] . 'x' . $size['height'];
            $imageParts[0] = preg_replace('/-'.$sizeName.'$/', '', $imageParts[0]);
        }
        $newImageName = join('.', $imageParts);

        // Replace the old image name with the new image name.
        // This is more reliable than splitting the path up.
        return str_replace($imageName, $newImageName, $image);
    }

    // We allow a size array to be passed instead of a string, so we build that here.
    private function parseSizeName($size)
    {
        if(is_array($size) && isset($size['width'], $size['height']))
        {
            $size = $size['width'] . 'x' . $size['height'];
        }
        return $size;
    }
}
