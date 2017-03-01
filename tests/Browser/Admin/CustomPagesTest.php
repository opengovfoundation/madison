<?php

namespace Tests\Browser;

use App\Models\Page;
use App\Models\PageContent;
use App\Models\User;
use Tests\Browser\AdminTestCase;
use Tests\Browser\Pages\Admin;
use Tests\Browser\Pages\CustomPages;
use Laravel\Dusk\Browser;

class CustomPagesTest extends AdminTestCase
{
    public function testAdminPage()
    {
        $this->browse(function ($browser) {
            $page = new Admin\CustomPagesPage;

            $this->assertNonAdminsDenied($browser, $page);

            $browser
                ->loginAs($this->admin)
                ;

            // create button
            $browser
                ->visit($page)
                ->click('@newPageBtn')
                ->assertRouteIs('pages.create')
                ;

            $customPage = factory(Page::class)->create();
            $customPage->content()->save(factory(PageContent::class)->make());

            // edit button
            $browser
                ->visit($page)
                ->click('@editPageBtn')
                ->assertRouteIs('pages.edit', $customPage)
                ;

            // delete button
            $browser
                ->visit($page)
                ->assertSee($customPage->nav_title)
                ->click('@destroyPageBtn')
                ->assertVisible('.alert.alert-info') // some success
                ->assertRouteIs('admin.pages.index')
                ->assertDontSee($customPage->nav_title)
                ;

            $this->assertEquals(null, Page::find($customPage->id));
        });
    }

    public function testCreate()
    {
        $this->browse(function ($browser) {
            $page = new CustomPages\CreatePage;

            $this->assertNonAdminsDenied($browser, $page);

            $browser
                ->loginAs($this->admin)
                ->visit($page)
                ->type('nav_title', 'test')
                ->press('Submit')
                ;

            $customPage = Page::first();

            $browser
                ->assertRouteIs('pages.edit', $customPage)
                ->assertInputValue('url', '/test')
                ->assertInputValue('nav_title', 'test')
                ->assertInputValue('page_title', 'test')
                ->assertInputValue('header', 'test')
                ->assertChecked('header_nav_link')
                ->assertNotChecked('footer_nav_link')
                ->assertNotChecked('external')
                ->assertInputValue('page_content', 'New page content')
                ;
        });
    }

    public function testEdit()
    {
        $this->browse(function ($browser) {
            $customPage = factory(Page::class)->create();
            $customPage->content()->save(factory(PageContent::class)->make());
            $page = new CustomPages\EditPage($customPage);

            $this->assertNonAdminsDenied($browser, $page);

            $fakeCustomPage = factory(Page::class)->states('randomize')->make();
            $fakeContent = factory(PageContent::class)->make();

            $browser
                ->loginAs($this->admin)
                ->visit($page)
                ->type('nav_title', $fakeCustomPage->nav_title)
                ->type('page_title', $fakeCustomPage->page_title)
                ->type('header', $fakeCustomPage->header)
                ->type('page_content', $fakeContent->content)
                ->press('Submit')
                ->assertVisible('.alert.alert-info') // some success
                ->assertInputValue('nav_title', $fakeCustomPage->nav_title)
                ->assertInputValue('page_title', $fakeCustomPage->page_title)
                ->assertInputValue('header', $fakeCustomPage->header)
                ->assertInputValue('page_content', $fakeContent->content)
                ;

            $customPage = $customPage->fresh();
            $this->assertEquals($fakeCustomPage->nav_title, $customPage->nav_title);
            $this->assertEquals($fakeCustomPage->page_title, $customPage->page_title);
            $this->assertEquals($fakeCustomPage->header, $customPage->header);
            $this->assertEquals($fakeContent->content, $customPage->content->content);

            // leading slash gets added to urls if needed
            $browser
                ->type('url', 'test')
                ->press('Submit')
                ->assertVisible('.alert.alert-info') // some success
                ->assertInputValue('url', '/test')
                ;

            $customPage = $customPage->fresh();
            $this->assertEquals('/test', $customPage->url);

            $browser
                ->type('url', '/test2')
                ->press('Submit')
                ->assertVisible('.alert.alert-info') // some success
                ->assertInputValue('url', '/test2')
                ;

            $customPage = $customPage->fresh();
            $this->assertEquals('/test2', $customPage->url);
        });
    }

    public function testEditExternal()
    {
        $this->browse(function ($browser) {
            $customPage = factory(Page::class)->states('external')->create();
            $page = new CustomPages\EditPage($customPage);

            $this->assertNonAdminsDenied($browser, $page);

            $browser
                ->loginAs($this->admin)
                ->visit($page)
                ->assertChecked('external')
                ->assertMissing('#page_title')
                ->assertMissing('#header')
                ->assertMissing('#page_content')
                ->type('nav_title', 'test')
                ->press('Submit')
                ->assertVisible('.alert.alert-info') // some success
                ->assertInputValue('nav_title', 'test')
                ->assertChecked('external')
                ;

            $customPage = $customPage->fresh();
            $this->assertEquals('test', $customPage->nav_title);
        });
    }

    public function testEditExternalTogglesFields()
    {
        $this->browse(function ($browser) {
            $customPage = factory(Page::class)->create();
            $page = new CustomPages\EditPage($customPage);

            $this->assertNonAdminsDenied($browser, $page);

            $browser
                ->loginAs($this->admin)
                ->visit($page)
                ->assertVisible('#page_title')
                ->assertVisible('#header')
                ->assertVisible('#page_content')
                ->check('external')
                ->assertMissing('#page_title')
                ->assertMissing('#header')
                ->assertMissing('#page_content')
                ->uncheck('external')
                ->assertVisible('#page_title')
                ->assertVisible('#header')
                ->assertVisible('#page_content')
                ;
        });
    }
}
