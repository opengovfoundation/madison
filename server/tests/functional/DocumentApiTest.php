<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\User;
use App\Models\Group;
use App\Models\Doc;

class DocumentApiTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test getting back all user documents.
     *
     * GET /api/user/:id/docs
     */
    public function testGetAllUserDocs()
    {
    //    $user = factory(User::class)->create();
    //    $group = factory(Group::class)->create();
    //    $group->addMember($user->id, Group::ROLE_OWNER);
    //    $individual_group = Group::createIndividualGroup($user->id);

        /**
         * TODO: These should be getting set from the factory
         */
    //    $doc1 = factory(Doc::class)->make();
    //    $doc1->title = 'Doc 1';

    //    $doc2 = factory(Doc::class)->make();
    //    $doc2->title = 'Doc 2';

    //    $doc3 = factory(Doc::class)->make();
    //    $doc3->title = 'Doc 3';

    //    $group->docs()->saveMany([ $doc1, $doc2 ]);
    //    $individual_group->docs()->save($doc3);

    //    // create docs for each group
    //    // -- set publish_state on some to private / unpublished
    //    // log in as user
    //    // get all docs
    //    $this->actingAs($user)
    //        ->json('GET', "/api/user/{$user->id}/docs")
    //        ->seeJson(['name' => $individual_group->name])
    //        ->seeJson(['docs' => [$doc3->toArray()]])
    //        ->seeJson(['name' => $group->name])
    //        ->seeJson(['docs' => [$doc1->toArray(), $doc2->toArray()]]);
    }

}
