<?php

namespace Tests\Feature\Document\Manage;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Doc as Document;
use App\Models\DocContent;
use App\Models\User;
use League\Csv\Reader;
use Tests\FactoryHelpers;

class CommentsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @runInSeparateProcess
     */
    public function testDownloadAllCommentsCSV()
    {
        $sponsorOwner = factory(User::class)->create();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser($sponsorOwner);

        $document = factory(Document::class)->create([
            'publish_state' => Document::PUBLISH_STATE_UNPUBLISHED,
        ]);
        $document->content()->save(factory(DocContent::class)->make());
        $document->sponsors()->save($sponsor);

        $comment = FactoryHelpers::createComment($sponsorOwner, $document);

        ob_start();
        $response = $this
            ->actingAs($sponsorOwner)
            ->get(route('documents.comments.index', [$document, 'download' => 'csv', 'all' => true], false))
            ;
        $output = ob_get_clean();

        $response
            ->assertStatus(200)
            // Can't assert these due to how this stuff works, but would like to
            // ->assertHeader('content-type', 'text/csv')
            // ->assertHeader('content-disposition', 'attachment: filename="comments.csv"')
            ;

        $csv = Reader::createFromString($output)->fetchAll();
        $this->assertEquals(['first_name','last_name','quote','text','type','created_at'], $csv[0]);
        $this->assertEquals([
            $comment->user->fname,
            $comment->user->lname,
            '',
            $comment->annotationType->content,
            'comment',
            $comment->created_at->toRfc3339String(),
        ], $csv[1]);
    }
}
