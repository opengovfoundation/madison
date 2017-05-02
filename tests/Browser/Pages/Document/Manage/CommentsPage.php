<?php

namespace Tests\Browser\Pages\Document\Manage;

use App\Models\Doc as Document;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

class CommentsPage extends BasePage
{
    public $document;

    public function __construct(Document $document)
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
        return route('documents.manage.comments', [$this->document], false);;
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@unhandledSection' => '.unhandled',
            '@handledSection' => '.handled',
        ];
    }

    public function getCommentRowSelector($comment)
    {
        return "#comment-{$comment->id}";
    }

    public function onCommentRow(Browser $browser, $comment, $fn)
    {
        $browser
            ->with($this->getCommentRowSelector($comment), $fn)
            ;
    }
}
