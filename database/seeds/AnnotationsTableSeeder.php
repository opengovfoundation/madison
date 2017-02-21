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
        $annotationService = App::make('App\Services\Annotations');

        foreach ($docs as $doc) {
            // All the comments
            $numComments = rand(
                config('madison.seeder.num_comments_per_doc_min'),
                config('madison.seeder.num_comments_per_doc_max')
            );

            if (empty($numComments)) {
                continue;
            }

            $fakeComments = factory(AnnotationTypes\Comment::class, $numComments)->make();
            $comments = $fakeComments->map(function ($fakeComment) use ($doc, $users, $annotationService) {
                $commentData = [
                    'text' => $fakeComment->content,
                ];
                return $annotationService
                    ->createAnnotationComment($doc, $users->random(), $commentData);
            });

            // Make some of them notes
            $numNotes = round($comments->count() * max(0, min(1, config('madison.seeder.comments_percentage_notes'))));
            if ($numNotes) {
                $notes = $comments->random($numNotes)->each(function ($comment) use ($doc, $annotationService) {
                    $content = $doc->content()->first()->content;
                    $contentLines = preg_split('/\\n\\n/', $content);
                    $paragraphNumber = rand(1, count($contentLines));
                    $endParagraphOffset = strlen($contentLines[$paragraphNumber - 1]);
                    $startOffset = rand(1, $endParagraphOffset);
                    $endOffset = rand($startOffset, $endParagraphOffset);

                    // create range annotation
                    $rangeData = [
                        'start' => '/p['.$paragraphNumber.']',
                        'end' => '/p['.$paragraphNumber.']',
                        'startOffset' => $startOffset,
                        'endOffset' => $endOffset,
                    ];
                    $annotationService
                        ->createAnnotationRange($comment, $comment->user, $rangeData);

                    $comment->annotation_subtype = Annotation::SUBTYPE_NOTE;
                    $comment->save();
                });
            }

            // Reply to some of them
            $numReplied = round($comments->count() * max(0, min(1, config('madison.seeder.comments_percentage_replied'))));
            if ($numReplied) {
                $replies = $comments->random($numReplied)->each(function ($comment) use ($users, $annotationService) {
                    $numReplies = rand(
                        config('madison.seeder.num_replies_per_comment_min'),
                        config('madison.seeder.num_replies_per_comment_max')
                    );

                    if ($numReplies) {
                        $fakeComments = factory(AnnotationTypes\Comment::class, $numReplies)->make();
                        $replies = $fakeComments->map(function ($fakeComment) use ($users, $annotationService, $comment) {
                            $commentData = [
                                'text' => $fakeComment->content,
                            ];

                            if ($comment->isNote()) {
                                $commentData['subtype'] = Annotation::SUBTYPE_NOTE;
                            }

                            return $annotationService
                                ->createAnnotationComment($comment, $users->random(), $commentData);
                        });
                    }
                });
            }
        }
    }
}
