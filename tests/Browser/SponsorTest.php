<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Sponsor;
use Tests\Browser\Pages\Sponsor as SponsorPages;
use Tests\DuskTestCase;
use Tests\FactoryHelpers;
use Illuminate\Support\Facades\Event;
use Laravel\Dusk\Browser;

class SponsorTest extends DuskTestCase
{

    public function testMustBeLoggedInToCreateSponsor()
    {
        $this->browse(function ($browser) {
            $browser
                ->visit(new SponsorPages\CreatePage)
                ->assertSee('unauthorized')
                ;
        });
    }

    public function testUserCanCreateSponsor()
    {
        // TODO: currently doesn't work with Dusk
        //Event::fake();

        $sponsorAttrs = factory(Sponsor::class)->make();
        $user = factory(User::class)->create();

        $this->browse(function ($browser) use ($sponsorAttrs, $user) {
            $browser
                ->loginAs($user)
                ->visit(new SponsorPages\CreatePage)
                ->fillNewSponsorForm($sponsorAttrs)
                ->click('@submitBtn')
                ->assertVisible('.alert.alert-info') // some success
                ;

            $sponsor = Sponsor::first();

            $browser->assertRouteIs('sponsors.members.index', $sponsor);

            $this->assertEquals($sponsor->status, Sponsor::STATUS_PENDING);

            $this->assertEquals($sponsor->name, $sponsorAttrs->name);
            $this->assertEquals($sponsor->display_name, $sponsorAttrs->display_name);
            $this->assertEquals($sponsor->address1, $sponsorAttrs->address1);
            $this->assertEquals($sponsor->city, $sponsorAttrs->city);
            $this->assertEquals($sponsor->state, $sponsorAttrs->state);
            $this->assertEquals($sponsor->postal_code, $sponsorAttrs->postal_code);
            $this->assertEquals($sponsor->phone, $sponsorAttrs->phone);

            // TODO: currently doesn't work with Dusk
            //Event::assertDispatched('App\Events\SponsorCreated', function ($e) use ($sponsor) {
            //    return $e->sponsor->id === $sponsor->id;
            //});
        });
    }

    public function testRedirectedToSoleSponsor()
    {
        $user = factory(User::class)->create();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser($user);

        $this->browse(function ($browser) use ($user, $sponsor) {
            $browser
                ->loginAs($user)
                ->visitRoute('users.sponsors.index', $user)
                ->assertRouteIs('sponsors.documents.index', $sponsor)
                ;
        });
    }

    public function testNotRedirectedWithMultiSponsor()
    {
        $user = factory(User::class)->create();
        $sponsor1 = FactoryHelpers::createActiveSponsorWithUser($user);
        $sponsor2 = FactoryHelpers::createActiveSponsorWithUser($user);

        $this->browse(function ($browser) use ($user) {
            $browser
                ->loginAs($user)
                ->visitRoute('users.sponsors.index', $user)
                ->assertRouteIs('users.sponsors.index', $user)
                ;
        });
    }

    public function testShowRedirectsToDocumentList()
    {
        $user = factory(User::class)->create();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser($user);

        $this->browse(function ($browser) use ($user, $sponsor) {
            $browser
                ->loginAs($user)
                ->visitRoute('sponsors.show', $sponsor)
                ->waitForText($sponsor->display_name)
                ->assertRouteIs('sponsors.documents.index', $sponsor)
                ;
        });
    }

    public function testSponsorOwnerCanEditSponsorSettings()
    {
        $user = factory(User::class)->create();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser($user);

        $newSponsorData = factory(Sponsor::class)->make();

        $this->browse(function ($browser) use ($user, $sponsor, $newSponsorData) {
            $browser
                ->loginAs($user)
                ->visit(new SponsorPages\EditPage($sponsor))
                ->assertFormHasDataForSponsor($sponsor)
                ->type('name', $newSponsorData->name)
                ->type('display_name', $newSponsorData->display_name)
                ->type('address1', $newSponsorData->address1)
                ->type('city', $newSponsorData->city)
                ->type('state', $newSponsorData->state)
                ->type('postal_code', $newSponsorData->postal_code)
                ->type('phone', $newSponsorData->phone)
                ->press('@submitBtn')
                ->assertRouteIs('sponsors.edit', $sponsor)
                ->assertFormHasDataForSponsor($sponsor->fresh())
                ;

            $sponsor = $sponsor->fresh();

            $this->assertEquals($sponsor->name, $newSponsorData->name);
            $this->assertEquals($sponsor->display_name, $newSponsorData->display_name);
            $this->assertEquals($sponsor->address1, $newSponsorData->address1);
            $this->assertEquals($sponsor->city, $newSponsorData->city);
            $this->assertEquals($sponsor->state, $newSponsorData->state);
            $this->assertEquals($sponsor->postal_code, $newSponsorData->postal_code);
            $this->assertEquals($sponsor->phone, $newSponsorData->phone);
        });
    }

    public function testNonOwnerCantEditSponsorSettings()
    {
        $owner = factory(User::class)->create();
        $editor = factory(User::class)->create();

        $sponsor = FactoryHelpers::createActiveSponsorWithUser($owner);
        $sponsor->addMember($editor->id, Sponsor::ROLE_EDITOR);

        $this->browse(function ($browser) use ($editor, $sponsor) {
            $browser
                ->loginAs($editor)
                ->visitRoute('sponsors.documents.index', $sponsor)
                ->assertDontSeeIn('#content .list-group', trans('messages.settings'))
                ->visitRoute('sponsors.edit', $sponsor)
                ->assertSee('unauthorized')
                ;
        });
    }

