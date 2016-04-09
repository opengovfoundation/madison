<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Role;

class PageApiTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test creating a new page.
     */
    public function testCreatePage()
    {
        $user = factory(App\Models\User::class)->create();
        $admin_role = Role::create(['name' => Role::ROLE_ADMIN]);
        $user->attachRole($admin_role);

        $this->actingAs($user)
            ->post('/api/pages', ['nav_title' => 'A New Page'])
            ->seeJson([
                'nav_title' => 'A New Page',
                'page_title' => 'A New Page',
                'url' => 'a-new-page',
                'header' => 'A New Page',
                'header_nav_link' => true,
                'footer_nav_link' => false,
                'external' => false
            ]);
    }

    /**
     * Test return list of pages.
     */
    public function testGetListOfPages()
    {
        $page1 = factory(App\Models\Page::class)->create();
        $page2 = factory(App\Models\Page::class)->create();

        $this->get('/api/pages')
            ->seeJson($page1->toArray())
            ->seeJson($page2->toArray());
    }

    /**
     * Test get one specific page.
     */
    public function testGetOnePage()
    {
        $page = factory(App\Models\Page::class)->create();

        $this->get("/api/pages/{$page->id}")
            ->seeJson($page->toArray());
    }

    /**
     * Test only admins can create new pages.
     */
    public function testOnlyAdminCanCreatePage()
    {
        $user = factory(App\Models\User::class)->create();

        $this->actingAs($user)
            ->post('/api/pages', ['nav_title' => 'A New Page'])
            ->assertResponseStatus(403);
    }

    /**
     * Test only admins can update pages.
     */
    public function testOnlyAdminCanUpdatePage()
    {
        $this->markTestIncomplete();
    }

    /**
     * Test only admins can destroy pages.
     */
    public function testOnlyAdminCanDestroyPage()
    {
        $this->markTestIncomplete();
    }
}
