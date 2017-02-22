<?php

namespace App\Traits;

use App\Models\Annotation;

trait AnnotatableHelpers
{
    public function annotatableBaseQuery()
    {
        return Annotation
            ::where('annotatable_type', static::ANNOTATABLE_TYPE)
            ->where('annotatable_id', $this->id)
            ;
    }

    public function annotationTypeBaseQuery($class)
    {
        return $this
            ->annotatableBaseQuery()
            ->where('annotation_type_type', $class)
            ;
    }

    public function comments()
    {
        return $this
            ->annotationTypeBaseQuery(Annotation::TYPE_COMMENT)
            ;
    }

    public function getCommentsAttribute()
    {
        return $this->comments()->get();
    }

    public function getCommentsCountAttribute()
    {
        return $this->comments()->count();
    }

    public function flags()
    {
        return $this
            ->annotationTypeBaseQuery(Annotation::TYPE_FLAG)
            ;
    }

    public function getFlagsAttribute()
    {
        return $this->flags()->get();
    }

    public function getFlagsCountAttribute()
    {
        return $this->flags()->count();
    }

    public function likes()
    {
        return $this
            ->annotationTypeBaseQuery(Annotation::TYPE_LIKE)
            ;
    }

    public function getLikesAttribute()
    {
        return $this->likes()->get();
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    public function seens()
    {
        return $this
            ->annotationTypeBaseQuery(Annotation::TYPE_SEEN)
            ;
    }

    public function getSeensAttribute()
    {
        return $this->seens()->get();
    }

    public function getSeensCountAttribute()
    {
        return $this->seens()->count();
    }

    public function hiddens()
    {
        return $this
            ->annotationTypeBaseQuery(Annotation::TYPE_HIDDEN)
            ;
    }

    public function isHidden()
    {
        return $this->hiddens()->count() > 0;
    }

    public function resolves()
    {
        return $this
            ->annotationTypeBaseQuery(Annotation::TYPE_RESOLVED)
            ;
    }

    public function isResolved()
    {
        return $this->resolves()->count() > 0;
    }

    public function ranges()
    {
        return $this
            ->annotationTypeBaseQuery(Annotation::TYPE_RANGE)
            ;
    }

    public function getRangesAttribute()
    {
        return $this->ranges()->get();
    }

    public function getRangesCountAttribute()
    {
        return $this->ranges()->count();
    }

    public function tags()
    {
        return $this
            ->annotationTypeBaseQuery(Annotation::TYPE_TAG)
            ;
    }

    public function getTagsAttribute()
    {
        return $this->tags()->get();
    }

    public function getTagsCountAttribute()
    {
        return $this->tags()->count();
    }

    public function allCommentsRecursive($excludeUserIds = [])
    {
        $comments = $this
            ->comments()
            ->whereNotIn('user_id', $excludeUserIds)
            ->get()
            ;

        foreach ($this->comments as $subcomment) {
            $comments = $comments->merge($subcomment->allCommentsRecursive($excludeUserIds));
        }

        return $comments;
    }

    public function allCommentsRecursiveCount()
    {
        $commentsCount = $this->comments_count;

        foreach ($this->comments as $subcomment) {
            $commentsCount += $subcomment->allCommentsRecursiveCount();
        }

        return $commentsCount;
    }

    public function scopeOnlyNotes($query)
    {
        $query->where('annotation_subtype', Annotation::SUBTYPE_NOTE);
    }

    public function scopeNotNotes($query)
    {
        $query->whereRaw('COALESCE(annotation_subtype <> ?, TRUE)', [Annotation::SUBTYPE_NOTE]);
    }

    public function scopeVisible($query)
    {
        $query->notHidden()->notRepliesToHidden();
    }

    public function scopeNotHidden($query)
    {
        $query->whereNotIn('id', function ($query) {
            $query
                ->select('annotatable_id')
                ->from('annotations')
                ->where('annotation_type_type', '=', Annotation::TYPE_HIDDEN)
                ->whereNull('deleted_at')
                ;
        });
    }

    public function scopeNotRepliesToHidden($query)
    {
        $query->whereNotIn('id', function ($query) {
            $query
                ->select('id')
                ->from('annotations')
                ->where('annotatable_type', '=', Annotation::ANNOTATABLE_TYPE)
                ->where('annotation_type_type', '=', Annotation::TYPE_COMMENT)
                ->whereIn('annotatable_id', function ($query) {
                    $query
                        ->select('id')
                        ->from('annotations')
                        ->whereIn('id', function ($query) {
                            $query
                                ->select('annotatable_id')
                                ->from('annotations')
                                ->where('annotation_type_type', '=', Annotation::TYPE_HIDDEN)
                                ->whereNull('deleted_at')
                                ;
                        })
                        ->whereNull('deleted_at')
                        ;
                })
                ;
        });
    }
}
