<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Annotation;
use App\Models\Doc;
use App\Models\User;

class AnnotationsTableSeeder extends Seeder
{
    public function run()
    {
        $user = User::find(1);
        $doc = Doc::find(1);

        $note1 = [
            'quote' => 'Document',
            'text' => 'Note!',
            'uri' => '/docs/example-document',
            'tags' => [],
            'comments' => [],
            'ranges' => [
                [
                    'start' => '/p[1]',
                    'end' => '/p[1]',
                    'startOffset' => 4,
                    'endOffset' => 12
                ]
            ]
        ];

        App::make('App\Http\Controllers\CommentController')
            ->createFromAnnotatorArray($doc, $user, $note1);

        $note2 = [
            'quote' => 'Content',
            'text' => 'Another Note!',
            'uri' => '/docs/example-document',
            'tags' => [],
            'comments' => [],
            'ranges' => [
                [
                    'start' => '/p[1]',
                    'end' => '/p[1]',
                    'startOffset' => 13,
                    'endOffset' => 20
                ]
            ]
        ];

        App::make('App\Http\Controllers\CommentController')
            ->createFromAnnotatorArray($doc, $user, $note2);

        $comment1 = [
            'text' => 'This is a comment'
        ];

        $comment1Result = App::make('App\Http\Controllers\CommentController')
            ->createFromAnnotatorArray($doc, $user, $comment1);

        $comment1Reply = [
            'text' => 'Comment reply',
        ];

        $commentTarget = Annotation::find($comment1Result['id']);
        App::make('App\Http\Controllers\CommentController')
            ->createFromAnnotatorArray($commentTarget, $user, $comment1Reply);

        $comment2 = [
            'text' => 'Yet another comment'
        ];

        App::make('App\Http\Controllers\CommentController')
            ->createFromAnnotatorArray($doc, $user, $comment2);
    }
}
