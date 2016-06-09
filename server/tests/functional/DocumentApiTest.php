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
        $user = factory(User::class)->create();
        $group = factory(Group::class)->create();
        $group->addMember($user->id, Group::ROLE_OWNER);
        $individual_group = Group::createIndividualGroup($user->id);

        /**
         * TODO: These should be getting set from the factory
         */
        $doc1 = factory(Doc::class)->create();
        $doc2 = factory(Doc::class)->create();
        $doc3 = factory(Doc::class)->create();

        $group->docs()->saveMany([ $doc1, $doc2 ]);
        $individual_group->docs()->save($doc3);

        //$response = $this->actingAs($user)
        //    ->get("/api/user{$user->id}/docs", ['content-type' => 'application/json'])->response;

        $data = $this->actingAs($user)
            ->json('GET', "/api/user/{$user->id}/docs")
            ->response->getContent();

        $response = json_decode($data, true);

        $this->assertEquals(count($response['groups']), 2);
        $this->assertEquals(count($response['groups'][0]['docs']), 2);
        $this->assertEquals(count($response['groups'][1]['docs']), 1);

        //$this->actingAs($user)
        //    ->json('GET', "/api/user/{$user->id}/docs")
        //    ->seeJson([
        //        'groups' => [
        //            [
        //                'name' => $individual_group->name,
        //                'docs' => [$doc3->toArray()]
        //            ],
        //            [
        //                'name' => $group->name,
        //                'docs' => [$doc1->toArray(), $doc2->toArray()]
        //            ]
        //        ]
        //    ]);
    }

}