    public function testSponsorOwnerCanAddMembers()
    {
        $owner = factory(User::class)->create();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser($owner);

        $user = factory(User::class)->create();
        $role = Sponsor::ROLE_EDITOR;

        $this->browse(function ($browser) use ($owner, $sponsor, $user, $role) {
            $browser
                ->loginAs($owner)
                ->visit(new SponsorPages\MembersPage($sponsor))
                ->assertDontSeeIn('table', $user->display_name)
                ->click('@addMemberButton')
                ->assertRouteIs('sponsors.members.create', $sponsor)
                ->type('email', $user->email)
                ->select('role', $role)
                ->press(trans('messages.sponsor_member.add_user'))
                ->assertRouteIs('sponsors.members.index', $sponsor)
                ->assertSeeIn('table', $user->display_name)
                ->assertSeeIn('tr#user-'.$user->id, trans('messages.sponsor_member.roles.'.$role))
                ;
        });
    }

    public function testNonSponsorOwnerCannotAddMembers()
    {
        $owner = factory(User::class)->create();
        $editor = factory(User::class)->create();

        $sponsor = FactoryHelpers::createActiveSponsorWithUser($owner);
        $sponsor->addMember($editor->id, Sponsor::ROLE_EDITOR);

        $this->browse(function ($browser) use ($sponsor, $editor) {
            $browser
                ->loginAs($editor)
                ->visit(new SponsorPages\MembersPage($sponsor))
                ->assertMissing('@addMemberButton')
                ->visitRoute('sponsors.members.create', $sponsor)
                ->assertSee('Whoops, looks like something went wrong') // 403 status
                ;
        });
    }

    public function testSponsorOwnerCanRemoveMembers()
    {
        $owner = factory(User::class)->create();
        $editor = factory(User::class)->create();

        $sponsor = FactoryHelpers::createActiveSponsorWithUser($owner);
        $sponsor->addMember($editor->id, Sponsor::ROLE_EDITOR);

        $this->browse(function ($browser) use ($owner, $sponsor, $editor) {
            $browser
                ->loginAs($owner)
                ->visit(new SponsorPages\MembersPage($sponsor))
                ->assertSeeIn('table', $editor->display_name)
                ->with('tr#user-' . $editor->id, function ($userRow) {
                    $userRow->press('.remove');
                })
                ->assertVisible('.alert.alert-info') // some success
                ->assertRouteIs('sponsors.members.index', $sponsor)
                ->assertDontSeeIn('table', $editor->display_name)
                ;
        });
    }

    public function testNonSponsorOwnerCannotRemoveMembers()
    {
        $owner = factory(User::class)->create();
        $editor = factory(User::class)->create();

        $sponsor = FactoryHelpers::createActiveSponsorWithUser($owner);
        $sponsor->addMember($editor->id, Sponsor::ROLE_EDITOR);

        $this->browse(function ($browser) use ($sponsor, $editor) {
            $browser
                ->loginAs($editor)
                ->visit(new SponsorPages\MembersPage($sponsor))
                ->assertMissing('tr .remove')
                ;
        });
    }

    public function testSponsorOwnerCanUpdateMemberRoles()
    {
        $owner = factory(User::class)->create();
        $editor = factory(User::class)->create();

        $sponsor = FactoryHelpers::createActiveSponsorWithUser($owner);
        $sponsor->addMember($editor->id, Sponsor::ROLE_EDITOR);

        $newRole = Sponsor::ROLE_STAFF;

        $this->browse(function ($browser) use ($owner, $sponsor, $editor, $newRole) {
            $browser
                ->loginAs($owner)
                ->visit(new SponsorPages\MembersPage($sponsor))
                ->assertSeeIn('table', $editor->display_name)
                ->with('tr#user-' . $editor->id, function ($userRow) use ($newRole) {
                    $userRow->select('role', $newRole);
                })
                ->assertVisible('.alert.alert-info') // some success
                ->assertRouteIs('sponsors.members.index', $sponsor)
                ->assertSeeIn('tr#user-' . $editor->id, trans('messages.sponsor_member.roles.'.$newRole))
                ;
        });
    }

    public function testNonSponsorOwnerCannotUpdateMemberRoles()
    {
        $owner = factory(User::class)->create();
        $editor = factory(User::class)->create();

        $sponsor = FactoryHelpers::createActiveSponsorWithUser($owner);
        $sponsor->addMember($editor->id, Sponsor::ROLE_EDITOR);

        $this->browse(function ($browser) use ($sponsor, $editor) {
            $browser
                ->loginAs($editor)
                ->visit(new SponsorPages\MembersPage($sponsor))
                ->assertMissing('tr select')
                ;
        });
    }

    public function testEnsuresAtLeastOneOwner()
    {
        $owner = factory(User::class)->create();

        $sponsor = FactoryHelpers::createActiveSponsorWithUser($owner);
        $sponsor->addMember(factory(User::class)->create()->id, Sponsor::ROLE_EDITOR);

        $originalRole = Sponsor::ROLE_OWNER;
        $newRole = Sponsor::ROLE_STAFF;

        $this->browse(function ($browser) use ($sponsor, $owner, $originalRole, $newRole) {
            $browser
                ->loginAs($owner)
                ->visit(new SponsorPages\MembersPage($sponsor))
                ->with('tr#user-' . $owner->id, function ($userRow) use ($originalRole, $newRole) {
                    $userRow->select('role', $newRole);
                })
                ->assertSeeIn('.alert', trans('messages.sponsor_member.need_owner'))
                ->assertSeeIn('tr#user-' . $owner->id, trans('messages.sponsor_member.roles.' . $originalRole))
                ;
        });
    }
}
