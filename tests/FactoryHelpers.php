<?php

namespace Tests;

use App;
use Faker;
use Exception;

use App\Models\Doc as Document;
use App\Models\User;
use App\Models\Sponsor;
use App\Models\AnnotationTypes;

class FactoryHelpers
{
    public static function addNoteToDocument(User $user, Document $document, $quote = null)
    {
        $faker = Faker\Factory::create();
        $commentService = App::make('App\Services\Comments');

        $docContent = $document->content()->first()->content;

        $paragraph = 0;
        $startOffset = 0;
        $endOffset = 1;

        if ($quote !== null) {
            $result = static::getPositionFromContent($docContent, $quote);
            $paragraph = $result['paragraph'];
            $startOffset = $result['start'];
            $endOffset = $startOffset + strlen($quote);
        } else {
            $firstParagraph = explode("\n\n", $docContent)[0];
            $startOffset = rand(0, strlen($firstParagraph) - 1);
            $endOffset = $startOffset + 1;
        }

        $note = [
            'quote' => 'Document',
            'text' => factory(AnnotationTypes\Comment::class)->make()->content,
            'uri' => '/documents/' . $document->slug,
            'tags' => [],
            'comments' => [],
            'ranges' => [
                [
                    'start' => '/p[' . (string) ($paragraph+1) . ']',
                    'end' => '/p[' . (string) ($paragraph+1) . ']',
                    'startOffset' => $startOffset,
                    'endOffset' => $endOffset
                ]
            ]
        ];

        return $commentService->createFromAnnotatorArray($document, $user, $note);
    }

    public static function createComment(User $user, $target)
    {
        $faker = Faker\Factory::create();
        $commentService = App::make('App\Services\Comments');

        return $commentService->createFromAnnotatorArray($target, $user, [
            'text' => factory(AnnotationTypes\Comment::class)->make()->content
        ]);
    }

    public static function createActiveSponsorWithUser(User $user)
    {
        $sponsor = factory(Sponsor::class)->create([
            'status' => Sponsor::STATUS_ACTIVE,
        ]);

        $sponsor->addMember($user->id, Sponsor::ROLE_OWNER);

        return $sponsor;
    }

    /**
     * For now, this assumes all content lines are individual paragraphs,
     * no support for any further complex HTML.
     *
     * In the future, we can use XPath here, since PHP has some built in
     * helpers for that and the ranges use it. But this will do for now :)
     */
    private static function getPositionFromContent($content, $quote)
    {
        $paragraphs = explode("\n\n", $content);

        $pIndex = null;
        $startIndex = null;

        foreach ($paragraphs as $idx => $p) {
            $quotePos = strpos($p, $quote);

            if ($quotePos !== false) {
                $pIndex = $idx;
                $startIndex = $quotePos;
                break;
            }
        }

        if ($pIndex === null) throw new Exception('Quote not found in string');

        return ['paragraph' => $pIndex, 'start' => $startIndex];
    }
}
