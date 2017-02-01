<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\User;
use App\Models\Sponsor;
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
        $sponsor = factory(Sponsor::class)->create();
        $sponsor->addMember($user->id, Sponsor::ROLE_OWNER);
        $individual_sponsor = Sponsor::createIndividualSponsor($user->id);

        /**
         * TODO: These should be getting set from the factory
         */
        $doc1 = factory(Doc::class)->create();
        $doc2 = factory(Doc::class)->create();
        $doc3 = factory(Doc::class)->create();

        $sponsor->docs()->saveMany([ $doc1, $doc2 ]);
        $individual_sponsor->docs()->save($doc3);

        //$response = $this->actingAs($user)
        //    ->get("/api/user{$user->id}/docs", ['content-type' => 'application/json'])->response;

        $data = $this->actingAs($user)
            ->json('GET', "/api/user/{$user->id}/docs")
            ->getContent();

        $response = json_decode($data, true);

        $this->assertEquals(count($response['sponsors']), 2);
        $this->assertEquals(count($response['sponsors'][0]['docs']), 2);
        $this->assertEquals(count($response['sponsors'][1]['docs']), 1);

        //$this->actingAs($user)
        //    ->json('GET', "/api/user/{$user->id}/docs")
        //    ->seeJson([
        //        'sponsors' => [
        //            [
        //                'name' => $individual_sponsor->name,
        //                'docs' => [$doc3->toArray()]
        //            ],
        //            [
        //                'name' => $sponsor->name,
        //                'docs' => [$doc1->toArray(), $doc2->toArray()]
        //            ]
        //        ]
        //    ]);
    }

}
