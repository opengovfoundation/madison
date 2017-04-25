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
            '@sponsorList' => '#doc-header .sponsors',
            '@stats' => '.document-stats',
            '@supportBtn' => '.support-btn button',
            '@opposeBtn' => '.oppose-btn button',
            '@outline' => '#document-outline',
            '@noteBubble' => '.annotation-group',
            '@notesPane' => '.annotation-list',
            '@commentsDiv' => '#comments.comments',
            '@contentDiv' => '#content #page_content',
            '@likeBtn' => '[data-action-type="likes"]',
            '@flagBtn' => '[data-action-type="flags"]',
            '@newCommentForm' => '.new-comment-form',
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

    public function assertSeeNote(Browser $browser, Annotation $note)
    {
        // Timestamps end up being off by a second sometimes, so leaving them out.
        $noteSelector = static::noteSelector($note);
        $browser->with('@notesPane', function ($notesPane) use ($note, $noteSelector) {
            $notesPane->with($noteSelector, function ($noteElement) use ($note) {
                $noteElement
                    ->assertSee($note->user->name)
                    ->assertSeeIn('@likeBtn', (string) $note->likes_count)
                    ->assertVisible('@flagBtn')
                    ->assertSeeCommentContent($note)
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
                        ->assertSeeIn('@likeBtn', (string) $reply->likes_count)
                        ->assertVisible('@flagBtn')
                        ->assertSeeCommentContent($reply)
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
                ->assertSeeIn('@likeBtn', (string) $comment->likes_count)
                ->assertVisible('@flagBtn')
                ->assertSeeCommentContent($comment)
                ;
        })
        ;
    }

    public function assertSeeCommentContent(Browser $browser, Annotation $comment)
    {
        foreach (preg_split('/\\n\\n/', $comment->annotationType->content) as $line) {
            $browser->assertSee($line);
        }
    }

    public function assertSeeReplyToComment(Browser $browser, Annotation $comment, Annotation $reply)
    {
        // Timestamps end up being off by a second sometimes, so leaving them out.
        $commentSelector = static::commentSelector($comment);
        $replySelector = static::commentSelector($reply);

        $browser
            ->with($commentSelector, function ($commentDiv) use ($reply, $replySelector) {
                $commentDiv
                    ->with($replySelector, function ($replyDiv) use ($reply) {
                        $replyDiv
                            ->assertSee($reply->user->name)
                            ->assertSeeIn('@likeBtn', (string) $reply->likes_count)
                            ->assertVisible('@flagBtn')
                            ->assertSeeCommentContent($reply)
                            ;
                    });
            })
        ;
    }

    public function assertCommentHasActionCount(Browser $browser, $action, Annotation $comment, $count)
    {
        $commentSelector = static::commentSelector($comment);
        $browser->with($commentSelector, function ($commentDiv) use ($action, $count) {
            $commentDiv->with('@' . $action . 'Btn', function ($actionDiv) use ($count) {
                // Use a wait since it updates after going to server
                $actionDiv->waitForText($count);
            });
        });
    }

    public function assertCommentActionActive(Browser $browser, $action, Annotation $comment)
    {
        $commentSelector = static::commentSelector($comment);
        $browser->with($commentSelector, function ($commentDiv) use ($action) {
            $commentDiv->waitUntil("$('".$commentDiv->resolver->format('@' . $action . 'Btn')."').hasClass('active')");
        });
    }

    public function assertNoteHasActionCount(Browser $browser, $action, Annotation $note, $count)
    {
        $noteSelector = static::noteSelector($note);
        $browser->with($noteSelector, function ($noteDiv) use ($action, $count) {
            $noteDiv->with('@' . $action . 'Btn', function ($actionDiv) use ($count) {
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
                    ->assertVisible('@' . $action . 'Btn')
                    ->pause(500)
                    ->click('@' . $action . 'Btn')
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
                    ->assertVisible('@' . $action . 'Btn')
                    ->pause(500)
                    ->click('@' . $action . 'Btn')
                    ;
            })
            ;
    }

    public function fillAndSubmitCommentForm(Browser $browser)
    {
        $fakeComment = factory(Comment::class)->make();

        $browser
            ->with('@newCommentForm', function ($commentForm) use ($fakeComment) {
                $commentForm
                    ->click('.new-comment-form-toggle')
                    ->waitFor('@submitBtn')
                    ->type('text', static::flattenParagraphs($fakeComment->content))
                    ->click('@submitBtn')
                    ;
            })
            ;
    }

    public function fillAndSubmitCommentReplyForm(Browser $browser, Annotation $comment)
    {
        $fakeComment = factory(Comment::class)->make();
        $commentSelector = static::commentSelector($comment);

        $browser->with($commentSelector, function ($commentDiv) use ($commentSelector, $fakeComment) {
            $commentDiv
                ->click('.new-comment-form-toggle')
                ->waitFor('@submitBtn')
                ->type('text', static::flattenParagraphs($fakeComment->content))
                ->click('@submitBtn')
                ->waitUntil("$('".$commentSelector."').hasClass('pending')")
                ->waitUntil("!$('".$commentSelector."').hasClass('pending')")
                ;
        });
    }

    public function addNoteToContent(Browser $browser)
    {
        $fakeNote = factory(Comment::class)->make();

        $browser
            ->pause(1000)
            ->script(join(';', [
                'let firstParagraph = document.querySelector("#content #page_content .annotator-wrapper p")',
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
            ->pause(500)
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
            ->pause(500)
            ->assertPathIs('/login')
            ->on(new LoginPage)
            ->fillAndSubmitLoginForm($user)
            ->pause(500)
            ->assertPathIs($this->url())
            ->assertAuthenticatedAs($user)
            ;
    }

    public function revealCommentReplies(Browser $browser, $comment) {
        $commentSelector = static::commentSelector($comment);

        $browser
            ->with($commentSelector, function ($commentDiv) {
                $commentDiv
                    ->pause(1000)
                    ->click('.comment-replies-toggle-show')
                    ->waitFor('.comment-replies .comment')
                    ;
            })
            ;
    }

    public static function noteSelector(Annotation $note)
    {
        return static::commentSelector($note);
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
