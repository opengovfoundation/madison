<?php

namespace App\Models;

use App\Models\Doc;
use App\Services\UniqId;
use App\Traits\AnnotatableHelpers;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use URL;

class Annotation extends Model implements ActivityInterface
{
    use SoftDeletes;
    use AnnotatableHelpers;

    const ANNOTATION_CONSUMER = "Madison";

    const ANNOTATABLE_TYPE = 'annotation';

    const TYPE_COMMENT = 'comment';
    const TYPE_FLAG = 'flag';
    const TYPE_LIKE = 'like';
    const TYPE_RANGE = 'range';
    const TYPE_SEEN = 'seen';
    const TYPE_TAG = 'tag';
    const TYPE_HIDDEN = 'hidden';
    const TYPE_RESOLVED = 'resolved';

    const SUBTYPE_NOTE = 'note';

    protected $table = 'annotations';
    protected $fillable = ['data', 'user_id', 'annotation_subtype', 'str_id'];
    protected $dates = ['deleted_at'];
    protected $casts = [
        'data' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('visible', function (Builder $builder) {
            $builder->visible();
        });

        static::creating(function($annotation) {
            if (!isset($annotation->str_id)) {
                $annotation->str_id = UniqId::genB64();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function permissions()
    {
        return $this->hasMany('App\Models\AnnotationPermission', 'annotation_id');
    }

    public function annotations()
    {
        return $this->morphMany('App\Models\Annotation', 'annotatable');
    }

    public function annotatable()
    {
        return $this->morphTo();
    }

    public function annotationType()
    {
        return $this->morphTo();
    }

    /**
     * The ultimate non-Annotation target for this Annotation. Currently
     * the only possibility is a Document.
     *
     * @return Doc
     */
    public function rootAnnotatable()
    {
        return $this->morphTo();
    }

    public function delete()
    {
        DB::transaction(function () {
            $this->annotations()->withoutGlobalScope('visible')->delete();
            $this->permissions()->delete();
            return parent::delete();
        });
    }

    /**
     * Construct link for the Annotation.
     *
     * @return URL|null
     */
    public function getLink()
    {
        switch ($this->annotation_type_type) {
            case static::TYPE_COMMENT:
                $root = $this->rootAnnotatable;

                if ($root instanceof Doc) {
                    return URL::to('documents/'.$root->slug.'#'.$this->str_id);
                }

                return null;
            default:
                return null;
        }
    }

    /**
     * Create RSS item for the Annotation.
     *
     * @return array
     */
    public function getFeedItem()
    {
        switch ($this->annotation_type_type) {
            case static::TYPE_COMMENT:
                $description = $this->annotationType->content;
                break;
            default:
                $description = 'Unknown';
        }

        return [
            'title' => $this->user->display_name."'s Annotation",
            'author' => $this->user->display_name,
            'link' => $this->getLink(),
            'pubdate' => $this->updated_at,
            'description' => $description,
            'content' => '',
            'enclosure' => [],
        ];
    }

    public function isNote()
    {
        if ($this->annotation_type_type != static::TYPE_COMMENT) {
            return false;
        }

        return $this->annotation_subtype === static::SUBTYPE_NOTE;
    }

}
