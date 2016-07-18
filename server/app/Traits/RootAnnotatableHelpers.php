<?php

namespace App\Traits;

use App\Models\Annotation;

trait RootAnnotatableHelpers
{
    use AnnotatableHelpers;

    public function rootAnnotatableBaseQuery()
    {
        return Annotation
            ::where('root_annotatable_type', static::ANNOTATABLE_TYPE)
            ->where('root_annotatable_id', $this->id)
            ;
    }

    public function rootAnnotationTypeBaseQuery($class)
    {
        return $this
            ->rootAnnotatableBaseQuery()
            ->where('annotation_type_type', $class)
            ;
    }

    public function allComments()
    {
        return $this
            ->rootAnnotationTypeBaseQuery(Annotation::TYPE_COMMENT)
            ;
    }

    public function getAllCommentsAttribute()
    {
        return $this->allComments()->get();
    }

    public function getAllCommentsCountAttribute()
    {
        return $this->allComments()->count();
    }
}
