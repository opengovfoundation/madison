<?php

namespace App\Services;

use App\Models\Annotation;
use App\Models\AnnotationTypes;
use App\Models\Doc;

class Annotations
{
    public function createAnnotation($target, $user, $type, $data)
    {
        switch ($type) {
            case Annotation::TYPE_COMMENT:
                return $this->createAnnotationComment($target, $user, $data);
            case Annotation::TYPE_FLAG:
                return $this->createAnnotationFlag($target, $user, $data);
            case Annotation::TYPE_LIKE:
                return $this->createAnnotationLike($target, $user, $data);
            case Annotation::TYPE_RANGE:
                return $this->createAnnotationRange($target, $user, $data);
            case Annotation::TYPE_SEEN:
                return $this->createAnnotationSeen($target, $user, $data);
            case Annotation::TYPE_TAG:
                return $this->createAnnotationTag($target, $user, $data);
            default:
                throw new \InvalidArgumentException('Annotation type not recognized');
        }
    }

    public function createBaseAnnotation($target, $user, $data)
    {
        $annotation = Annotation::create([
            'user_id' => $user->id,
            'annotation_subtype' => $data['subtype'],
        ]);

        $target->annotations()->save($annotation);

        $rootTarget = null;
        if ($target instanceof Doc) {
            $rootTarget = $target;
        } elseif ($target instanceof Annotation) {
            $rootTarget = $target->rootAnnotatable;
        } else {
            throw new \InvalidArgumentException();
        }

        $rootTarget->allAnnotations()->save($annotation);

        return $annotation;
    }

    public function createAnnotationComment($target, $user, $data)
    {
        $annotationComment = AnnotationTypes\Comment::create([
            'content' => $data['text'],
        ]);

        $annotation = $this->createBaseAnnotation($target, $user, $data);
        $annotation->data = array_intersect_key($data, array_flip(['uri', 'quote']));
        $annotation->save();

        $annotationComment->annotation()->save($annotation);

        return $annotation;
    }

    public function createAnnotationFlag($target, $user, $data)
    {
        $annotationFlag = AnnotationTypes\Flag::create([
        ]);

        $annotation = $this->createBaseAnnotation($target, $user, $data);

        $annotationFlag->annotation()->save($annotation);

        return $annotation;
    }

    public function createAnnotationLike($target, $user, $data)
    {
        $annotationLike = AnnotationTypes\Like::create([
        ]);

        $annotation = $this->createBaseAnnotation($target, $user, $data);

        $annotationLike->annotation()->save($annotation);

        return $annotation;
    }

    public function createAnnotationRange($target, $user, $data)
    {
        $annotationRange = AnnotationTypes\Range::create([
            'start_offset' => $data['startOffset'],
            'end_offset' => $data['endOffset'],
            'start' => $data['start'],
            'end' => $data['end'],
        ]);

        $annotation = $this->createBaseAnnotation($target, $user, $data);

        $annotationRange->annotation()->save($annotation);

        return $annotation;
    }

    public function createAnnotationSeen($target, $user, $data)
    {
        $annotationSeen = AnnotationTypes\Seen::create([
        ]);

        $annotation = $this->createBaseAnnotation($target, $user, $data);

        $annotationSeen->annotation()->save($annotation);

        return $annotation;
    }

    public function createAnnotationTag($target, $user, $data)
    {
        $annotationTag = AnnotationTypes\Tag::create([
            'tag' => $data['tag'],
        ]);

        $annotation = $this->createBaseAnnotation($target, $user, $data);

        $annotationTag->annotation()->save($annotation);

        return $annotation;
    }
}
