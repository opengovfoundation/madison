<?php

namespace App\Services;

use App\Models\Annotation;
use App\Models\AnnotationPermission;
use App\Models\Doc as Document;
use App\Models\User;
use DB;
use League\Csv\Writer;

class Comments
{
    protected $annotationService;

    public function __construct(Annotations $annotationService)
    {
        $this->annotationService = $annotationService;
    }

    /**
     * @param [App\Models\Comment] $comments
     *
     * @return League\Csv\Writer
     */
    public function toCsv($comments)
    {
        $csv = Writer::createFromFileObject(new \SplTempFileObject());

        $fields = [
            'first_name',
            'last_name',
            'email',
            'quote',
            'text',
            'type',
            'created_at'
        ];

        // Headings.
        $csv->insertOne($fields);

        foreach ($comments as $comment) {
            $row = [
                'first_name' => $comment->user->fname,
                'last_name' => $comment->user->lname,
                'email' => $comment->user->email,
                'quote' => !empty($comment->data['quote']) ? $comment->data['quote'] : null,
                'text' => $comment->annotationType->content,
                'type' => $comment->isNote() ? 'note' : 'comment',
                'created_at' => $comment->created_at->toRfc3339String(),
            ];

            $csv->insertOne($row);
        }

        return $csv;
    }

    public function toAnnotatorArray(Annotation $comment, $includeChildren = true, $userId = null)
    {
        if ($comment->annotation_type_type !== Annotation::TYPE_COMMENT) {
            throw new InvalidArgumentException('Can only handle Annotations of type Comment');
        }

        $getUserInfo = function (User $user) {
            return array_intersect_key($user->toArray(), array_flip(['id', 'email', 'display_name']));
        };

        $item['id'] = $comment->id;
        $item['annotator_schema_version'] = 'v1.0';
        $item['ranges'] = [];
        $item['tags'] = [];
        $item['comments'] = [];
        $item['permissions'] = [
            'read' => [],
            'update' => [],
            'delete' => [],
            'admin' => [],
        ];

        $item['text'] = $comment->annotationType->content;

        if ($includeChildren) {
            $childComments = $comment->comments;
            foreach ($childComments as $childComment) {
                $item['comments'][] = [
                    'id' => $childComment->id,
                    'text' => $childComment->annotationType->content,
                    'created_at' => $childComment->created_at->toRfc3339String(),
                    'created_at_relative' => $childComment->created_at->diffForHumans(),
                    'updated_at' => $childComment->updated_at->toRfc3339String(),
                    'updated_at_relative' => $childComment->updated_at->diffForHumans(),
                    'user' => $getUserInfo($childComment->user),
                    'likes' => $childComment->likes_count,
                    'flags' => $childComment->flags_count,
                ];
            }
        } else {
            $item['comments_count'] = $comment->comments_count;
        }

        $ranges = $comment->ranges;
        foreach ($ranges as $range) {
            $rangeData = $range->annotationType;
            $item['ranges'][] = [
                'start' => $rangeData->start,
                'end' => $rangeData->end,
                'startOffset' => $rangeData->start_offset,
                'endOffset' => $rangeData->end_offset,
            ];
        }

        $item['user'] = $getUserInfo($comment->user);

        $item['consumer'] = Annotation::ANNOTATION_CONSUMER;

        foreach ($comment->tags as $tag) {
            $item['tags'][] = $tag->annotationType->tag;
        }

        $permissions = AnnotationPermission::where('annotation_id', '=', $comment->id)->get();
        foreach ($permissions as $perm) {
            if ($perm->read) {
                $item['permissions']['read'][] = $perm['user_id'];
            }

            $item['permissions']['update'][] = $perm->update ? $perm['user_id'] : '0';
            $item['permissions']['delete'][] = $perm->update ? $perm['user_id'] : '0';
            $item['permissions']['admin'][] = $perm->admin ? $perm['user_id'] : '0';
        }

        $item['likes'] = $comment->likes_count;
        $item['flags'] = $comment->flags_count;
        $item['seen'] = (bool) $comment->seens_count;
        $item['created_at'] = $comment->created_at->toRfc3339String();
        $item['created_at_relative'] = $comment->created_at->diffForHumans();
        $item['updated_at'] = $comment->updated_at->toRfc3339String();
        $item['updated_at_relative'] = $comment->updated_at->diffForHumans();

        // Pull in all other data
        if ($comment->data) {
            $item = array_merge($item, $comment->data);
        }

        // Filter down to just the keys we should send, just to be safe
        $item = array_intersect_key($item, array_flip([
            'id', 'annotator_schema_version', 'created_at',
            'created_at_relative', 'updated_at', 'updated_at_relative',
            'text', 'quote', 'uri', 'ranges', 'user', 'consumer', 'tags',
            'permissions', 'likes', 'flags', 'seen', 'comments',
            'comments_count', 'doc_id',
        ]));

        return $item;
    }

    public function createFromAnnotatorArray($target, User $user, array $data)
    {
        $isEdit = false;
        // check for edit tag
        if (!empty($data['tags']) && in_array('edit', $data['tags'])) {
            $isEdit = true;

            // if no explanation present, throw error
            if (!isset($data['explanation'])) {
                throw new \Exception('Explanation required for edits');
            }
        }

        $id = DB::transaction(function () use ($target, $user, $data, $isEdit) {
            if ((!empty($data['ranges']) && $target instanceof Document)
                || (empty($data['ranges']) && $target instanceof Annotation && $target->isNote())
            ) {
                $data['subtype'] = Annotation::SUBTYPE_NOTE;
            }

            $annotation = $this->annotationService->createAnnotationComment($target, $user, $data);

            $permissions = new AnnotationPermission();
            $permissions->annotation_id = $annotation->id;
            $permissions->user_id = $user->id;
            $permissions->read = 1;
            $permissions->update = 0;
            $permissions->delete = 0;
            $permissions->admin = 0;
            $permissions->save();

            if (!empty($data['ranges'])) {
                foreach ($data['ranges'] as $range) {
                    $this->annotationService->createAnnotationRange($annotation, $user, $range);
                }
            }

            if (!empty($data['tags'])) {
                foreach ($data['tags'] as $tag) {
                    $this->annotationService->createAnnotationTag($annotation, $user, ['tag' => $tag]);
                }
            }

            if ($isEdit) {
                $editData = [
                    'text' => $data['explanation'],
                    'subtype' => !empty($data['subtype']) ? $data['subtype'] : null,
                ];
                $this->annotationService->createAnnotationComment($annotation, $user, $editData);
            }

            return $annotation->id;
        });

        return Annotation::find($id);
    }
}
