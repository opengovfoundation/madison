<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Annotation;
use App\Models\AnnotationTypes;
use App\Models\Doc as Document;
use App\Models\User;

class AnnotationsTableSeeder extends Seeder
{
    public function run()
    {
        $regularUser = User::find(1);
        $adminUser = User::find(2);
        $users = collect([$regularUser, $adminUser]);
        $docs = Document::all();

        foreach ($docs as $doc) {
            // All the comments
            $numComments = rand(
                config('madison.seeder.num_comments_per_doc_min'),
                config('madison.seeder.num_comments_per_doc_max')
            );

            if (empty($numComments)) {
                continue;
            }

            $comments = factory(Annotation::class, $numComments)->create([
                'user_id' => $users->random()->id,
            ])->each(function ($ann) use ($doc, $users) {
                $doc->annotations()->save($ann);
                $doc->allAnnotations()->save($ann);
                $comment = factory(AnnotationTypes\Comment::class)->create();
                $comment->annotation()->save($ann);
                $ann->user()->associate($users->random());
            });

            // Make some of them notes
            $numNotes = round($comments->count() * max(0, min(1, config('madison.seeder.comments_percentage_notes'))));
            if ($numNotes) {
                $notes = $comments->random($numNotes)->each(function ($comment) use ($doc) {
                    $content = $doc->content()->first()->content;
                    $contentLines = preg_split('/\\n\\n/', $content);
                    $paragraphNumber = rand(1, count($contentLines));
                    $endParagraphOffset = strlen($contentLines[$paragraphNumber - 1]);
                    $startOffset = rand(1, $endParagraphOffset);
                    $endOffset = rand($startOffset, $endParagraphOffset);

                    // create range annotation
                    $annotation = factory(Annotation::class)->create([
                        'user_id' => $comment->user->id,
                    ]);
                    $range = factory(AnnotationTypes\Range::class)->create([
                        'start' => '/p['.$paragraphNumber.']',
                        'end' => '/p['.$paragraphNumber.']',
                        'start_offset' => $startOffset,
                        'end_offset' => $endOffset,
                    ]);
                    $range->annotation()->save($annotation);

                    // mark comment with range
                    $comment->annotations()->save($annotation);
                    $doc->allAnnotations()->save($annotation);
                    $comment->annotation_subtype = 'note';
                    $comment->save();
                });
            }

            // Reply to some of them
            $numReplied = round($comments->count() * max(0, min(1, config('madison.seeder.comments_percentage_replied'))));
            if ($numReplied) {
                $replies = $comments->random($numReplied)->each(function ($comment) use ($doc, $users) {
                    $numReplies = rand(
                        config('madison.seeder.num_replies_per_comment_min'),
                        config('madison.seeder.num_replies_per_comment_max')
                    );

                    if ($numReplies) {
                        $replies = factory(Annotation::class, $numReplies)->create([
                            'user_id' => $users->random()->id,
                        ])->each(function ($annotation) use ($doc, $comment) {
                            $reply = factory(AnnotationTypes\Comment::class)->create();
                            $reply->annotation()->save($annotation);

                            // mark comment with reply
                            $comment->annotations()->save($annotation);
                            $doc->allAnnotations()->save($annotation);
                            if ($comment->isNote()) {
                                $comment->annotation_subtype = 'note';
                            }
                            $comment->save();
                        });
                    }
                });
            }
        }
    }
}
