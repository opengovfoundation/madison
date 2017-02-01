<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\User;
use App\Models\Role;
use App\Models\Page;
use App\Models\PageContent;

class PageApiTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test creating a new page.
     *
     * POST /api/pages
     */
    public function testCreatePage()
    {
        $user = factory(User::class)->create();
        $admin_role = factory(Role::class, 'admin_role')->create();
        $user->attachRole($admin_role);

        $this->actingAs($user)
            ->json('POST', '/api/pages', ['nav_title' => 'A New Page'])
            ->assertJson([
                'nav_title' => 'A New Page',
                'page_title' => 'A New Page',
                'url' => '/a-new-page',
                'header' => 'A New Page',
                'header_nav_link' => true,
                'footer_nav_link' => false,
                'external' => false
            ]);

        $page = Page::where('nav_title', 'A New Page')->first();

        // Should also create base page content
        $this->json('GET', "/api/pages/{$page->id}/content?format=markdown")
            ->assertJson([ 'content' => 'New page content' ]);

        $this->assertDatabaseHas('pages', [
            'nav_title' => 'A New Page',
            'page_title' => 'A New Page',
            'url' => '/a-new-page',
            'header' => 'A New Page',
            'header_nav_link' => true,
            'footer_nav_link' => false,
            'external' => false
        ]);

        $this->assertDatabaseHas('page_contents', [
            'content' => 'New page content'
        ]);
    }

    /**
     * Test return list of pages.
     *
     * GET /api/pages
     */
    public function testGetListOfPages()
    {
        $page1 = factory(Page::class)->create();
        $page2 = factory(Page::class)->create();

        // $this->json('GET', '/api/pages')
        //     ->assertJson($page1->toArray())
        //     ->assertJson($page2->toArray());
    }

    public function testGetOnlyHeaderPages()
    {
        $page1 = factory(Page::class)->create([
            'header_nav_link' => true,
            'footer_nav_link' => false
        ]);
        $page2 = factory(Page::class)->create([
            'header_nav_link' => true,
            'footer_nav_link' => false
        ]);
        $page3 = factory(Page::class)->create([
            'header_nav_link' => false,
            'footer_nav_link' => true
        ]);

        $this->json('GET', '/api/pages?header_nav_link=true')
            ->assertExactJson([
                $page1->toArray(),
                $page2->toArray()
            ]);

        $this->json('GET', '/api/pages?footer_nav_link=true')
            ->assertExactJson([$page3->toArray()]);
    }

    /**
     * Test get one specific page.
     *
     * GET /api/pages/:id
     */
    public function testGetOnePage()
    {
        $page = factory(Page::class)->create();

        $this->json('GET', "/api/pages/{$page->id}")
            ->assertJson($page->toArray());
    }

    /**
     * Test only admins can create new pages.
     *
     * POST /api/pages
     */
    public function testOnlyAdminCanCreatePage()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->json('POST', '/api/pages', ['nav_title' => 'A New Page'])
            ->assertStatus(403);
    }

    /**
     * Test admin can update page.
     *
     * PUT /api/pages/:id
     */
    public function testUpdatePage()
    {
        $admin = factory(User::class)->create();
        $admin_role = factory(Role::class, 'admin_role')->create();
        $admin->attachRole($admin_role);

        $page = factory(Page::class)->create();

        $this->actingAs($admin)
            ->json('PUT', "/api/pages/{$page->id}",
                array_merge(
                    $page->toArray(),
                    ['nav_title' => 'New Title!']
                )
            )->assertJson([
                'nav_title' => 'New Title!'
            ]);

    }

    /**
     * Test only admins can update pages.
     *
     * PUT /api/pages/:id
     */
    public function testOnlyAdminCanUpdatePage()
    {
        $user = factory(User::class)->create();
        $page = factory(Page::class)->create();

        $this->actingAs($user)
            ->json('PUT', "/api/pages/{$page->id}",
                array_merge(
                    ['nav_title' => 'New Title!'],
                    $page->toArray()
                )
            )->assertStatus(403);
    }

    /**
     * Test PUT route requires whole object.
     *
     * PUT /api/pages/:id
     */
    public function testWholeObjectRequiredForUpdate()
    {
        $user = factory(User::class)->create();
        $admin_role = factory(Role::class, 'admin_role')->create();
        $user->attachRole($admin_role);

        $page = factory(Page::class)->create();

        $page_attrs = $page->toArray();

        unset($page_attrs['nav_title']);

        $this->actingAs($user)
            ->json('PUT', "/api/pages/{$page->id}", $page_attrs)
            ->assertStatus(422);
    }

    /**
     * Test that non-admins can NOT destroy pages.
     *
     * DELETE /api/pages/:id
     */
    public function testNonAdminCantDestroyPage()
    {
        $user = factory(User::class)->create();
        $page = factory(Page::class)->create();

        $this->actingAs($user)
            ->json('DELETE', "/api/pages/{$page->id}")
            ->assertStatus(403);
    }

    /**
     * Test that admins can destroy pages.
     *
     * DELETE /api/pages/:id
     */
    public function testAdminCanDestroyPage()
    {
        $user = factory(User::class)->create();
        $admin_role = factory(Role::class, 'admin_role')->create();
        $user->attachRole($admin_role);

        $page = factory(Page::class)->create();

        $this->actingAs($user)
            ->json('DELETE', "/api/pages/{$page->id}")
            ->assertStatus(200);

        $this->assertDatabaseMissing('pages', [ 'id' => $page->id ]);
        $this->assertDatabaseMissing('page_contents', [ 'page_id' => $page->id ]);
    }

    /**
     * Test getting page content in HTML.
     *
     * GET /api/pages/:id/content?format=html
     */
    public function testGetPageContentHTML()
    {
        $page = factory(Page::class)->create();
        $content = factory(PageContent::class)->create([
            'page_id' => $page->id
        ]);

        $this->json('GET', "/api/pages/{$page->id}/content?format=html")
            ->assertJson([ 'content' => $content->html() ]);
    }

    /**
     * Test getting page content, default of markdown
     *
     * GET /api/pages/:id/content
     */
    public function testGetPageContent()
    {
        $page = factory(Page::class)->create();
        $content = factory(PageContent::class)->create([
            'page_id' => $page->id
        ]);

        $this->json('GET', "/api/pages/{$page->id}/content")
            ->assertJson([ 'content' => $content->markdown() ]);
    }

    public function testUpdatePageContent()
    {
        $user = factory(User::class)->create();
        $admin_role = factory(Role::class, 'admin_role')->create();
        $user->attachRole($admin_role);

        $page = factory(Page::class)->create();
        $content = factory(PageContent::class)->create([
            'page_id' => $page->id
        ]);

        $this->actingAs($user)
            ->json('PUT', "/api/pages/{$page->id}/content", [
                'content' => 'New page content!'
            ])->assertJson([
                'content' => 'New page content!'
            ]);

        $this->json('GET', "/api/pages/{$page->id}/content")
            ->assertJson([ 'content' => 'New page content!' ]);
    }
}
