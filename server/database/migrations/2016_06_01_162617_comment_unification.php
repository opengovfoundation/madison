<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Annotation;
use App\Models\AnnotationTypes;
use App\Models\Doc;
use App\Models\Group;

class CommentUnification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        // create new stuff
        Schema::table('annotations', function ($table) {
            $table->morphs('annotatable');
            $table->morphs('annotation_type');
            $table->morphs('root_annotatable');
            $table->string('annotation_subtype', 10)->nullable();
            $table->json('data');
        });

        Schema::create('annotation_types_comment', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('content');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('annotation_types_range', function (Blueprint $table) {
            $table->increments('id');
            $table->string('start');
            $table->string('end');
            $table->integer('start_offset')->unsigned();
            $table->integer('end_offset')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('annotation_types_tag', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tag');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('annotation_types_seen', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('annotation_types_flag', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('annotation_types_like', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
        });


        // migrate old data to new places

        // Annotation -> AnnotationTypes/Comment
        $annotations = DB::table('annotations')->get();
        foreach ($annotations as $annotation) {
            $newComment = AnnotationTypes\Comment::create([
                'content' => $annotation->text,
            ]);

            $this->updateTimestamps($newComment, $annotation);

            $data = [
                'quote' => $annotation->quote,
                'uri' => $annotation->uri,
                'page' => $annotation->page,
                'old_id' => $annotation->id, // not actually changing, but just to be complete
                'old_permalink_type' => 'annotation',
            ];

            DB
                ::table('annotations')
                ->where('id', $annotation->id)
                ->update([
                    'annotatable_id' => $annotation->doc_id,
                    'annotatable_type' => Doc::ANNOTATABLE_TYPE,
                    'annotation_type_id' => $newComment->id,
                    'annotation_type_type' => Annotation::TYPE_COMMENT,
                    'root_annotatable_id' => $annotation->doc_id,
                    'root_annotatable_type' => Doc::ANNOTATABLE_TYPE,
                    'annotation_subtype' => Annotation::SUBTYPE_NOTE,
                    'data' => json_encode($data),
                ])
                ;

            if ($annotation->seen) {
                $doc = Doc::find($annotation->doc_id);

                if ($doc->sponsors()->count()) {
                    $newSeen = AnnotationTypes\Seen::create();

                    $this->updateTimestamps($newSeen, $annotation);

                    $sponser = $doc->sponsors()->first()->findUsersByRole(Group::ROLE_OWNER)->first();

                    DB::table('annotations')->insert([
                        'user_id' => $sponsor->id,
                        'annotatable_id' => $newComment->id,
                        'annotatable_type' => Annotation::ANNOTATABLE_TYPE,
                        'annotation_type_id' => $newSeen->id,
                        'annotation_type_type' => Annotation::TYPE_SEEN,
                        'root_annotatable_id' => $annotation->doc_id,
                        'root_annotatable_type' => Doc::ANNOTATABLE_TYPE,
                        'created_at' => $annotation->created_at,
                        'updated_at' => $annotation->updated_at,
                        'deleted_at' => $annotation->deleted_at,
                    ]);
                }
            }

            // NoteMeta -> AnnotationTypes/Likes and AnnotationTypes/Flags
            $noteMetas = DB::select('select * from note_meta where annotation_id = ?', [$annotation->id]);
            foreach ($noteMetas as $noteMeta) {
                $docId = DB::table('annotations')->where('id', $noteMeta->annotation_id)->value('doc_id');
                switch ($noteMeta->meta_value) {
                    case 'like':
                        $newLike = AnnotationTypes\Like::create();
                        DB::table('annotations')->insert([
                            'user_id' => $noteMeta->user_id,
                            'annotatable_id' => $noteMeta->annotation_id,
                            'annotatable_type' => Annotation::ANNOTATABLE_TYPE,
                            'annotation_type_id' => $newLike->id,
                            'annotation_type_type' => Annotation::TYPE_LIKE,
                            'root_annotatable_id' => $docId,
                            'root_annotatable_type' => Doc::ANNOTATABLE_TYPE,
                            'created_at' => $noteMeta->created_at,
                            'updated_at' => $noteMeta->updated_at,
                        ]);
                        break;
                    case 'flag':
                        $newFlag = AnnotationTypes\Flag::create();
                        DB::table('annotations')->insert([
                            'user_id' => $noteMeta->user_id,
                            'annotatable_id' => $noteMeta->annotation_id,
                            'annotatable_type' => Annotation::ANNOTATABLE_TYPE,
                            'annotation_type_id' => $newFlag->id,
                            'annotation_type_type' => Annotation::TYPE_FLAG,
                            'created_at' => $noteMeta->created_at,
                            'updated_at' => $noteMeta->updated_at,
                        ]);
                        break;
                    default:
                        throw new Exception('Unknown note meta value: "'.$noteMeta->meta_value.'"');
                }
            }
        }

        // AnnotationTag -> AnnotationTypes/Tag
        $tags = DB::table('annotation_tags')->get();
        foreach ($tags as $tag) {
            $newTag = AnnotationTypes\Tag::create([
                'tag' => $tag->tag,
            ]);

            $this->updateTimestamps($newTag, $tag);

            $docId = DB::table('annotations')->where('id', $tag->annotation_id)->value('doc_id');
            $userId = DB::table('annotations')->where('id', $tag->annotation_id)->value('user_id');
            DB::table('annotations')->insert([
                'user_id' => $userId,
                'annotatable_id' => $tag->annotation_id,
                'annotatable_type' => Annotation::ANNOTATABLE_TYPE,
                'annotation_type_id' => $newTag->id,
                'annotation_type_type' => Annotation::TYPE_TAG,
                'root_annotatable_id' => $docId,
                'root_annotatable_type' => Doc::ANNOTATABLE_TYPE,
                'created_at' => $tag->created_at,
                'updated_at' => $tag->updated_at,
                'deleted_at' => $tag->deleted_at,
            ]);
        }

        // AnnotationComment -> AnnotationTypes/Comment
        $annotationComments = DB::table('annotation_comments')->get();
        foreach ($annotationComments as $annotationComment) {
            $newComment = AnnotationTypes\Comment::create([
                'content' => $annotationComment->text,
            ]);

            $this->updateTimestamps($newComment, $annotationComment);

            $docId = DB::table('annotations')->where('id', $annotationComment->annotation_id)->value('doc_id');
            $data = [
                'old_id' => $annotationComment->id,
                'old_permalink_type' => 'annsubcomment',
            ];
            DB::table('annotations')->insert([
                'user_id' => $annotationComment->user_id,
                'annotatable_id' => $annotationComment->annotation_id,
                'annotatable_type' => Annotation::ANNOTATABLE_TYPE,
                'annotation_type_id' => $newComment->id,
                'annotation_type_type' => Annotation::TYPE_COMMENT,
                'root_annotatable_id' => $docId,
                'root_annotatable_type' => Doc::ANNOTATABLE_TYPE,
                'created_at' => $annotationComment->created_at,
                'updated_at' => $annotationComment->updated_at,
                'deleted_at' => $annotationComment->deleted_at,
                'data' => json_encode($data),
            ]);

            $annotationSeen = DB::select('select seen from annotations where id = ?', [$annotationComment->annotation_id]);
            if ($annotationSeen) {
                $doc = Doc::find($docId);

                if ($doc->sponsors()->count() === 0) {
                    continue;
                }

                $newSeen = AnnotationTypes\Seen::create();

                $this->updateTimestamps($newSeen, $annotationComment);

                $sponsor = $doc->sponsors()->first()->findUsersByRole(Group::ROLE_OWNER)->first();

                DB::table('annotations')->insert([
                    'user_id' => $sponsor->id,
                    'annotatable_id' => $annotationComment->annotation_id,
                    'annotatable_type' => Annotation::ANNOTATABLE_TYPE,
                    'annotation_type_id' => $newSeen->id,
                    'annotation_type_type' => Annotation::TYPE_SEEN,
                    'root_annotatable_id' => $docId,
                    'root_annotatable_type' => Doc::ANNOTATABLE_TYPE,
                    'created_at' => $annotationComment->created_at,
                    'updated_at' => $annotationComment->updated_at,
                    'deleted_at' => $annotationComment->deleted_at,
                ]);
            }
        }

        // Comment -> AnnotationTypes/Comment
        $comments = DB::table('comments')->orderBy('id')->get();
        $commentIdToAnnotationId = [];
        foreach ($comments as $comment) {
            $newComment = AnnotationTypes\Comment::create([
                'content' => $comment->text,
            ]);

            $this->updateTimestamps($newComment, $comment);

            $annotatableId = $comment->doc_id;
            $annotatableType = Doc::ANNOTATABLE_TYPE;

            // if this is a reply/subcomment, then look up it's new id as an
            // annotation, this should be safe to just do since subcomments
            // will naturally come after their parents by id
            if (!empty($comment->parent_id)) {
                $annotatableId = $commentIdToAnnotationId[$comment->parent_id];
                $annotatableType = Annotation::ANNOTATABLE_TYPE;
            }

            $data = [
                'old_id' => $comment->id,
                'old_permalink_type' => 'comment',
            ];

            $commentAnnotationId = DB::table('annotations')->insertGetId([
                'user_id' => $comment->user_id,
                'annotatable_id' => $annotatableId,
                'annotatable_type' => $annotatableType,
                'annotation_type_id' => $newComment->id,
                'annotation_type_type' => Annotation::TYPE_COMMENT,
                'root_annotatable_id' => $comment->doc_id,
                'root_annotatable_type' => Doc::ANNOTATABLE_TYPE,
                'created_at' => $comment->created_at,
                'updated_at' => $comment->updated_at,
                'deleted_at' => $comment->deleted_at,
                'data' => json_encode($data),
            ]);

            // save our old id -> new id info for any sub comments
            $commentIdToAnnotationId[$comment->id] = $commentAnnotationId;

            if ($comment->seen) {
                $doc = Doc::find($comment->doc_id);

                if ($doc->sponsors()->count()) {
                    $newSeen = AnnotationTypes\Seen::create();

                    $this->updateTimestamps($newSeen, $comment);

                    $sponsor = $doc->sponsors()->first()->findUsersByRole(Group::ROLE_OWNER)->first();

                    DB::table('annotations')->insert([
                        'user_id' => $sponsor->id,
                        'annotatable_id' => $commentAnnotationId,
                        'annotatable_type' => Annotation::ANNOTATABLE_TYPE,
                        'annotation_type_id' => $newSeen->id,
                        'annotation_type_type' => Annotation::TYPE_SEEN,
                        'root_annotatable_id' => $comment->doc_id,
                        'root_annotatable_type' => Doc::ANNOTATABLE_TYPE,
                        'created_at' => $comment->created_at,
                        'updated_at' => $comment->updated_at,
                        'deleted_at' => $comment->deleted_at,
                    ]);
                }
            }

            // CommentMeta -> AnnotationTypes/Likes and AnnotationTypes/Flags
            $commentMetas = DB::select('select * from comment_meta where comment_id = ?', [$comment->id]);
            foreach ($commentMetas as $commentMeta) {
                switch ($commentMeta->meta_value) {
                    case 'like':
                        $newLike = AnnotationTypes\Like::create();
                        $this->updateTimestamps($newLike, $commentMeta);
                        DB::table('annotations')->insert([
                            'user_id' => $commentMeta->user_id,
                            'annotatable_id' => $commentAnnotationId,
                            'annotatable_type' => Annotation::ANNOTATABLE_TYPE,
                            'annotation_type_id' => $newLike->id,
                            'annotation_type_type' => Annotation::TYPE_LIKE,
                            'root_annotatable_id' => $comment->doc_id,
                            'root_annotatable_type' => Doc::ANNOTATABLE_TYPE,
                            'created_at' => $commentMeta->created_at,
                            'updated_at' => $commentMeta->updated_at,
                        ]);
                        break;
                    case 'flag':
                        $newFlag = AnnotationTypes\Flag::create();
                        $this->updateTimestamps($newFlag, $commentMeta);
                        DB::table('annotations')->insert([
                            'user_id' => $commentMeta->user_id,
                            'annotatable_id' => $commentAnnotationId,
                            'annotatable_type' => Annotation::ANNOTATABLE_TYPE,
                            'annotation_type_id' => $newFlag->id,
                            'annotation_type_type' => Annotation::TYPE_FLAG,
                            'root_annotatable_id' => $comment->doc_id,
                            'root_annotatable_type' => Doc::ANNOTATABLE_TYPE,
                            'created_at' => $commentMeta->created_at,
                            'updated_at' => $commentMeta->updated_at,
                        ]);
                        break;
                    default:
                        throw new Exception('Unknown comment meta value: "'.$commentMeta->meta_value.'"');
                }
            }
        }

        // AnnotationRange -> AnnotationTypes/Range
        $annotationRanges = DB::table('annotation_ranges')->get();
        foreach ($annotationRanges as $annotationRange) {
            $newRange = AnnotationTypes\Range::create([
                'start' => $annotationRange->start,
                'end' => $annotationRange->end,
                'start_offset' => $annotationRange->start_offset,
                'end_offset' => $annotationRange->end_offset,
            ]);

            $this->updateTimestamps($newRange, $annotationRange);

            $docId = DB
                ::table('annotations')
                ->where('id', $annotationRange->annotation_id)
                ->value('doc_id')
                ;
            $userId = DB
                ::table('annotations')
                ->where('id', $annotationRange->annotation_id)
                ->value('user_id')
                ;

            DB::table('annotations')->insert([
                'user_id' => $userId,
                'annotatable_id' => $annotationRange->annotation_id,
                'annotatable_type' => Annotation::ANNOTATABLE_TYPE,
                'annotation_type_id' => $newRange->id,
                'annotation_type_type' => Annotation::TYPE_RANGE,
                'root_annotatable_id' => $docId,
                'root_annotatable_type' => Doc::ANNOTATABLE_TYPE,
                'created_at' => $annotationRange->created_at,
                'updated_at' => $annotationRange->updated_at,
                'deleted_at' => $annotationRange->deleted_at,
            ]);
        }


        // delete old places
        Schema::table('annotations', function ($table) {
            $table->dropColumn('quote');
            $table->dropColumn('page');
            $table->dropColumn('search_id');
            $table->dropColumn('seen');
            $table->dropColumn('text');
            $table->dropColumn('uri');
            $table->dropForeign(['doc_id']);
            $table->dropColumn('doc_id');
        });

        Schema::drop('annotation_comments');
        Schema::drop('annotation_ranges');
        Schema::drop('annotation_tags');
        Schema::drop('comments');
        Schema::drop('comment_meta');
        Schema::drop('note_meta');
        DB::statement('DROP VIEW doc_actions');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

    protected function updateTimestamps($target, $basedOn)
    {
        DB
            ::table($target->getTable())
            ->where('id', $target->id)
            ->update([
                'created_at' => $basedOn->created_at,
                'updated_at' => $basedOn->updated_at,
                'deleted_at' => property_exists($basedOn, 'deleted_at') ? $basedOn->deleted_at : null,
            ])
            ;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // it is not safe to reverse the migration
    }
}
