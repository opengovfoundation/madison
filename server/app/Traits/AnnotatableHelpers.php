<?php

namespace App\Traits;

use App\Models\Annotation;
use App\Models\AnnotationTypes;

trait AnnotatableHelpers
{
    public function annotatableBaseQuery()
    {
        return Annotation
            ::where('annotatable_type', static::class)
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
            ->annotationTypeBaseQuery(AnnotationTypes\Comment::class)
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
            ->annotationTypeBaseQuery(AnnotationTypes\Flag::class)
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
            ->annotationTypeBaseQuery(AnnotationTypes\Like::class)
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
            ->annotationTypeBaseQuery(AnnotationTypes\Seen::class)
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

    public function ranges()
    {
        return $this
            ->annotationTypeBaseQuery(AnnotationTypes\Range::class)
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
            ->annotationTypeBaseQuery(AnnotationTypes\Tag::class)
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

    public function allComments($excludeUserIds = [])
    {
        $comments = $this
            ->comments()
            ->whereNotIn('user_id', $excludeUserIds)
            ->get()
            ;

        foreach ($this->comments as $subcomment) {
            $comments = $comments->merge($subcomment->allComments($excludeUserIds));
        }

        return $comments;
    }

    public function allCommentsCount()
    {
        $commentsCount = $this->comments_count;

        foreach ($this->comments as $subcomment) {
            $commentsCount += $subcomment->allCommentsCount();
        }

        return $commentsCount;
    }

    public function scopeOnlyNotes($query)
    {
        $this->notesQuery($query, 'whereIn');
    }

    public function scopeNotNotes($query)
    {
        $this->notesQuery($query, 'whereNotIn');
    }

    protected function notesQuery($commentsQuery, $condition)
    {
        $commentsQuery->{$condition}('id', function ($query) {
            $query
                ->select('annotatable_id')
                ->from('annotations')
                ->where('annotatable_type', Annotation::class)
                ->where('annotation_type_type', AnnotationTypes\Range::class)
                ;
        });
    }
}
