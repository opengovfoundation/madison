<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Sponsor;
use App\Models\Doc as Document;
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
                ->visit((new SponsorPages\CreatePage)->url())
                ->assertRouteIs('login')
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

            $browser->assertRouteIs('sponsors.awaiting-approval');

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

    public function testNoSponsorShowsMessage()
    {
        $user = factory(User::class)->create();

        $this->browse(function ($browser) use ($user) {
            $browser
                ->loginAs($user)
                ->visitRoute('users.sponsors.index', $user)
                ->assertSee('You are not a sponsor')
                ->assertDontSee('table')
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

    public function testSponsorOwnerCanCreateDocuments()
    {
        $owner = factory(User::class)->create();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser($owner);

        $this->userCanCreateDocumentsForSponsor($owner, $sponsor);
    }

    public function testSponsorEditorCanCreateDocuments()
    {
        $owner = factory(User::class)->create();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser($owner);

        $editor = factory(User::class)->create();
        $sponsor->addMember($editor->id, Sponsor::ROLE_EDITOR);

        $this->userCanCreateDocumentsForSponsor($editor, $sponsor);
    }

    public function testAdminCanCreateDocuments()
    {
        $owner = factory(User::class)->create();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser($owner);

        $admin = factory(User::class)->create()->makeAdmin();

        $this->userCanCreateDocumentsForSponsor($admin, $sponsor);
    }

    public function testSponsorStaffCannotCreateDocuments()
    {
        $owner = factory(User::class)->create();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser($owner);

        $staff = factory(User::class)->create();
        $sponsor->addMember($staff->id, Sponsor::ROLE_STAFF);

        $this->browse(function ($browser) use ($staff, $sponsor) {
            $browser
                ->loginAs($staff)
                ->visit(new SponsorPages\DocumentsPage($sponsor))
                ->assertMissing('@newDocumentButton')
                ;
        });
    }

    public function testSponsorOwnerCanEditSponsorSettings()
    {
        $user = factory(User::class)->create();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser($user);

        $this->userCanEditSponsor($user, $sponsor);
    }

    public function testAdminCanEditSponsorSettings()
    {
        $owner = factory(User::class)->create();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser($owner);

        $admin = factory(User::class)->create()->makeAdmin();

        $this->userCanEditSponsor($admin, $sponsor);
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
                ->assertSee('403')
                ;
        });
    }

    public function testSponsorOwnerCanAddMembers()
    {
        $owner = factory(User::class)->create();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser($owner);

        $this->userCanAddMembersToSponsor($owner, $sponsor);
    }

    public function testAdminCanAddMembers()
    {
        $owner = factory(User::class)->create();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser($owner);

        $admin = factory(User::class)->create()->makeAdmin();

        $this->userCanAddMembersToSponsor($admin, $sponsor);
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
                ->assertSee('403') // 403 status
                ;
        });
    }

    public function testSponsorOwnerCanRemoveMembers()
    {
        $owner = factory(User::class)->create();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser($owner);

        $this->userCanRemoveMembersFromSponsor($owner, $sponsor);
    }

    public function testAdminCanRemoveMembers()
    {
        $owner = factory(User::class)->create();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser($owner);

        $admin = factory(User::class)->create()->makeAdmin();

        $this->userCanRemoveMembersFromSponsor($admin, $sponsor);
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
        $sponsor = FactoryHelpers::createActiveSponsorWithUser($owner);

        $this->userCanUpdateSponsorMemberRoles($owner, $sponsor);
    }

    public function testAdminCanUpdateMemberRoles()
    {
        $owner = factory(User::class)->create();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser($owner);

        $admin = factory(User::class)->create()->makeAdmin();

        $this->userCanUpdateSponsorMemberRoles($admin, $sponsor);
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

    public function testSponsorOwnerRedirectedToPendingPageIfSponsorNotApproved()
    {
        $owner = factory(User::class)->create();
        $sponsor = factory(Sponsor::class)->create([
            'status' => Sponsor::STATUS_PENDING,
        ]);
        $sponsor->addMember($owner->id, Sponsor::ROLE_OWNER);

        $this->browse(function ($browser) use ($owner, $sponsor) {
            $browser
                ->loginAs($owner)
                ;

            $sponsorPages = [
                (new SponsorPages\EditPage($sponsor))->url(),
                (new SponsorPages\MembersPage($sponsor))->url(),
                route('sponsors.documents.index', [$sponsor], false),
            ];

            foreach ($sponsorPages as $page) {
                $browser
                    ->visit($page)
                    ->assertRouteIs('sponsors.awaiting-approval')
                    ;
            }
        });
    }

    public function testAdminNotRedirectedToPendingPageIfSponsorNotApproved()
    {
        $admin = factory(User::class)->create()->makeAdmin();
        $sponsor = factory(Sponsor::class)->create([
            'status' => Sponsor::STATUS_PENDING,
        ]);

        $this->browse(function ($browser) use ($admin, $sponsor) {
            $browser
                ->loginAs($admin)
                ;

            $sponsorPages = [
                (new SponsorPages\EditPage($sponsor))->url(),
                (new SponsorPages\MembersPage($sponsor))->url(),
                route('sponsors.documents.index', [$sponsor], false),
            ];

            foreach ($sponsorPages as $page) {
                $browser
                    ->visit($page)
                    ->assertPathIs($page)
                    ;
            }
        });
    }

    public function testOwnerCanDeleteDocuments()
    {
        $owner = factory(User::class)->create();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser($owner);
        $document = FactoryHelpers::createPublishedDocumentForSponsor($sponsor);

        $this->browse(function ($browser) use ($owner, $sponsor, $document) {
            $browser
                ->loginAs($owner)
                ->visit(new SponsorPages\DocumentsPage($sponsor))
                ->deleteDocumentAndAssertSuccess($document)
                ;

            $document = $document->fresh();
            $this->assertTrue($document->publish_state === Document::PUBLISH_STATE_DELETED_USER);
            $this->assertTrue($document->deleted_at !== null);
        });
    }

    public function testEditorCanDeleteDocuments()
    {
        $editor = factory(User::class)->create();

        $sponsor = FactoryHelpers::createActiveSponsorWithUser(factory(User::class)->create());
        $sponsor->addMember($editor->id, Sponsor::ROLE_EDITOR);

        $document = FactoryHelpers::createPublishedDocumentForSponsor($sponsor);

        $this->browse(function ($browser) use ($editor, $sponsor, $document) {
            $browser
                ->loginAs($editor)
                ->visit(new SponsorPages\DocumentsPage($sponsor))
                ->deleteDocumentAndAssertSuccess($document)
                ;

            $document = $document->fresh();
            $this->assertTrue($document->publish_state === Document::PUBLISH_STATE_DELETED_USER);
            $this->assertTrue($document->deleted_at !== null);
        });
    }

    public function testStaffCannotDeleteDocuments()
    {
        $staff = factory(User::class)->create();

        $sponsor = FactoryHelpers::createActiveSponsorWithUser(factory(User::class)->create());
        $sponsor->addMember($staff->id, Sponsor::ROLE_STAFF);

        FactoryHelpers::createPublishedDocumentForSponsor($sponsor);

        $this->browse(function ($browser) use ($staff, $sponsor) {
            $browser
                ->loginAs($staff)
                ->visit(new SponsorPages\DocumentsPage($sponsor))
                ->assertDontSee('.delete-document')
                ->assertDontSee(trans('messages.document.view_deleted'))
                ;
        });
    }

    public function testAdminCanDeleteDocuments()
    {
        $admin = factory(User::class)->create()->makeAdmin();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser(factory(User::class)->create());
        $document = FactoryHelpers::createPublishedDocumentForSponsor($sponsor);

        $this->browse(function ($browser) use ($admin, $sponsor, $document) {
            $browser
                ->loginAs($admin)
                ->visit(new SponsorPages\DocumentsPage($sponsor))
                ->deleteDocumentAndAssertSuccess($document)
                ;

            $document = $document->fresh();
            $this->assertTrue($document->publish_state === Document::PUBLISH_STATE_DELETED_ADMIN);
            $this->assertTrue($document->deleted_at !== null);
        });
    }

    public function testNonAdminCannotSeeAdminDeletedDocuments()
    {
        $owner = factory(User::class)->create();
        $admin = factory(User::class)->create()->makeAdmin();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser($owner);
        $document = FactoryHelpers::createPublishedDocumentForSponsor($sponsor);

        $this->browse(function ($browser) use ($admin, $owner, $sponsor, $document) {
            $browser
                ->loginAs($admin)
                ->visit(new SponsorPages\DocumentsPage($sponsor))
                ->deleteDocumentAndAssertSuccess($document)
                ;

            $document = $document->fresh();
            $this->assertTrue($document->publish_state === Document::PUBLISH_STATE_DELETED_ADMIN);
            $this->assertTrue($document->deleted_at !== null);

            $browser
                ->loginAs($owner)
                ->visit(new SponsorPages\DocumentsPage($sponsor))
                ->clickLink(trans('messages.document.view_deleted'))
                ->assertDontSee($document->title)
                ;
        });
    }

    public function testOwnerCanRestoreDocuments()
    {
        $owner = factory(User::class)->create();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser($owner);
        $document = FactoryHelpers::createPublishedDocumentForSponsor($sponsor);

        $this->browse(function ($browser) use ($owner, $sponsor, $document) {
            $browser
                ->loginAs($owner)
                ->visit(new SponsorPages\DocumentsPage($sponsor))
                ->deleteDocumentAndAssertSuccess($document)
                ;

            $document = $document->fresh();
            $this->assertTrue($document->publish_state === Document::PUBLISH_STATE_DELETED_USER);
            $this->assertTrue($document->deleted_at !== null);

            $browser
                ->clickLink(trans('messages.document.view_deleted'))
                ->restoreDocumentAndAssertSuccess($document)
                ;

            $document = $document->fresh();
            $this->assertTrue($document->publish_state === Document::PUBLISH_STATE_UNPUBLISHED);
            $this->assertTrue($document->deleted_at === null);
        });
    }

    public function testEditorCanRestoreDocuments()
    {
        $editor = factory(User::class)->create();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser(factory(User::class)->create());
        $document = FactoryHelpers::createPublishedDocumentForSponsor($sponsor);
        $sponsor->addMember($editor->id, Sponsor::ROLE_EDITOR);

        $this->browse(function ($browser) use ($editor, $sponsor, $document) {
            $browser
                ->loginAs($editor)
                ->visit(new SponsorPages\DocumentsPage($sponsor))
                ->deleteDocumentAndAssertSuccess($document)
                ;

            $document = $document->fresh();
            $this->assertTrue($document->publish_state === Document::PUBLISH_STATE_DELETED_USER);
            $this->assertTrue($document->deleted_at !== null);

            $browser
                ->clickLink(trans('messages.document.view_deleted'))
                ->restoreDocumentAndAssertSuccess($document)
                ;

            $document = $document->fresh();
            $this->assertTrue($document->publish_state === Document::PUBLISH_STATE_UNPUBLISHED);
            $this->assertTrue($document->deleted_at === null);
        });
    }

    public function testAdminCanRestoreAdminDeletedDocuments()
    {
        $admin = factory(User::class)->create()->makeAdmin();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser(factory(User::class)->create());
        $document = FactoryHelpers::createPublishedDocumentForSponsor($sponsor);

        $this->browse(function ($browser) use ($admin, $sponsor, $document) {
            $browser
                ->loginAs($admin)
                ->visit(new SponsorPages\DocumentsPage($sponsor))
                ->deleteDocumentAndAssertSuccess($document)
                ;

            $document = $document->fresh();
            $this->assertTrue($document->publish_state === Document::PUBLISH_STATE_DELETED_ADMIN);
            $this->assertTrue($document->deleted_at !== null);

            $browser
                ->clickLink(trans('messages.document.view_deleted'))
                ->restoreDocumentAndAssertSuccess($document)
                ;

            $document = $document->fresh();
            $this->assertTrue($document->publish_state === Document::PUBLISH_STATE_UNPUBLISHED);
            $this->assertTrue($document->deleted_at === null);
        });
    }

    public function userCanEditSponsor($user, $sponsor)
    {
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

    public function userCanAddMembersToSponsor($user, $sponsor)
    {
        $newUser = factory(User::class)->create();
        $role = Sponsor::ROLE_EDITOR;

        $this->browse(function ($browser) use ($user, $sponsor, $newUser, $role) {
            $browser
                ->loginAs($user)
                ->visit(new SponsorPages\MembersPage($sponsor))
                ->assertDontSeeIn('table', $newUser->display_name)
                ->click('@addMemberButton')
                ->assertRouteIs('sponsors.members.create', $sponsor)
                ->type('email', $newUser->email)
                ->select('role', $role)
                ->press(trans('messages.sponsor_member.add_user'))
                ->assertRouteIs('sponsors.members.index', $sponsor)
                ->assertSeeIn('table', $newUser->display_name)
                ->assertSeeIn('tr#user-'.$newUser->id, trans('messages.sponsor_member.roles.'.$role))
                ;
        });
    }

    public function userCanRemoveMembersFromSponsor($user, $sponsor)
    {
        $editor = factory(User::class)->create();
        $sponsor->addMember($editor->id, Sponsor::ROLE_EDITOR);

        $this->browse(function ($browser) use ($user, $sponsor, $editor) {
            $browser
                ->loginAs($user)
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

    public function userCanUpdateSponsorMemberRoles($user, $sponsor)
    {
        $editor = factory(User::class)->create();
        $sponsor->addMember($editor->id, Sponsor::ROLE_EDITOR);

        $newRole = Sponsor::ROLE_STAFF;

        $this->browse(function ($browser) use ($user, $sponsor, $editor, $newRole) {
            $browser
                ->loginAs($user)
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

    public function userCanCreateDocumentsForSponsor($user, $sponsor)
    {
        $documentData = factory(Document::class)->make();

        $this->browse(function ($browser) use ($user, $sponsor, $documentData) {
            $browser
                ->loginAs($user)
                ->visit(new SponsorPages\DocumentsPage($sponsor))
                ->assertVisible('@newDocumentButton')
                ->click('@newDocumentButton')
                ->waitFor('@newDocumentModal')
                ->type('title', $documentData->title)
                ->press(trans('messages.submit'))
                ->assertRouteIs('documents.manage.settings', ['slug' => str_slug($documentData->title)])
                ->assertInputValue('title', $documentData->title)
                ->assertInputValue('slug', str_slug($documentData->title))
                ;
        });
    }
}
