<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;
use App\Models\Annotation;
use App\Models\AnnotationTypes\Comment;
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
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@sponsorList' => '.jumbotron .sponsors',
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
            '@commentsPane' => '#comments.comments',
            '@contentPane' => '#content #page_content',
            '@likeCount' => '.activity-actions a[data-action-type="likes"] .action-count',
            '@flagCount' => '.activity-actions a[data-action-type="flags"] .action-count',
            '@addCommentForm' => '.comment-form',
            '@noteReplyForm' => '.add-subcomment-form',
            '@submitBtn' => 'button[type="submit"]',
            '@noteAdder' => '.annotator-adder button',
            '@annotatorWidget' => '.annotator-widget',
            '@saveNoteBtn' => '.annotator-save',
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
            ->waitFor('@commentsPane')
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
                    ->assertSee(static::flattenParagraphs($note->annotationType->content))
                    ;
            });
        });
    }

    public function assertSeeReplyToNote(Browser $browser, Annotation $note, Annotation $reply)
    {
        $noteSelector = static::noteSelector($note);
        $replySelector = static::commentSelector($reply); // replies are comments, not notes

        $browser->with('@notesPane', function ($notesPane) use ($reply, $noteSelector, $replySelector) {
            $notesPane->with($noteSelector, function ($noteElement) use ($reply, $replySelector) {
                $noteElement->with($replySelector, function ($replyElement) use ($reply) {
                    $replyElement
                        ->assertSee($reply->user->name)
                        ->assertSeeIn('@likeCount', (string) $reply->likes_count)
                        ->assertSeeIn('@flagCount', (string) $reply->flags_count)
                        ->assertSee(static::flattenParagraphs($reply->annotationType->content))
                        ;
                });
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
                ->assertSee(static::flattenParagraphs($comment->annotationType->content))
                ;
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
                    ->assertSee(static::flattenParagraphs($reply->annotationType->content))
                    ;
            });
        })
        ;
    }

    public function assertCommentHasActionCount(Browser $browser, $action, Annotation $comment, $count)
    {
        $commentSelector = static::commentSelector($comment);
        $browser->with($commentSelector, function ($commentDiv) use ($action, $count) {
            $commentDiv->with('@' . $action . 'Count', function ($actionDiv) use ($count) {
                // Use a wait since it updates after going to server
                $actionDiv->waitForText($count);
            });
        });
    }

    public function assertNoteHasActionCount(Browser $browser, $action, Annotation $note, $count)
    {
        $noteSelector = static::noteSelector($note);
        $browser->with($noteSelector, function ($noteDiv) use ($action, $count) {
            $noteDiv->with('@' . $action . 'Count', function ($actionDiv) use ($count) {
                // Use a wait since it updates after going to server
                $actionDiv->waitForText($count);
            });
        });
    }

    public function assertDocumentSupportCount(Browser $browser, $count)
    {
        $browser->with('@supportBtn', function ($supportBtn) use ($count) {
            $supportBtn->waitForText($count);
        });
    }

    public function assertDocumentOpposeCount(Browser $browser, $count)
    {
        $browser->with('@opposeBtn', function ($opposeBtn) use ($count) {
            $opposeBtn->waitForText($count);
        });
    }

    public function addActionToComment(Browser $browser, $action, Annotation $comment)
    {
        $commentSelector = static::commentSelector($comment);
        $browser
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
            ->with($noteSelector, function ($noteElement) use ($action) {
                $noteElement
                    ->assertVisible('@' . $action . 'Count')
                    ->click('@' . $action . 'Count')
                    ;
            })
            ;
    }

    public function fillAndSubmitCommentForm(Browser $browser)
    {
        $fakeComment = factory(Comment::class)->make();

        $browser
            ->with('@addCommentForm', function ($commentForm) use ($fakeComment) {
                $commentForm
                    ->type('text', static::flattenParagraphs($fakeComment->content))
                    ->click('@submitBtn')
                    ;
            })
            ;
    }

    public function fillAndSubmitCommentReplyForm(Browser $browser, Annotation $comment)
    {
        $fakeComment = factory(Comment::class)->make();

        $browser->with('.comment#' . $comment->str_id, function ($commentDiv) use ($fakeComment) {
            $commentDiv
                ->type('text', static::flattenParagraphs($fakeComment->content))
                ->click('@submitBtn')
                ;
        });
    }

    public function addNoteToContent(Browser $browser)
    {
        $fakeNote = factory(Comment::class)->make();

        $browser
            ->pause(1000)
            ->script(join(';', [
                'let firstParagraph = document.querySelector("#content #page_content .annotator-wrapper p:first-child")',
                'let selection = window.getSelection()',
                'let range = document.createRange()',
                'range.selectNodeContents(firstParagraph)',
                'selection.removeAllRanges()',
                'selection.addRange(range)',
                '$(document).trigger("mouseup")',
            ]))
            ;

        $browser
            ->click('@noteAdder')
            ->with('@annotatorWidget', function ($annotatorWidget) use ($fakeNote) {
                $annotatorWidget
                    ->keys('#annotator-field-0', str_replace("\n\n", " ", $fakeNote->content)) // newlines seem to cause problems
                    ->click('@saveNoteBtn')
                    ->script('$(window).scrollTop(0)')
                    ;
            })
            ;
    }

    public function addReplyToNote(Browser $browser, Annotation $note)
    {
        $fakeNote = factory(Comment::class)->make();
        $targetNoteSelector = static::noteSelector($note);

        $browser
            ->with($targetNoteSelector, function ($noteElement) use ($note, $fakeNote) {
                $noteElement
                    ->type('text', str_replace("\n\n", " ", $fakeNote->content)) // newlines seem to cause problems
                    ->press('Submit')
                    ;
            });
    }

    public function assertLoginRedirectsBackToPage(Browser $browser, $user)
    {
        $browser
            ->on(new LoginPage)
            ->fillAndSubmitLoginForm($user)
            ->assertPathIs($this->url())
            ->assertAuthenticatedAs($user)
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

    public static function flattenParagraphs($content)
    {
        // Content paragraphs are combined into one in the UI as of now.
        return str_replace("\r\n", " ", str_replace("\n\n", " ", $content));
    }
}
