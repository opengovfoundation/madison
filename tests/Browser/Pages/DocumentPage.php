<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;
use App\Models\Annotation;
use App\Models\User;

class DocumentPage extends BasePage
{

    public function __construct($document)
    {
        $this->document = $document;
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route('documents.show', $this->document, false);;
    }

    /**
     * Assert that the browser is on the page.
     *
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url());
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@sponsorList' => '.page-header .sponsors',
            '@stats' => '.document-stats',
            '@participantCount' => '.participants-count',
            '@commentsCount' => '.comments-count',
            '@notesCount' => '.notes-count',
            '@supportBtn' => '.support-btn button',
            '@opposeBtn' => '.oppose-btn button',
            '@contentTab' => '.nav-tabs a[href="#content"]',
            '@commentsTab' => '.nav-tabs a[href="#comments"]',
            '@noteBubble' => '.annotation-group',
            '@notesPane' => '.annotation-list',
            '@commentsList' => '#comments.comments',
            '@likeCount' => '.activity-actions a[data-action-type="likes"] .action-count',
            '@flagCount' => '.activity-actions a[data-action-type="flags"] .action-count',
            '@addCommentForm' => '.comment-form',
            '@noteReplyForm' => '.add-subcomment-form',
        ];
    }

    public function openNotesPane(Browser $browser)
    {
        $browser
            ->waitFor('@noteBubble')
            ->click('@noteBubble')
            ->pause(700) // Ensure enough time for notes pane to expand
            ->assertVisible('@notesPane')
            ;
    }

    public function openCommentsTab(Browser $browser)
    {
        $browser
            ->waitFor('@noteBubble') // Ensures annotator has added action callbacks to comments
            ->click('@commentsTab')
            ->waitFor('@commentsList')
            ;
    }

    public function assertSeeNote(Browser $browser, Annotation $note)
    {
        // Timestamps end up being off by a second sometimes, so leaving them out.
        $noteSelector = static::noteSelector($note);
        $browser->with('@notesPane', function ($notesPane) use ($note, $noteSelector) {
            $notesPane->with($noteSelector, function ($noteElement) use ($note) {
                $noteElement
                    ->assertSee($note->user->name)
                    ->assertSeeIn('@likeCount', (string) $note->likes_count)
                    ->assertSeeIn('@flagCount', (string) $note->flags_count)
                    ;

                foreach (explode("\n\n", $note->annotationType->content) as $p) {
                    $noteElement->assertSee($p);
                }
            });
        });
    }

    public function assertSeeComment(Browser $browser, Annotation $comment)
    {
        // Timestamps end up being off by a second sometimes, so leaving them out.
        $commentSelector = static::commentSelector($comment);
        $browser->with($commentSelector, function ($commentDiv) use ($comment) {
            $commentDiv
                ->assertSee($comment->user->name)
                ->assertSeeIn('@likeCount', (string) $comment->likes_count)
                ->assertSeeIn('@flagCount', (string) $comment->flags_count)
                ;

            foreach (explode("\n\n", $comment->annotationType->content) as $p) {
                $commentDiv->assertSee($p);
            }
        })
        ;
    }

    public function assertSeeReplyToComment(Browser $browser, Annotation $comment, Annotation $reply)
    {
        /**
         * Timestamps end up being off by a second sometimes, so leaving them out.
         */
        $commentSelector = static::commentSelector($comment);
        $replySelector = static::commentSelector($reply);

        $browser->with($commentSelector, function ($commentDiv) use ($reply, $replySelector) {
            $commentDiv->with($replySelector, function ($replyDiv) use ($reply) {
                $replyDiv
                    ->assertSee($reply->user->name)
                    ->assertSeeIn('@likeCount', (string) $reply->likes_count)
                    ->assertSeeIn('@flagCount', (string) $reply->flags_count)
                    ;

                foreach (explode("\n\n", $reply->annotationType->content) as $p) {
                    $replyDiv->assertSee($p);
                }
            });
        })
        ;
    }

    public function addActionToComment(Browser $browser, $action, Annotation $comment)
    {
        $commentSelector = static::commentSelector($comment);
        $browser
            ->openCommentsTab()
            ->with($commentSelector, function ($commentDiv) use ($action) {
                $commentDiv
                    ->assertVisible('@' . $action . 'Count')
                    ->click('@' . $action . 'Count')
                    ;
            })
            ;
    }

    public function addActionToNote(Browser $browser, $action, Annotation $note)
    {
        $noteSelector = static::noteSelector($note);
        $browser
            ->openNotesPane()
            ->with($noteSelector, function ($noteElement) use ($action) {
                $noteElement
                    ->assertVisible('@' . $action . 'Count')
                    ->click('@' . $action . 'Count')
                    ;
            })
            ;
    }

    public static function noteSelector(Annotation $note)
    {
        return '.annotation#' . $note->str_id;
    }

    public static function commentSelector(Annotation $comment)
    {
        return '.comment#' . $comment->str_id;
    }
}
